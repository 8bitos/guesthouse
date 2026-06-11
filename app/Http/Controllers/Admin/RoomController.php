<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Display a listing of the rooms.
     */
    public function index(): View
    {
        $rooms = Room::latest()->paginate(10);

        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new room.
     */
    public function create(): View
    {
        return view('admin.rooms.create');
    }

    /**
     * Store a newly created room in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:Standard Double Room,Deluxe Double Room,Budget Double Room,Superior King Room'],
            'price' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'size' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:tersedia,dipesan,perbaikan'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'addons' => ['nullable', 'array'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('rooms', 'public');
        }

        $addons = $request->input('addons');
        if ($addons === null) {
            $addons = [];
            if ($request->boolean('allow_breakfast', true)) {
                $addons[] = ['name' => 'Breakfast', 'price' => 50000, 'description' => 'Enable breakfast addon', 'type' => 'per_guest_per_night'];
            }
            if ($request->boolean('allow_extra_bed', true)) {
                $addons[] = ['name' => 'Extra Bed', 'price' => 150000, 'description' => 'Enable extra bed', 'type' => 'per_night'];
            }
            if ($request->boolean('allow_late_checkout', true)) {
                $addons[] = ['name' => 'Late Check-out', 'price' => 100000, 'description' => 'Enable late check-out', 'type' => 'flat_fee'];
            }
        }

        // Determine allow boolean fields based on addons list for backward compatibility with checkbook UI/queries
        $hasBreakfast = collect($addons)->contains(fn ($a) => stripos($a['name'] ?? '', 'breakfast') !== false);
        $hasExtraBed = collect($addons)->contains(fn ($a) => stripos($a['name'] ?? '', 'extra bed') !== false);
        $hasLateCheckout = collect($addons)->contains(fn ($a) => stripos($a['name'] ?? '', 'late check') !== false || stripos($a['name'] ?? '', 'late out') !== false);

        Room::create([
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
            'capacity' => $request->capacity,
            'size' => $request->size,
            'description' => $request->description,
            'status' => $request->status,
            'image' => $imagePath,
            'addons' => $addons,
            'allow_breakfast' => $hasBreakfast,
            'allow_extra_bed' => $hasExtraBed,
            'allow_late_checkout' => $hasLateCheckout,
        ]);

        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully.');
    }

    /**
     * Show the form for editing the specified room.
     */
    public function edit(Room $room): View
    {
        return view('admin.rooms.edit', compact('room'));
    }

    /**
     * Update the specified room in storage.
     */
    public function update(Request $request, Room $room): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:Standard Double Room,Deluxe Double Room,Budget Double Room,Superior King Room'],
            'price' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1'],
            'size' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:tersedia,dipesan,perbaikan'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'addons' => ['nullable', 'array'],
        ]);

        $addons = $request->input('addons');
        if ($addons === null) {
            $addons = [];
            if ($request->boolean('allow_breakfast', true)) {
                $addons[] = ['name' => 'Breakfast', 'price' => 50000, 'description' => 'Enable breakfast addon', 'type' => 'per_guest_per_night'];
            }
            if ($request->boolean('allow_extra_bed', true)) {
                $addons[] = ['name' => 'Extra Bed', 'price' => 150000, 'description' => 'Enable extra bed', 'type' => 'per_night'];
            }
            if ($request->boolean('allow_late_checkout', true)) {
                $addons[] = ['name' => 'Late Check-out', 'price' => 100000, 'description' => 'Enable late check-out', 'type' => 'flat_fee'];
            }
        }

        // Determine allow boolean fields based on addons list for backward compatibility with checkbook UI/queries
        $hasBreakfast = collect($addons)->contains(fn ($a) => stripos($a['name'] ?? '', 'breakfast') !== false);
        $hasExtraBed = collect($addons)->contains(fn ($a) => stripos($a['name'] ?? '', 'extra bed') !== false);
        $hasLateCheckout = collect($addons)->contains(fn ($a) => stripos($a['name'] ?? '', 'late check') !== false || stripos($a['name'] ?? '', 'late out') !== false);

        $roomData = [
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
            'capacity' => $request->capacity,
            'size' => $request->size,
            'description' => $request->description,
            'status' => $request->status,
            'addons' => $addons,
            'allow_breakfast' => $hasBreakfast,
            'allow_extra_bed' => $hasExtraBed,
            'allow_late_checkout' => $hasLateCheckout,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $roomData['image'] = $request->file('image')->store('rooms', 'public');
        }

        $room->update($roomData);

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully.');
    }

    /**
     * Remove the specified room from storage.
     */
    public function destroy(Room $room): RedirectResponse
    {
        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully.');
    }
}
