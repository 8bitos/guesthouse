<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        // Mock bookings for demonstration
        $mockBookings = [
            [
                'id' => 'RES-10492',
                'room_name' => 'Suite 3',
                'check_in' => '2026-06-12',
                'check_out' => '2026-06-15',
                'price' => 3600000,
                'status' => 'confirmed',
            ],
            [
                'id' => 'RES-10311',
                'room_name' => 'Family Suite 1',
                'check_in' => '2026-05-01',
                'check_out' => '2026-05-03',
                'price' => 1820000,
                'status' => 'completed',
            ],
        ];

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
            'total_rooms' => 8, // from room catalogue
            'active_bookings' => 5,
            'revenue' => 12450000,
        ];

        // Mock recent bookings for admin display
        $recentBookings = [
            [
                'id' => 'RES-10492',
                'guest' => 'Budi Santoso',
                'room' => 'Suite 3',
                'dates' => '12 Jun - 15 Jun 2026',
                'amount' => 3600000,
                'status' => 'confirmed',
                'payment' => 'verified',
            ],
            [
                'id' => 'RES-10493',
                'guest' => 'Siti Aminah',
                'room' => 'Potato Room 1',
                'dates' => '18 Jun - 20 Jun 2026',
                'amount' => 1300000,
                'status' => 'pending',
                'payment' => 'waiting',
            ],
            [
                'id' => 'RES-10494',
                'guest' => 'John Doe',
                'room' => 'Family Suite 2',
                'dates' => '22 Jun - 25 Jun 2026',
                'amount' => 3120000,
                'status' => 'pending',
                'payment' => 'waiting',
            ],
            [
                'id' => 'RES-10491',
                'guest' => 'Dewi Lestari',
                'room' => 'Suite 4',
                'dates' => '01 Jun - 04 Jun 2026',
                'amount' => 2730000,
                'status' => 'completed',
                'payment' => 'verified',
            ],
        ];

        return view('dashboard.admin', compact('user', 'stats', 'recentBookings'));
    }
}
