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
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:tersedia,dipesan,perbaikan'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'allow_breakfast' => ['nullable', 'boolean'],
            'allow_extra_bed' => ['nullable', 'boolean'],
            'allow_late_checkout' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('rooms', 'public');
        }

        Room::create([
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'status' => $request->status,
            'image' => $imagePath,
            'allow_breakfast' => $request->boolean('allow_breakfast'),
            'allow_extra_bed' => $request->boolean('allow_extra_bed'),
            'allow_late_checkout' => $request->boolean('allow_late_checkout'),
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
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:tersedia,dipesan,perbaikan'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'allow_breakfast' => ['nullable', 'boolean'],
            'allow_extra_bed' => ['nullable', 'boolean'],
            'allow_late_checkout' => ['nullable', 'boolean'],
        ]);

        $roomData = [
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
            'capacity' => $request->capacity,
            'description' => $request->description,
            'status' => $request->status,
            'allow_breakfast' => $request->boolean('allow_breakfast'),
            'allow_extra_bed' => $request->boolean('allow_extra_bed'),
            'allow_late_checkout' => $request->boolean('allow_late_checkout'),
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
