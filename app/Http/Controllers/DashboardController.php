<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the regular user (pelanggan) dashboard.
     */
    public function user(Request $request): View
    {
        $user = Auth::user();

        // Load real bookings for the logged-in user
        $mockBookings = Booking::with('room')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->invoice_no,
                    'invoice_no' => $booking->invoice_no,
                    'room_name' => $booking->room ? $booking->room->name : 'Deleted Room',
                    'check_in' => $booking->check_in,
                    'check_out' => $booking->check_out,
                    'price' => $booking->total_price,
                    'status' => $booking->status,
                    'date' => $booking->created_at->format('Y-m-d H:i'),
                    'guest_name' => $booking->guest_name,
                    'nights' => $booking->nights,
                    'adults' => $booking->adults,
                    'children' => $booking->children,
                    'include_breakfast' => $booking->include_breakfast,
                    'include_extra_bed' => $booking->include_extra_bed,
                    'late_checkout' => $booking->late_checkout,
                    'payment_method' => $booking->payment_method,
                    'subtotal' => $booking->subtotal,
                    'discount' => $booking->discount,
                    'tax' => $booking->tax,
                    'total_price' => $booking->total_price,
                ];
            });

        return view('dashboard.user', compact('user', 'mockBookings'));
    }

    /**
     * Display the admin dashboard.
     */
    public function admin(Request $request): View
    {
        $user = Auth::user();

        // Gather stats
        $stats = [
            'total_users' => User::count(),
            'total_rooms' => Room::count(),
            'active_bookings' => Booking::where('status', 'confirmed')->count(),
            'revenue' => Booking::where('status', 'confirmed')->sum('total_price'),
        ];

        // Fetch recent bookings for admin display from database
        $recentBookings = Booking::with('room', 'user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'invoice_no' => $booking->invoice_no,
                    'guest' => $booking->guest_name,
                    'room' => $booking->room ? $booking->room->name : 'Deleted Room',
                    'dates' => date('d M', strtotime($booking->check_in)).' - '.date('d M Y', strtotime($booking->check_out)),
                    'amount' => $booking->total_price,
                    'include_breakfast' => $booking->include_breakfast,
                    'include_extra_bed' => $booking->include_extra_bed,
                    'late_checkout' => $booking->late_checkout,
                    'status' => $booking->status,
                    'payment' => $booking->status === 'confirmed' ? 'verified' : 'waiting',
                    'payment_proof' => $booking->payment_proof ? asset('storage/'.$booking->payment_proof) : null,
                ];
            });

        return view('dashboard.admin', compact('user', 'stats', 'recentBookings'));
    }

    /**
     * Approve the specified booking.
     */
    public function approveBooking(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'confirmed']);

        return redirect()->back()->with('success', 'Booking approved successfully.');
    }

    /**
     * Reject the specified booking.
     */
    public function rejectBooking(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Booking rejected successfully.');
    }
}
