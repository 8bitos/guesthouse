<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    /**
     * Display listing of facilities.
     */
    public function index(): View
    {
        $facilities = Facility::latest()->paginate(10);

        return view('admin.cms.facilities.index', compact('facilities'));
    }

    /**
     * Show form for creating a new facility.
     */
    public function create(): View
    {
        return view('admin.cms.facilities.create');
    }

    /**
     * Store a newly created facility.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'icon' => ['required', 'string', 'max:50'], // e.g. 🏊, 🏔️, 🍽️
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        Facility::create([
            'icon' => $request->icon,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.cms.facilities.index')->with('success', 'Facility created successfully.');
    }

    /**
     * Show form for editing the facility.
     */
    public function edit(Facility $facility): View
    {
        return view('admin.cms.facilities.edit', compact('facility'));
    }

    /**
     * Update the facility.
     */
    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $request->validate([
            'icon' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $facility->update([
            'icon' => $request->icon,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.cms.facilities.index')->with('success', 'Facility updated successfully.');
    }

    /**
     * Delete the facility.
     */
    public function destroy(Facility $facility): RedirectResponse
    {
        $facility->delete();

        return redirect()->route('admin.cms.facilities.index')->with('success', 'Facility deleted successfully.');
    }
}
