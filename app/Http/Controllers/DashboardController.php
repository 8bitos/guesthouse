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

        // Calculate occupancy & revenue trends based on filter
        $trendFilter = $request->query('trend_filter', '6months');
        $monthlyTrends = [];
        $trendsTitle = 'Occupancy & Revenue Trends (Last 6 Months)';
        $trendsColumnHeader = 'Month';

        if ($trendFilter === 'today') {
            $trendsTitle = 'Occupancy & Revenue Trends (Today)';
            $trendsColumnHeader = 'Room';
            $todayStr = today()->format('Y-m-d');

            $bookings = Booking::whereIn('status', ['confirmed', 'completed'])
                ->whereDate('check_in', $todayStr)
                ->get();

            $rooms = Room::all();
            foreach ($rooms as $room) {
                $roomBookings = $bookings->where('room_id', $room->id);
                $monthlyTrends[] = [
                    'label' => $room->name,
                    'bookings_count' => $roomBookings->count(),
                    'revenue' => (float) $roomBookings->sum('total_price'),
                ];
            }
        } elseif ($trendFilter === '7days') {
            $trendsTitle = 'Occupancy & Revenue Trends (Last 7 Days)';
            $trendsColumnHeader = 'Date';
            $startDate = today()->subDays(6)->format('Y-m-d');

            $bookings = Booking::whereIn('status', ['confirmed', 'completed'])
                ->where('check_in', '>=', $startDate)
                ->get();

            for ($i = 6; $i >= 0; $i--) {
                $date = today()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $dayBookings = $bookings->filter(function ($b) use ($dateStr) {
                    return date('Y-m-d', strtotime($b->check_in)) === $dateStr;
                });

                $monthlyTrends[] = [
                    'label' => $date->format('d M'),
                    'bookings_count' => $dayBookings->count(),
                    'revenue' => (float) $dayBookings->sum('total_price'),
                ];
            }
        } elseif ($trendFilter === '1month') {
            $trendsTitle = 'Occupancy & Revenue Trends (Last 30 Days)';
            $trendsColumnHeader = 'Date';
            $startDate = today()->subDays(29)->format('Y-m-d');

            $bookings = Booking::whereIn('status', ['confirmed', 'completed'])
                ->where('check_in', '>=', $startDate)
                ->get();

            for ($i = 29; $i >= 0; $i--) {
                $date = today()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $dayBookings = $bookings->filter(function ($b) use ($dateStr) {
                    return date('Y-m-d', strtotime($b->check_in)) === $dateStr;
                });

                $monthlyTrends[] = [
                    'label' => $date->format('d M'),
                    'bookings_count' => $dayBookings->count(),
                    'revenue' => (float) $dayBookings->sum('total_price'),
                ];
            }
        } elseif ($trendFilter === '1year') {
            $trendsTitle = 'Occupancy & Revenue Trends (Last 12 Months)';
            $trendsColumnHeader = 'Month';
            $startDate = today()->subMonths(11)->startOfMonth()->format('Y-m-d');

            $bookings = Booking::whereIn('status', ['confirmed', 'completed'])
                ->where('check_in', '>=', $startDate)
                ->get();

            for ($i = 11; $i >= 0; $i--) {
                $month = today()->subMonths($i);
                $monthStr = $month->format('Y-m');
                $monthBookings = $bookings->filter(function ($b) use ($monthStr) {
                    return date('Y-m', strtotime($b->check_in)) === $monthStr;
                });

                $monthlyTrends[] = [
                    'label' => $month->format('M Y'),
                    'bookings_count' => $monthBookings->count(),
                    'revenue' => (float) $monthBookings->sum('total_price'),
                ];
            }
        } else { // default: 6months
            $trendsTitle = 'Occupancy & Revenue Trends (Last 6 Months)';
            $trendsColumnHeader = 'Month';
            $startDate = today()->subMonths(5)->startOfMonth()->format('Y-m-d');

            $bookings = Booking::whereIn('status', ['confirmed', 'completed'])
                ->where('check_in', '>=', $startDate)
                ->get();

            for ($i = 5; $i >= 0; $i--) {
                $month = today()->subMonths($i);
                $monthStr = $month->format('Y-m');
                $monthBookings = $bookings->filter(function ($b) use ($monthStr) {
                    return date('Y-m', strtotime($b->check_in)) === $monthStr;
                });

                $monthlyTrends[] = [
                    'label' => $month->format('M Y'),
                    'bookings_count' => $monthBookings->count(),
                    'revenue' => (float) $monthBookings->sum('total_price'),
                ];
            }
        }

        // Calculate guest origin trends (top 5 origins, database-agnostic)
        $guestOrigins = Booking::whereIn('status', ['confirmed', 'completed'])
            ->pluck('guest_country')
            ->map(function ($country) {
                return trim($country);
            })
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(5)
            ->map(function ($count, $country) {
                return [
                    'country' => $country,
                    'count' => $count,
                ];
            })->values()->toArray();

        return view('dashboard.admin', compact('user', 'stats', 'recentBookings', 'favoriteRooms', 'monthlyTrends', 'trendsTitle', 'trendsColumnHeader', 'trendFilter', 'guestOrigins'));
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
