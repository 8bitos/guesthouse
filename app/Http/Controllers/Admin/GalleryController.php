<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Display listing of gallery photos.
     */
    public function index(): View
    {
        $photos = Gallery::orderBy('order_index')->orderBy('created_at', 'desc')->paginate(12);

        return view('admin.cms.gallery.index', compact('photos'));
    }

    /**
     * Show form for uploading a photo.
     */
    public function create(): View
    {
        return view('admin.cms.gallery.create');
    }

    /**
     * Store uploaded photo in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // Max 5MB
            'caption' => ['nullable', 'string', 'max:255'],
            'order_index' => ['nullable', 'integer'],
        ]);

        $imagePath = $request->file('image')->store('gallery', 'public');

        Gallery::create([
            'image' => $imagePath,
            'caption' => $request->caption,
            'order_index' => $request->order_index ?? 0,
        ]);

        return redirect()->route('admin.cms.gallery.index')->with('success', 'Photo uploaded successfully.');
    }

    /**
     * Delete the photo from storage and database.
     */
    public function destroy(Gallery $gallery): RedirectResponse
    {
        Storage::disk('public')->delete($gallery->image);
        $gallery->delete();

        return redirect()->route('admin.cms.gallery.index')->with('success', 'Photo deleted successfully.');
    }
}
