<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;

        // Check if email already exists
        $existing = NewsletterSubscription::where('email', $email)->first();

        if ($existing) {
            if ($existing->status === 'active') {
                return response()->json([
                    'message' => 'You are already subscribed to our newsletter.',
                ], 200);
            } else {
                // Reactivate subscription
                $existing->update([
                    'status' => 'active',
                    'subscribed_at' => now(),
                    'unsubscribed_at' => null,
                ]);

                return response()->json([
                    'message' => 'Thank you for resubscribing! You will receive our latest updates.',
                ], 200);
            }
        }

        // Create new subscription
        NewsletterSubscription::create([
            'email' => $email,
            'status' => 'active',
            'subscribed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Thank you for subscribing! You will receive our latest power tips and updates.',
        ], 201);
    }
}
