<?php

namespace App\Http\Controllers;

use App\Mail\BookingApproved;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                    'db_id' => $booking->id,
                ];
            });

        // Load user's complaints
        $complaints = Complaint::with('booking')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.user', compact('user', 'mockBookings', 'complaints'));
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

        // Calculate booking trends (most popular rooms)
        $favoriteRooms = Room::withCount(['bookings' => function ($query) {
            $query->whereIn('status', ['confirmed', 'completed']);
        }])->orderBy('bookings_count', 'desc')->take(5)->get();

        // Calculate monthly occupancy & revenue trends (last 6 months, database-agnostic)
        $activeBookingsForTrends = Booking::whereIn('status', ['confirmed', 'completed'])->get();
        $monthlyTrends = $activeBookingsForTrends->groupBy(function ($booking) {
            return date('Y-m', strtotime($booking->check_in));
        })->map(function ($bookings, $month) {
            return [
                'raw_month' => $month,
                'month' => date('M Y', strtotime($month.'-01')),
                'bookings_count' => $bookings->count(),
                'revenue' => (float) $bookings->sum('total_price'),
            ];
        })->sortBy('raw_month')->take(-6)->values()->toArray();

        return view('dashboard.admin', compact('user', 'stats', 'recentBookings', 'favoriteRooms', 'monthlyTrends'));
    }

    /**
     * Export all reservation reports to CSV.
     */
    public function exportReports(): StreamedResponse
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=reservations_report_'.date('Ymd_His').'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $bookings = Booking::with('room')->orderBy('created_at', 'desc')->get();

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Headers
            fputcsv($file, [
                'Invoice No',
                'Guest Name',
                'Guest Email',
                'Guest Phone',
                'Guest Country',
                'Room Name',
                'Check-In Date',
                'Check-Out Date',
                'Nights',
                'Adults',
                'Children',
                'Breakfast',
                'Extra Bed',
                'Late Check-out',
                'Subtotal (RP)',
                'Discount (RP)',
                'Tax (RP)',
                'Total Price (RP)',
                'Status',
                'Created At',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->invoice_no,
                    $booking->guest_name,
                    $booking->guest_email,
                    $booking->guest_phone,
                    $booking->guest_country,
                    $booking->room ? $booking->room->name : 'Deleted Room',
                    $booking->check_in,
                    $booking->check_out,
                    $booking->nights,
                    $booking->adults,
                    $booking->children,
                    $booking->include_breakfast ? 'Yes' : 'No',
                    $booking->include_extra_bed ? 'Yes' : 'No',
                    $booking->late_checkout ? 'Yes' : 'No',
                    $booking->subtotal,
                    $booking->discount,
                    $booking->tax,
                    $booking->total_price,
                    ucfirst($booking->status),
                    $booking->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Approve the specified booking.
     */
    public function approveBooking(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'confirmed']);

        // Send confirmation email to guest
        Mail::to($booking->guest_email)->send(new BookingApproved($booking));

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
