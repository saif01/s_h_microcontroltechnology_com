<?php

namespace App\Http\Controllers\Public\pages;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'type' => 'nullable|string|in:contact,quote',
        ]);

        $lead = Lead::create([
            'type' => $validated['type'] ?? 'contact',
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        // Send email notification
        $adminEmail = Setting::get('contact_email', config('mail.from.address'));
        
        // Here you would send the email notification
        // Mail::to($adminEmail)->send(new ContactFormSubmitted($lead));

        return response()->json([
            'message' => 'Thank you for your message. We will get back to you soon.',
            'lead' => $lead,
        ], 201);
    }
}
