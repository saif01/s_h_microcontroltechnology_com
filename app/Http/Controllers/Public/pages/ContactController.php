<?php

namespace App\Http\Controllers\Public\pages;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Setting;
use App\Services\TelegramNotify;
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

        // Send Telegram notification
        $telegramMessage = $this->formatTelegramMessage($lead);
        TelegramNotify::T_NOTIFY($telegramMessage);

        // Send email notification
        // $adminEmail = Setting::get('contact_email', config('mail.from.address'));
        
        // Here you would send the email notification
        // Mail::to($adminEmail)->send(new ContactFormSubmitted($lead));

        return response()->json([
            'message' => 'Thank you for your message. We will get back to you soon.',
            'lead' => $lead,
        ], 201);
    }

    /**
     * Format lead data for Telegram message
     *
     * @param Lead $lead
     * @return string
     */
    private function formatTelegramMessage(Lead $lead): string
    {
        $typeLabel = ucfirst($lead->type ?? 'contact');
        $emoji = $lead->type === 'quote' ? '💼' : '📧';

        $message = "{$emoji} <b>New {$typeLabel} Lead Received</b>\n\n";
        $message .= "👤 <b>Name:</b> {$lead->name}\n";
        
        if ($lead->email) {
            $message .= "📧 <b>Email:</b> {$lead->email}\n";
        }
        
        $message .= "📱 <b>Phone:</b> {$lead->phone}\n";
        
        if ($lead->subject) {
            $message .= "📌 <b>Subject:</b> {$lead->subject}\n";
        }
        
        $message .= "\n💬 <b>Message:</b>\n";
        $message .= htmlspecialchars($lead->message, ENT_QUOTES, 'UTF-8');
        
        $message .= "\n\n🆔 <b>Lead ID:</b> #{$lead->id}";
        $message .= "\n📅 <b>Date:</b> " . $lead->created_at->format('Y-m-d H:i:s');

        return $message;
    }
}
