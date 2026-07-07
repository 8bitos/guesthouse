<?php

namespace App\Http\Controllers;

use App\Mail\BookingApproved;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Display the booking page with list of rooms.
     */
    public function index(Request $request): View
    {
        $rooms = Room::where('status', 'tersedia')->get();
        $selectedRoomId = $request->query('room_id');

        return view('pages.booking', compact('rooms', 'selectedRoomId'));
    }

    /**
     * Check room availability for a given date range.
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
        ]);

        $checkIn = $request->query('check_in');
        $checkOut = $request->query('check_out');

        // Find bookings that overlap with this range and are active
        $bookings = Booking::whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->get();

        $bookedRooms = $bookings->groupBy('room_id')->map(function ($roomBookings) {
            $latestBooking = $roomBookings->sortByDesc('check_out')->first();

            return [
                'room_id' => $latestBooking->room_id,
                'check_out' => $latestBooking->check_out,
                'check_out_formatted' => date('d M Y', strtotime($latestBooking->check_out)),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'booked_rooms' => $bookedRooms,
        ]);
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request): JsonResponse
    {
        if (Auth::user() && Auth::user()->role === 'admin') {
            return response()->json([
                'error' => 'You cannot perform checkout because you are logged in as an admin.',
                'message' => 'You cannot perform checkout because you are logged in as an admin.',
            ], 403);
        }

        if (! $request->has('payment_method')) {
            $request->merge(['payment_method' => 'Transfer Bank']);
        }

        // Normalize request for backward compatibility: if "rooms" is not present, build it from single room inputs
        if (! $request->has('rooms')) {
            $rooms = [
                [
                    'room_id' => $request->input('room_id'),
                    'guests' => $request->input('guests') !== null ? (int) $request->input('guests') : null,
                    'include_breakfast' => $request->input('include_breakfast'),
                    'include_extra_bed' => $request->input('include_extra_bed'),
                    'late_checkout' => $request->input('late_checkout'),
                    'subtotal' => $request->input('subtotal') !== null ? (float) $request->input('subtotal') : null,
                    'discount' => $request->input('discount') !== null ? (float) $request->input('discount') : null,
                    'tax' => $request->input('tax') !== null ? (float) $request->input('tax') : null,
                    'total_price' => $request->input('total_price') !== null ? (float) $request->input('total_price') : null,
                ],
            ];
        } else {
            $rooms = $request->input('rooms', []);
        }

        // Preprocess and cast fields
        foreach ($rooms as &$room) {
            $room['include_breakfast'] = isset($room['include_breakfast']) ? filter_var($room['include_breakfast'], FILTER_VALIDATE_BOOLEAN) : false;
            $room['include_extra_bed'] = isset($room['include_extra_bed']) ? filter_var($room['include_extra_bed'], FILTER_VALIDATE_BOOLEAN) : false;
            $room['late_checkout'] = isset($room['late_checkout']) ? filter_var($room['late_checkout'], FILTER_VALIDATE_BOOLEAN) : false;

            // Optional check-in / check-out per room (fallback to global request dates)
            $room['check_in'] = $room['check_in'] ?? $request->input('check_in');
            $room['check_out'] = $room['check_out'] ?? $request->input('check_out');
            $room['nights'] = isset($room['nights']) ? (int) $room['nights'] : (int) $request->input('nights');

            // Default guests count to room capacity if not provided or empty
            if (empty($room['guests'])) {
                $roomObj = Room::find($room['room_id']);
                $room['guests'] = $roomObj ? $roomObj->capacity : 1;
            }

            // Parse addons if sent as JSON string
            if (isset($room['addons']) && is_string($room['addons'])) {
                $room['addons'] = json_decode($room['addons'], true);
            } else {
                $room['addons'] = $room['addons'] ?? [];
            }
        }
        unset($room);

        $request->merge(['rooms' => $rooms]);

        $request->validate([
            'rooms' => ['required', 'array', 'min:1'],
            'rooms.*.room_id' => ['required', 'exists:rooms,id'],
            'rooms.*.guests' => ['required', 'integer', 'min:1'],
            'rooms.*.include_breakfast' => ['boolean'],
            'rooms.*.include_extra_bed' => ['boolean'],
            'rooms.*.late_checkout' => ['boolean'],
            'rooms.*.addons' => ['nullable'],
            'rooms.*.subtotal' => ['required', 'numeric', 'min:0'],
            'rooms.*.discount' => ['required', 'numeric', 'min:0'],
            'rooms.*.tax' => ['required', 'numeric', 'min:0'],
            'rooms.*.total_price' => ['required', 'numeric', 'min:0'],
            'rooms.*.check_in' => ['required', 'date'],
            'rooms.*.check_out' => ['required', 'date'],
            'rooms.*.nights' => ['required', 'integer', 'min:1'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'guest_country' => ['required', 'string', 'max:255'],
            'special_requests' => ['nullable', 'string'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'nights' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'string', 'in:Transfer Bank,Midtrans'],
            'payment_proof' => [
                Rule::requiredIf(fn () => $request->input('payment_method') === 'Transfer Bank'),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        // Validate individual check-out dates are after check-in dates
        foreach ($request->rooms as $roomData) {
            if (strtotime($roomData['check_out']) <= strtotime($roomData['check_in'])) {
                return response()->json(['error' => 'Check-out date must be after check-in date for all rooms.'], 422);
            }
        }

        $roomIds = collect($request->rooms)->pluck('room_id')->toArray();

        // Check for duplicate rooms in payload
        if (count($roomIds) !== count(array_unique($roomIds))) {
            return response()->json(['error' => 'Duplicate rooms selected.'], 422);
        }

        // Check if any selected rooms are already booked for their respective dates
        foreach ($request->rooms as $roomData) {
            $rCheckIn = $roomData['check_in'];
            $rCheckOut = $roomData['check_out'];
            $rRoomId = $roomData['room_id'];

            $roomOverlapping = Booking::whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->where('check_in', '<', $rCheckOut)
                ->where('check_out', '>', $rCheckIn)
                ->where('room_id', $rRoomId)
                ->exists();

            if ($roomOverlapping) {
                return response()->json(['error' => 'One or more selected rooms are not available for the chosen dates.'], 422);
            }
        }

        // Check if rooms exist and are available
        $rooms = Room::whereIn('id', $roomIds)->get();
        if ($rooms->count() !== count($roomIds)) {
            return response()->json(['error' => 'One or more rooms do not exist.'], 422);
        }

        foreach ($rooms as $room) {
            if ($room->status !== 'tersedia') {
                return response()->json(['error' => "Room {$room->name} is not available."], 422);
            }
        }

        // Generate shared invoice number
        $randomId = rand(1000, 9999);
        $now = now();
        $invoiceNo = 'BGH-'.$now->format('Ym').'-'.$randomId;

        // Store payment proof
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('proofs', 'public');
        }

        $parentBooking = null;
        $totalSubtotal = 0;
        $totalDiscount = 0;
        $totalTax = 0;
        $totalPrice = 0;
        $totalGuests = 0;

        $anyBreakfast = false;
        $anyExtraBed = false;
        $anyLateCheckout = false;
        $breakfastCost = 0;
        $extraBedCost = 0;
        $lateCheckoutCost = 0;

        foreach ($request->rooms as $roomData) {
            $isBreakfast = filter_var($roomData['include_breakfast'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $isExtraBed = filter_var($roomData['include_extra_bed'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $isLateCheckout = filter_var($roomData['late_checkout'] ?? false, FILTER_VALIDATE_BOOLEAN);

            $gCount = (int) $roomData['guests'];
            $rNights = (int) $roomData['nights'];
            $rCheckIn = $roomData['check_in'];
            $rCheckOut = $roomData['check_out'];

            if ($isBreakfast) {
                $anyBreakfast = true;
                $breakfastCost += 50000 * $gCount * $rNights;
            }
            if ($isExtraBed) {
                $anyExtraBed = true;
                $extraBedCost += 150000 * $rNights;
            }
            if ($isLateCheckout) {
                $anyLateCheckout = true;
                $lateCheckoutCost += 100000;
            }

            $booking = Booking::create([
                'parent_id' => $parentBooking ? $parentBooking->id : null,
                'invoice_no' => $invoiceNo,
                'user_id' => Auth::id(),
                'room_id' => $roomData['room_id'],
                'guest_name' => $request->guest_name,
                'guest_email' => $request->guest_email,
                'guest_phone' => $request->guest_phone,
                'guest_country' => $request->guest_country,
                'special_requests' => $request->special_requests,
                'include_breakfast' => $isBreakfast,
                'include_extra_bed' => $isExtraBed,
                'late_checkout' => $isLateCheckout,
                'addons' => $roomData['addons'] ?? [],
                'check_in' => $rCheckIn,
                'check_out' => $rCheckOut,
                'nights' => $rNights,
                'guests' => $gCount,
                'subtotal' => $roomData['subtotal'],
                'discount' => $roomData['discount'],
                'tax' => $roomData['tax'],
                'total_price' => $roomData['total_price'],
                'payment_method' => $request->input('payment_method', 'Transfer Bank'),
                'payment_proof' => $proofPath,
                'status' => 'pending',
            ]);

            if (! $parentBooking) {
                $parentBooking = $booking;
            }

            $totalSubtotal += $roomData['subtotal'];
            $totalDiscount += $roomData['discount'];
            $totalTax += $roomData['tax'];
            $totalPrice += $roomData['total_price'];
            $totalGuests += $gCount;
        }

        $roomNames = $rooms->pluck('name')->join(', ');

        $snapToken = null;
        if ($request->input('payment_method') === 'Midtrans') {
            $snapToken = $this->getMidtransSnapToken($parentBooking, $totalPrice);
            $parentBooking->update(['snap_token' => $snapToken]);

            foreach ($parentBooking->childBookings as $child) {
                $child->update(['snap_token' => $snapToken]);
            }
        }

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
            'booking' => [
                'id' => $parentBooking->id,
                'invoice_no' => $parentBooking->invoice_no,
                'date' => $parentBooking->created_at->format('d M Y'),
                'guest_name' => $parentBooking->guest_name,
                'room_name' => $roomNames,
                'check_in' => $parentBooking->check_in,
                'check_out' => $parentBooking->check_out,
                'nights' => $parentBooking->nights,
                'guests' => $totalGuests,
                'include_breakfast' => $anyBreakfast,
                'include_extra_bed' => $anyExtraBed,
                'late_checkout' => $anyLateCheckout,
                'breakfast_cost' => $breakfastCost,
                'extra_bed_cost' => $extraBedCost,
                'late_checkout_cost' => $lateCheckoutCost,
                'subtotal' => (float) $totalSubtotal,
                'discount' => (float) $totalDiscount,
                'tax' => (float) $totalTax,
                'total_price' => (float) $totalPrice,
                'payment_method' => $parentBooking->payment_method,
                'status' => $parentBooking->status,
            ],
        ]);
    }

    /**
     * Generate Midtrans Snap Token for transaction.
     */
    private function getMidtransSnapToken(Booking $booking, float $grossAmount): ?string
    {
        $serverKey = config('services.midtrans.server_key');

        if (empty($serverKey) || strpos($serverKey, 'YOUR_SANDBOX_KEY') !== false) {
            return 'MOCK-SNAP-TOKEN-'.uniqid();
        }

        try {
            $payload = [
                'transaction_details' => [
                    'order_id' => $booking->invoice_no,
                    'gross_amount' => (int) $grossAmount,
                ],
                'credit_card' => [
                    'secure' => config('services.midtrans.is_3ds', true),
                ],
                'customer_details' => [
                    'first_name' => $booking->guest_name,
                    'email' => $booking->guest_email,
                    'phone' => $booking->guest_phone,
                ],
            ];

            $response = Http::withBasicAuth($serverKey, '')
                ->withHeaders(['Accept' => 'application/json', 'Content-Type' => 'application/json'])
                ->post('https://app.sandbox.midtrans.com/snap/v1/transactions', $payload);

            if ($response->successful()) {
                return $response->json('token');
            }

            Log::error('Midtrans API Error: '.$response->body());
        } catch (\Exception $e) {
            Log::error('Midtrans Exception: '.$e->getMessage());
        }

        return 'MOCK-SNAP-TOKEN-'.uniqid();
    }

    /**
     * Handle Midtrans payment notification callback (webhook).
     */
    public function midtransCallback(Request $request): JsonResponse
    {
        $orderId = $request->input('order_id');
        $statusCode = $request->input('status_code');
        $grossAmount = $request->input('gross_amount');
        $transactionStatus = $request->input('transaction_status');
        $signatureKey = $request->input('signature_key');

        $serverKey = config('services.midtrans.server_key');

        $localSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        if (strpos($serverKey, 'YOUR_SANDBOX_KEY') === false && $localSignature !== $signatureKey) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $bookings = Booking::where('invoice_no', $orderId)->get();
        if ($bookings->isEmpty()) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            foreach ($bookings as $booking) {
                $booking->update([
                    'status' => 'confirmed',
                    'midtrans_id' => $request->input('transaction_id'),
                ]);
            }

            $primaryBooking = $bookings->first();
            try {
                Mail::to($primaryBooking->guest_email)
                    ->send(new BookingApproved($primaryBooking));
            } catch (\Exception $e) {
                Log::error('Error sending email on callback: '.$e->getMessage());
            }
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            foreach ($bookings as $booking) {
                $booking->update([
                    'status' => 'rejected',
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Bypass payment verification for testing/simulation (mock bypass).
     */
    public function bypassPayment(Booking $booking): JsonResponse
    {
        if ($booking->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $bookings = Booking::where('invoice_no', $booking->invoice_no)->get();
        foreach ($bookings as $b) {
            $b->update([
                'status' => 'confirmed',
                'midtrans_id' => 'BYPASS-'.strtoupper(uniqid()),
            ]);
        }

        $primaryBooking = $bookings->first();
        try {
            Mail::to($primaryBooking->guest_email)
                ->send(new BookingApproved($primaryBooking));
        } catch (\Exception $e) {
            Log::error('Error sending email on bypass: '.$e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment has been bypassed successfully. Booking is now Confirmed!',
        ]);
    }

    /**
     * Cancel/reject booking if payment was cancelled.
     */
    public function cancelBooking(Booking $booking): JsonResponse
    {
        if ($booking->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $bookings = Booking::where('invoice_no', $booking->invoice_no)->get();
        foreach ($bookings as $b) {
            $b->update([
                'status' => 'rejected',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking has been cancelled.',
        ]);
    }
}
