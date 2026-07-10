<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Mail\BookingApproved;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\Gallery;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/testemail', function () {
    $users = User::orderBy('name')->get();
    $selectedUserId = request('user_id');
    $status = null;
    $error = null;

    if ($selectedUserId) {
        $user = User::find($selectedUserId);
        if ($user) {
            $booking = Booking::first();
            if (! $booking) {
                $room = Room::first() ?: Room::create([
                    'name' => 'Test Deluxe Room',
                    'type' => 'Deluxe Double Room',
                    'price' => 1200000,
                    'capacity' => 2,
                    'status' => 'tersedia',
                ]);
                $booking = Booking::create([
                    'user_id' => $user->id,
                    'room_id' => $room->id,
                    'invoice_no' => 'INV-TEST-999',
                    'guest_name' => $user->name,
                    'guest_email' => $user->email,
                    'guest_phone' => '0812345678',
                    'guest_country' => 'Indonesia',
                    'check_in' => now()->format('Y-m-d'),
                    'check_out' => now()->addDays(2)->format('Y-m-d'),
                    'nights' => 2,
                    'adults' => 2,
                    'children' => 0,
                    'subtotal' => 2400000,
                    'tax' => 240000,
                    'total_price' => 2640000,
                    'status' => 'confirmed',
                ]);
            } else {
                $booking = clone $booking;
                $booking->guest_name = $user->name;
                $booking->guest_email = $user->email;
            }

            try {
                Mail::to($user->email)->send(new BookingApproved($booking));
                $status = 'Test email successfully sent to: '.$user->name.' ('.$user->email.')! Check your Mailtrap inbox.';
            } catch (Throwable $e) {
                $error = 'Failed to send email. Error: '.$e->getMessage().' (in '.$e->getFile().' on line '.$e->getLine().')';
            }
        } else {
            $error = 'Selected user not found.';
        }
    }

    $optionsHtml = '';
    foreach ($users as $u) {
        $selected = $selectedUserId == $u->id ? 'selected' : '';
        $roleBadge = $u->role === 'admin' ? '[Admin]' : '[Pelanggan]';
        $optionsHtml .= "<option value=\"{$u->id}\" {$selected}>{$u->name} - {$u->email} {$roleBadge}</option>";
    }

    $statusMessage = '';
    if ($status) {
        $statusMessage = "<div class=\"alert alert-success\">{$status}</div>";
    } elseif ($error) {
        $statusMessage = "<div class=\"alert alert-danger\">{$error}</div>";
    }

    $noUsersMsg = '';
    if ($users->isEmpty()) {
        $noUsersMsg = "<p style='color: #ef4444; text-align: center;'>No users found in database. Please seed or register first.</p>";
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Email Testing - Bagus Guest House</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        h1 {
            font-size: 28px;
            font-weight: 800;
            margin-top: 0;
            margin-bottom: 8px;
            background: linear-gradient(to right, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.025em;
        }
        p.subtitle {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .form-group {
            text-align: left;
            margin-bottom: 24px;
        }
        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #fbbf24;
            margin-bottom: 8px;
        }
        select {
            width: 100%;
            padding: 14px 18px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #f8fafc;
            font-family: inherit;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
            cursor: pointer;
        }
        select:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.25);
        }
        select option {
            background: #0f172a;
            color: #f8fafc;
        }
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            border: none;
            border-radius: 12px;
            color: #0f172a;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(217, 119, 6, 0.3);
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -3px rgba(217, 119, 6, 0.45);
        }
        button:active {
            transform: translateY(0);
        }
        .alert {
            border-radius: 12px;
            padding: 16px;
            font-size: 14px;
            margin-bottom: 24px;
            text-align: left;
            line-height: 1.5;
            border: 1px solid transparent;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
            color: #34d399;
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            color: #f87171;
            word-break: break-word;
        }
        .back-link {
            display: inline-block;
            margin-top: 24px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s ease;
        }
        .back-link:hover {
            color: #fbbf24;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>SMTP Tester</h1>
        <p class="subtitle">Bagus Guest House - Email Notification Test Portal</p>
        
        {$statusMessage}
        {$noUsersMsg}

        <form action="/testemail" method="GET">
            <div class="form-group">
                <label for="user_id">Select Destination User</label>
                <select name="user_id" id="user_id" required>
                    <option value="" disabled selected>-- Select a registered user --</option>
                    {$optionsHtml}
                </select>
            </div>
            <button type="submit">Send Test Confirmation Email</button>
        </form>
        
        <a href="/admin/dashboard" class="back-link">Back to Admin Dashboard</a>
    </div>
</body>
</html>
HTML;
});

Route::get('/', function () {
    $rooms = Room::where('status', '!=', 'perbaikan')->get();

    // Fetch CMS settings
    $aboutTitle = Setting::getValue('about_title', 'About Us');
    $aboutDesc = Setting::getValue('about_desc', 'Experience luxury hospitality with breathtaking mountain and valley views. Bagus Guest House offers modern facilities, comfortable accommodations, and world-class dining in a serene natural setting.');
    $aboutWhyRaw = Setting::getValue('about_why_list', "Spectacular mountain and valley views\nModern luxury accommodations\nWorld-class dining and cafe\nProfessional and friendly staff");
    $aboutWhyList = array_filter(array_map('trim', explode("\n", $aboutWhyRaw)));
    $aboutVision = Setting::getValue('about_vision', 'To be the most preferred luxury accommodation destination in the region, offering unforgettable experiences and exceptional hospitality.');

    // Fetch facilities
    $facilities = Facility::all();
    if ($facilities->isEmpty()) {
        $facilities = collect([
            (object) ['icon' => '🏔️', 'title' => 'Mountain View', 'description' => 'Breathtaking views of surrounding mountains and Batur'],
            (object) ['icon' => '🏞️', 'title' => 'Valley View', 'description' => 'Scenic valley panoramas and peaceful settings'],
            (object) ['icon' => '🏊', 'title' => 'Swimming Pool', 'description' => 'Mountain-side infinity pool with panoramic views'],
            (object) ['icon' => '🚴', 'title' => 'Activities', 'description' => 'Hiking, trekking, jeep tours and adventures'],
            (object) ['icon' => '🍽️', 'title' => 'Fine Dining', 'description' => 'Restaurant and cafe with local cuisine'],
            (object) ['icon' => '💆', 'title' => 'Spa Services', 'description' => 'Wellness and relaxation treatments'],
        ]);
    }

    // Fetch gallery photos
    $galleryPhotos = Gallery::orderBy('order_index')->orderBy('created_at', 'desc')->take(4)->get();

    // Fetch custom hero background image
    $heroImage = Setting::getValue('hero_image');

    return view('pages.home', compact('rooms', 'aboutTitle', 'aboutDesc', 'aboutWhyList', 'aboutVision', 'facilities', 'galleryPhotos', 'heroImage'));
})->name('home');

Route::get('/rooms', function () {
    $rooms = Room::where('status', '!=', 'perbaikan')->get();

    return view('pages.rooms', compact('rooms'));
})->name('rooms');

Route::get('/gallery', function () {
    $photos = Gallery::orderBy('order_index')->orderBy('created_at', 'desc')->get();

    return view('pages.gallery', compact('photos'));
})->name('gallery');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Guest-only Routes (Login / Register)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated Routes
Route::post('/midtrans/callback', [BookingController::class, 'midtransCallback'])->name('midtrans.callback');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profile Routes (accessible by all authenticated users)
    Route::get('/profile', [DashboardController::class, 'showProfile'])->name('profile.edit');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

    // Booking Routes (accessible by both pelanggan and admin)
    Route::get('/booking', [BookingController::class, 'index'])->name('booking');
    Route::get('/booking/check-availability', [BookingController::class, 'checkAvailability'])->name('booking.check-availability');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::post('/booking/{booking}/bypass-payment', [BookingController::class, 'bypassPayment'])->name('booking.bypass');
    Route::post('/booking/{booking}/cancel', [BookingController::class, 'cancelBooking'])->name('booking.cancel');
    Route::post('/booking/{booking}/confirm-payment', [BookingController::class, 'confirmPayment'])->name('booking.confirm-payment');

    // User (Pelanggan) Routes
    Route::middleware('role:pelanggan')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');
        Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    });

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
        Route::get('/admin/reports/export', [DashboardController::class, 'exportReports'])->name('admin.reports.export');

        Route::post('/admin/bookings/{booking}/approve', [DashboardController::class, 'approveBooking'])->name('admin.bookings.approve');
        Route::post('/admin/bookings/{booking}/reject', [DashboardController::class, 'rejectBooking'])->name('admin.bookings.reject');
        Route::post('/admin/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('admin.bookings.cancel');
        Route::post('/admin/bookings/{booking}/check-in', [AdminBookingController::class, 'checkIn'])->name('admin.bookings.checkin');
        Route::post('/admin/bookings/{booking}/check-out', [AdminBookingController::class, 'checkOut'])->name('admin.bookings.checkout');

        Route::resource('admin/bookings', AdminBookingController::class)->except(['create', 'store', 'show'])->names([
            'index' => 'admin.bookings.index',
            'edit' => 'admin.bookings.edit',
            'update' => 'admin.bookings.update',
            'destroy' => 'admin.bookings.destroy',
        ]);

        Route::resource('admin/users', UserController::class)->except(['create', 'store', 'show'])->names([
            'index' => 'admin.users.index',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]);

        Route::resource('admin/complaints', AdminComplaintController::class)->only(['index', 'show', 'update'])->names([
            'index' => 'admin.complaints.index',
            'show' => 'admin.complaints.show',
            'update' => 'admin.complaints.update',
        ]);

        Route::resource('admin/rooms', RoomController::class)->names([
            'index' => 'admin.rooms.index',
            'create' => 'admin.rooms.create',
            'store' => 'admin.rooms.store',
            'edit' => 'admin.rooms.edit',
            'update' => 'admin.rooms.update',
            'destroy' => 'admin.rooms.destroy',
        ]);

        // CMS Customization
        Route::get('admin/cms/about', [CmsController::class, 'editAbout'])->name('admin.cms.about');
        Route::post('admin/cms/about', [CmsController::class, 'updateAbout']);
        Route::resource('admin/cms/facilities', FacilityController::class)->names('admin.cms.facilities');
        Route::resource('admin/cms/gallery', GalleryController::class)->names('admin.cms.gallery');
    });
});
