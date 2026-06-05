<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CmsController extends Controller
{
    /**
     * Show form for editing About Us content.
     */
    public function editAbout(): View
    {
        $aboutTitle = Setting::getValue('about_title', 'About Us');
        $aboutDesc = Setting::getValue('about_desc', 'Experience luxury hospitality with breathtaking mountain and valley views...');
        $aboutWhyList = Setting::getValue('about_why_list', "Spectacular mountain and valley views\nModern luxury accommodations\nWorld-class dining and cafe\nProfessional and friendly staff");
        $aboutVision = Setting::getValue('about_vision', 'To be the most preferred luxury accommodation destination in the region, offering unforgettable experiences and exceptional hospitality.');

        return view('admin.cms.about', compact('aboutTitle', 'aboutDesc', 'aboutWhyList', 'aboutVision'));
    }

    /**
     * Update About Us content.
     */
    public function updateAbout(Request $request): RedirectResponse
    {
        $request->validate([
            'about_title' => ['required', 'string', 'max:255'],
            'about_desc' => ['required', 'string'],
            'about_why_list' => ['required', 'string'],
            'about_vision' => ['required', 'string'],
        ]);

        Setting::setValue('about_title', $request->about_title);
        Setting::setValue('about_desc', $request->about_desc);
        Setting::setValue('about_why_list', $request->about_why_list);
        Setting::setValue('about_vision', $request->about_vision);

        return redirect()->back()->with('success', 'About Us content updated successfully.');
    }
}
