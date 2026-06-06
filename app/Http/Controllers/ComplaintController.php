<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * Store a newly created complaint in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        Complaint::create([
            'user_id' => auth()->id(),
            'booking_id' => $request->booking_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Your complaint/feedback has been submitted successfully.');
    }
}
