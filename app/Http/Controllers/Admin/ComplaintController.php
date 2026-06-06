<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the complaints.
     */
    public function index(): View
    {
        $complaints = Complaint::with(['user', 'booking'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.complaints.index', compact('complaints'));
    }

    /**
     * Display the specified complaint.
     */
    public function show(string $id): View
    {
        $complaint = Complaint::with(['user', 'booking.room'])->findOrFail($id);

        return view('admin.complaints.show', compact('complaint'));
    }

    /**
     * Update the specified complaint.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $complaint = Complaint::findOrFail($id);

        $request->validate([
            'resolution' => ['required', 'string'],
            'status' => ['required', 'in:pending,resolved'],
        ]);

        $complaint->update([
            'resolution' => $request->resolution,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.complaints.index')->with('success', 'Complaint resolution saved successfully.');
    }
}
