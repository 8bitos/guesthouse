<?php

use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Models\Facility;
use App\Models\Gallery;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    $rooms = Room::where('status', 'tersedia')->take(6)->get();

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
    $galleryPhotos = Gallery::orderBy('order_index')->orderBy('created_at', 'desc')->take(8)->get();

    return view('pages.home', compact('rooms', 'aboutTitle', 'aboutDesc', 'aboutWhyList', 'aboutVision', 'facilities', 'galleryPhotos'));
})->name('home');

Route::get('/rooms', function () {
    $rooms = Room::all();

    return view('pages.rooms', compact('rooms'));
})->name('rooms');

Route::get('/gallery', function () {
    $photos = Gallery::orderBy('order_index')->orderBy('created_at', 'desc')->get();

    return view('pages.gallery', compact('photos'));
})->name('gallery');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

// Guest-only Routes (Login / Register)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // User (Pelanggan) Routes
    Route::middleware('role:pelanggan')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');
        Route::get('/booking', function (Request $request) {
            $rooms = Room::where('status', 'tersedia')->get();
            $selectedRoomId = $request->query('room_id');

            return view('pages.booking', compact('rooms', 'selectedRoomId'));
        })->name('booking');
    });

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
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
