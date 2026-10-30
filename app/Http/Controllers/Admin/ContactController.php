<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Display the contact management page.
     * If no contact record exists yet, redirect to the create page.
     */
    public function index(): View|RedirectResponse
    {
        $contact = ContactSetting::first();

        if (!$contact) {
            return redirect()->route('admin.contact.create');
        }

        return view('admin.contact.index', compact('contact'));
    }

    /**
     * Show the form for creating the contact details.
     * Once a record exists, this action is locked and redirects to edit/index.
     */
    public function create(): View|RedirectResponse
    {
        $contact = ContactSetting::first();

        if ($contact) {
            return redirect()->route('admin.contact.index')
                ->with('info', 'Contact details already exist. You can update them below.');
        }

        return view('admin.contact.create');
    }

    /**
     * Store newly created contact details.
     * Disallows creation if a record already exists.
     */
    public function store(Request $request): RedirectResponse
    {
        if (ContactSetting::exists()) {
            return redirect()->route('admin.contact.index')
                ->with('info', 'Contact details already exist.');
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string'],
            'working_hours' => ['required', 'string', 'max:255'],
            'google_maps_link' => ['required', 'string'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'instagram_url' => ['nullable', 'url', 'max:500'],
            'twitter_url' => ['nullable', 'url', 'max:500'],
            'youtube_url' => ['nullable', 'url', 'max:500'],
        ]);

        ContactSetting::create($validated);

        return redirect()->route('admin.contact.index')
            ->with('success', 'Business contact details have been successfully created.');
    }

    /**
     * Display the edit form.
     */
    public function edit(): View|RedirectResponse
    {
        return $this->index();
    }

    /**
     * Update the business contact reachability settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $contact = ContactSetting::first();

        if (!$contact) {
            return redirect()->route('admin.contact.create');
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string'],
            'working_hours' => ['required', 'string', 'max:255'],
            'google_maps_link' => ['required', 'string'],
            'facebook_url' => ['nullable', 'url', 'max:500'],
            'instagram_url' => ['nullable', 'url', 'max:500'],
            'twitter_url' => ['nullable', 'url', 'max:500'],
            'youtube_url' => ['nullable', 'url', 'max:500'],
        ]);

        $contact->update($validated);

        return redirect()->route('admin.contact.index')
            ->with('success', 'Business contact details have been updated and synchronized with the public website.');
    }
}
