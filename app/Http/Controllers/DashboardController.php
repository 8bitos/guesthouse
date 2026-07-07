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
use Illuminate\Support\Facades\Hash;
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

        // Load parent bookings with rooms and child bookings (for multi-room grouping)
        $bookings = Booking::with(['room', 'childBookings.room'])
            ->where('user_id', $user->id)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $mockBookings = $bookings->map(function ($booking) {
            // Roll up information from children
            $allRooms = collect([$booking->room])->concat($booking->childBookings->pluck('room'))->filter()->unique('id');
            $roomNames = $allRooms->pluck('name')->join(', ');

            $totalGuests = $booking->guests + $booking->childBookings->sum('guests');
            $totalPrice = $booking->total_price + $booking->childBookings->sum('total_price');
            $subtotal = $booking->subtotal + $booking->childBookings->sum('subtotal');
            $discount = $booking->discount + $booking->childBookings->sum('discount');
            $tax = $booking->tax + $booking->childBookings->sum('tax');

            $anyBreakfast = $booking->include_breakfast || $booking->childBookings->contains('include_breakfast', true);
            $anyExtraBed = $booking->include_extra_bed || $booking->childBookings->contains('include_extra_bed', true);
            $anyLateCheckout = $booking->late_checkout || $booking->childBookings->contains('late_checkout', true);

            // Compute actual costs to pass to JS
            $breakfastCost = 0;
            $extraBedCost = 0;
            $lateCheckoutCost = 0;
            $otherAddonsCost = 0;

            $allBookings = collect([$booking])->concat($booking->childBookings);
            foreach ($allBookings as $b) {
                if ($b->addons && is_array($b->addons) && count($b->addons) > 0) {
                    $roomAddons = ($b->room && is_array($b->room->addons)) ? $b->room->addons : [];
                    foreach ($b->addons as $addonName) {
                        $config = collect($roomAddons)->first(fn ($a) => ($a['name'] ?? '') === $addonName);
                        if ($config) {
                            $price = (float) ($config['price'] ?? 0);
                            $type = $config['type'] ?? 'flat_fee';
                            $cost = 0;
                            if ($type === 'per_guest_per_night') {
                                $cost = $price * $b->guests * $b->nights;
                            } elseif ($type === 'per_night') {
                                $cost = $price * $b->nights;
                            } else {
                                $cost = $price;
                            }

                            $lowerName = strtolower($addonName);
                            if (strpos($lowerName, 'breakfast') !== false || strpos($lowerName, 'sarapan') !== false) {
                                $breakfastCost += $cost;
                            } elseif (strpos($lowerName, 'extra bed') !== false || strpos($lowerName, 'kasur') !== false) {
                                $extraBedCost += $cost;
                            } elseif (strpos($lowerName, 'late check') !== false || strpos($lowerName, 'late out') !== false) {
                                $lateCheckoutCost += $cost;
                            } else {
                                $otherAddonsCost += $cost;
                            }
                        } else {
                            $lowerName = strtolower($addonName);
                            if (strpos($lowerName, 'breakfast') !== false || strpos($lowerName, 'sarapan') !== false) {
                                $breakfastCost += 50000 * $b->guests * $b->nights;
                            } elseif (strpos($lowerName, 'extra bed') !== false || strpos($lowerName, 'kasur') !== false) {
                                $extraBedCost += 150000 * $b->nights;
                            } elseif (strpos($lowerName, 'late check') !== false || strpos($lowerName, 'late out') !== false) {
                                $lateCheckoutCost += 100000;
                            }
                        }
                    }
                } else {
                    if ($b->include_breakfast) {
                        $breakfastCost += 50000 * $b->guests * $b->nights;
                    }
                    if ($b->include_extra_bed) {
                        $extraBedCost += 150000 * $b->nights;
                    }
                    if ($b->late_checkout) {
                        $lateCheckoutCost += 100000;
                    }
                }
            }

            return [
                'id' => $booking->invoice_no,
                'invoice_no' => $booking->invoice_no,
                'room_name' => $roomNames ?: 'Deleted Room',
                'check_in' => $booking->check_in,
                'check_out' => $booking->check_out,
                'price' => $totalPrice,
                'status' => $booking->status,
                'date' => $booking->created_at->format('Y-m-d H:i'),
                'guest_name' => $booking->guest_name,
                'nights' => $booking->nights,
                'guests' => $totalGuests,
                'include_breakfast' => $anyBreakfast,
                'include_extra_bed' => $anyExtraBed,
                'late_checkout' => $anyLateCheckout,
                'breakfast_cost' => $breakfastCost,
                'extra_bed_cost' => $extraBedCost,
                'late_checkout_cost' => $lateCheckoutCost,
                'other_addons_cost' => $otherAddonsCost,
                'payment_method' => $booking->payment_method,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_price' => $totalPrice,
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
            'active_bookings' => Booking::where('status', 'checked_in')->count(),
            'revenue' => Booking::whereIn('status', ['checked_in', 'completed'])->sum('total_price'),
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
                    'room_status' => $booking->room ? $booking->room->status : 'tersedia',
                    'dates' => date('d M', strtotime($booking->check_in)).' - '.date('d M Y', strtotime($booking->check_out)),
                    'amount' => $booking->total_price,
                    'include_breakfast' => $booking->include_breakfast,
                    'include_extra_bed' => $booking->include_extra_bed,
                    'late_checkout' => $booking->late_checkout,
                    'addons' => $booking->addons,
                    'status' => $booking->status,
                    'payment' => in_array($booking->status, ['confirmed', 'checked_in', 'completed']) ? 'verified' : 'waiting',
                    'payment_proof' => $booking->payment_proof ? asset('storage/'.$booking->payment_proof) : null,
                ];
            });

        // Calculate booking trends (most popular rooms)
        $favoriteRooms = Room::withCount(['bookings' => function ($query) {
            $query->whereIn('status', ['checked_in', 'completed']);
        }])->orderBy('bookings_count', 'desc')->take(5)->get();

        // Calculate occupancy & revenue trends based on filter
        $trendFilter = $request->query('trend_filter', '6months');
        $monthlyTrends = [];
        $trendsTitle = 'Occupancy & Revenue Trends (Last 6 Months)';
        $trendsColumnHeader = 'Month';

        if ($trendFilter === 'today') {
            $trendsTitle = 'Occupancy & Revenue Trends (Today)';
            $trendsColumnHeader = 'Hour';
            $todayStr = today()->format('Y-m-d');

            $bookings = Booking::whereIn('status', ['checked_in', 'completed'])
                ->whereDate('created_at', $todayStr)
                ->get();

            for ($hour = 0; $hour < 24; $hour += 4) {
                $startHour = sprintf('%02d:00', $hour);
                $hourBookings = $bookings->filter(function ($b) use ($hour) {
                    return $b->created_at->hour >= $hour && $b->created_at->hour < ($hour + 4);
                });

                $monthlyTrends[] = [
                    'label' => $startHour,
                    'bookings_count' => $hourBookings->count(),
                    'revenue' => (float) $hourBookings->sum('total_price'),
                ];
            }
        } elseif ($trendFilter === '7days') {
            $trendsTitle = 'Occupancy & Revenue Trends (Last 7 Days)';
            $trendsColumnHeader = 'Date';
            $startDate = today()->subDays(6)->format('Y-m-d');

            $bookings = Booking::whereIn('status', ['checked_in', 'completed'])
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

            $bookings = Booking::whereIn('status', ['checked_in', 'completed'])
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

            $bookings = Booking::whereIn('status', ['checked_in', 'completed'])
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

            $bookings = Booking::whereIn('status', ['checked_in', 'completed'])
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
        $guestOrigins = Booking::whereIn('status', ['checked_in', 'completed'])
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

        $rooms = Room::with(['bookings' => function ($query) {
            $query->whereIn('status', ['checked_in', 'confirmed']);
        }])->orderBy('name')->get();

        return view('dashboard.admin', compact('user', 'stats', 'recentBookings', 'favoriteRooms', 'monthlyTrends', 'trendsTitle', 'trendsColumnHeader', 'trendFilter', 'guestOrigins', 'rooms'));
    }

    /**
     * Export reservation reports to XLS.
     */
    public function exportReports(Request $request): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=reservations_report_'.date('Ymd_His').'.xls',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $query = Booking::with('room');

        // Apply filters
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('room_id') && $request->room_id !== 'all') {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('date_type') && $request->filled('start_date')) {
            $field = $request->date_type === 'created_at' ? 'created_at' : $request->date_type;
            if ($field === 'created_at') {
                $query->whereDate($field, '>=', $request->start_date);
            } else {
                $query->where($field, '>=', $request->start_date);
            }
        }

        if ($request->filled('date_type') && $request->filled('end_date')) {
            $field = $request->date_type === 'created_at' ? 'created_at' : $request->date_type;
            if ($field === 'created_at') {
                $query->whereDate($field, '<=', $request->end_date);
            } else {
                $query->where($field, '<=', $request->end_date);
            }
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $callback = function () use ($bookings) {
            $html = view('exports.bookings_xls', compact('bookings'))->render();
            echo $html;
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Approve the specified booking.
     */
    public function approveBooking(Booking $booking): RedirectResponse
    {
        if ($booking->parent_id) {
            $booking = $booking->parentBooking;
        }

        $booking->update(['status' => 'confirmed']);
        $booking->childBookings()->update(['status' => 'confirmed']);

        // Send confirmation email to guest
        Mail::to($booking->guest_email)->send(new BookingApproved($booking));

        return redirect()->back()->with('success', 'Booking approved successfully.');
    }

    /**
     * Reject the specified booking.
     */
    public function rejectBooking(Booking $booking): RedirectResponse
    {
        if ($booking->parent_id) {
            $booking = $booking->parentBooking;
        }

        $booking->update(['status' => 'rejected']);
        $booking->childBookings()->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Booking rejected successfully.');
    }

    /**
     * Show the user's profile settings.
     */
    public function showProfile(): View
    {
        $user = Auth::user();

        // Parse phone number
        $countryCode = '+62';
        $phoneNumber = $user->phone;

        if ($user->phone) {
            $prefixes = [
                '+1684', '+1264', '+1268', '+1242', '+1246', '+1441', '+1767', '+1849', '+1473', '+1671',
                '+1876', '+1664', '+1670', '+1939', '+1869', '+1758', '+1784', '+1868', '+1649', '+1284',
                '+1340', '+358', '+355', '+213', '+376', '+244', '+672', '+374', '+297', '+994',
                '+973', '+880', '+375', '+501', '+229', '+975', '+591', '+387', '+267', '+246',
                '+673', '+359', '+226', '+257', '+855', '+237', '+238', '+345', '+236', '+235',
                '+269', '+242', '+243', '+682', '+506', '+225', '+385', '+357', '+420', '+253',
                '+593', '+503', '+240', '+291', '+372', '+251', '+500', '+298', '+679', '+594',
                '+689', '+241', '+220', '+995', '+233', '+350', '+299', '+590', '+502', '+224',
                '+245', '+595', '+509', '+379', '+504', '+852', '+354', '+964', '+353', '+972',
                '+962', '+254', '+686', '+850', '+965', '+996', '+856', '+371', '+961', '+266',
                '+231', '+218', '+423', '+370', '+352', '+853', '+389', '+261', '+265', '+960',
                '+223', '+356', '+692', '+596', '+222', '+230', '+262', '+691', '+373', '+377',
                '+976', '+382', '+212', '+258', '+264', '+674', '+977', '+599', '+687', '+505',
                '+227', '+234', '+683', '+968', '+680', '+970', '+507', '+675', '+872', '+351',
                '+974', '+250', '+290', '+508', '+685', '+378', '+239', '+966', '+221', '+381',
                '+248', '+232', '+421', '+386', '+677', '+252', '+211', '+249', '+597', '+268',
                '+963', '+886', '+992', '+255', '+670', '+228', '+690', '+676', '+216', '+993',
                '+688', '+256', '+380', '+971', '+598', '+998', '+678', '+681', '+967', '+260',
                '+263', '+93', '+54', '+61', '+43', '+32', '+55', '+56', '+86', '+57',
                '+53', '+45', '+20', '+33', '+49', '+30', '+44', '+36', '+91', '+62',
                '+98', '+39', '+81', '+77', '+82', '+60', '+52', '+95', '+31', '+64',
                '+47', '+92', '+51', '+63', '+48', '+40', '+65', '+27', '+34', '+94',
                '+46', '+41', '+66', '+90', '+58', '+84', '+1', '+7',
            ];
            foreach ($prefixes as $prefix) {
                if (str_starts_with($user->phone, $prefix)) {
                    $countryCode = $prefix;
                    $phoneNumber = substr($user->phone, strlen($prefix));
                    break;
                }
            }
        }

        return view('pages.profile', compact('user', 'countryCode', 'phoneNumber'));
    }

    /**
     * Update the user's profile settings.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:10'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $userData = [
            'name' => $request->name,
            'phone' => $request->phone ? $request->country_code.ltrim($request->phone, '0') : null,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'current_password' => ['required', 'string'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            if (! Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'The provided current password does not match your record.']);
            }

            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        return redirect()->back()->with('success', 'Profile settings updated successfully.');
    }
}
