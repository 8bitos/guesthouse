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
     * Store a newly created booking in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'guest_country' => ['required', 'string', 'max:255'],
            'special_requests' => ['nullable', 'string'],
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
