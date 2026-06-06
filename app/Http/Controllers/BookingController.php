<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $bookings = Booking::whereIn('status', ['pending', 'confirmed'])
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

        $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'guest_country' => ['required', 'string', 'max:255'],
            'special_requests' => ['nullable', 'string'],
            'include_breakfast' => ['nullable', 'in:0,1,true,false'],
            'include_extra_bed' => ['nullable', 'in:0,1,true,false'],
            'late_checkout' => ['nullable', 'in:0,1,true,false'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'nights' => ['required', 'integer', 'min:1'],
            'adults' => ['required', 'integer', 'min:1'],
            'children' => ['required', 'integer', 'min:0'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $room = Room::findOrFail($request->room_id);
        if ($room->status !== 'tersedia') {
            return response()->json(['error' => 'Room is not available.'], 422);
        }

        // Generate invoice number
        $randomId = rand(1000, 9999);
        $now = now();
        $invoiceNo = 'BGH-'.$now->format('Ym').'-'.$randomId;

        // Store payment proof
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('proofs', 'public');
        }

        $booking = Booking::create([
            'invoice_no' => $invoiceNo,
            'user_id' => Auth::id(),
            'room_id' => $request->room_id,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'guest_country' => $request->guest_country,
            'special_requests' => $request->special_requests,
            'include_breakfast' => (bool) $request->input('include_breakfast', false),
            'include_extra_bed' => (bool) $request->input('include_extra_bed', false),
            'late_checkout' => (bool) $request->input('late_checkout', false),
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'nights' => $request->nights,
            'adults' => $request->adults,
            'children' => $request->children,
            'subtotal' => $request->subtotal,
            'discount' => $request->discount,
            'tax' => $request->tax,
            'total_price' => $request->total_price,
            'payment_method' => 'Transfer Bank',
            'payment_proof' => $proofPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'invoice_no' => $booking->invoice_no,
                'date' => $booking->created_at->format('d M Y'),
                'guest_name' => $booking->guest_name,
                'room_name' => $room->name,
                'check_in' => $booking->check_in,
                'check_out' => $booking->check_out,
                'nights' => $booking->nights,
                'adults' => $booking->adults,
                'children' => $booking->children,
                'include_breakfast' => $booking->include_breakfast,
                'include_extra_bed' => $booking->include_extra_bed,
                'late_checkout' => $booking->late_checkout,
                'subtotal' => (float) $booking->subtotal,
                'discount' => (float) $booking->discount,
                'tax' => (float) $booking->tax,
                'total_price' => (float) $booking->total_price,
                'payment_method' => $booking->payment_method,
                'status' => $booking->status,
            ],
        ]);
    }
}
