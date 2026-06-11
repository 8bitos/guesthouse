<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Booking::with(['room', 'user'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('check_in', '<=', $request->date)
                ->whereDate('check_out', '>=', $request->date);
        }

        $bookings = $query->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        $booking = Booking::with('room')->findOrFail($id);
        $rooms = Room::where('status', 'tersedia')->get();

        return view('admin.bookings.edit', compact('booking', 'rooms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:20'],
            'guest_country' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending,confirmed,completed,cancelled,rejected'],
            'special_requests' => ['nullable', 'string'],
        ]);

        $parent = $booking->parent_id ? $booking->parentBooking : $booking;

        $updateData = [
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'guest_country' => $request->guest_country,
            'status' => $request->status,
            'special_requests' => $request->special_requests,
        ];

        $parent->update($updateData);
        $parent->childBookings()->update($updateData);

        // Sync room availability if booking is finalized/cancelled/rejected
        if (in_array($request->status, ['completed', 'cancelled', 'rejected'])) {
            if ($parent->room) {
                $parent->room->update(['status' => 'tersedia']);
            }
            foreach ($parent->childBookings as $child) {
                if ($child->room) {
                    $child->room->update(['status' => 'tersedia']);
                }
            }
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Reservation details updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $booking = Booking::findOrFail($id);
        $parent = $booking->parent_id ? $booking->parentBooking : $booking;
        $parent->delete();

        return redirect()->route('admin.bookings.index')->with('success', 'Reservation deleted successfully.');
    }

    /**
     * Cancel the specified booking.
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        $parent = $booking->parent_id ? $booking->parentBooking : $booking;
        $parent->update(['status' => 'cancelled']);
        $parent->childBookings()->update(['status' => 'cancelled']);

        // Set rooms to tersedia
        if ($parent->room) {
            $parent->room->update(['status' => 'tersedia']);
        }
        foreach ($parent->childBookings as $child) {
            if ($child->room) {
                $child->room->update(['status' => 'tersedia']);
            }
        }

        return redirect()->back()->with('success', 'Reservation has been cancelled successfully.');
    }

    /**
     * Check in the guest.
     */
    public function checkIn(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'confirmed']);
        if ($booking->room) {
            $booking->room->update(['status' => 'dipesan']);
        }

        return redirect()->back()->with('success', 'Guest checked in successfully. Room is now Occupied.');
    }

    /**
     * Check out the guest.
     */
    public function checkOut(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'completed']);
        if ($booking->room) {
            $booking->room->update(['status' => 'tersedia']);
        }

        return redirect()->back()->with('success', 'Guest checked out successfully. Room is now Vacant.');
    }
}
