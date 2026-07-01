<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendContactRequest;
use App\Mail\ContactSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Send contact message.
     */
    public function send(SendContactRequest $request): RedirectResponse
    {
        Mail::to('bagusguesthouse01@gmail.com')->send(new ContactSubmitted($request->validated()));

        return redirect()->back()->with('success', 'Thank you! Your message has been sent successfully.');
    }
}
