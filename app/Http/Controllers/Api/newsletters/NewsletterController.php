<?php

namespace App\Http\Controllers\Api\newsletters;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscription::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'subscribed_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['id', 'email', 'status', 'subscribed_at', 'unsubscribed_at', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'subscribed_at';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 10);
        $subscriptions = $query->paginate($perPage);
        
        return response()->json($subscriptions);
    }

    public function show(NewsletterSubscription $newsletterSubscription)
    {
        return response()->json($newsletterSubscription);
    }

    public function update(Request $request, NewsletterSubscription $newsletterSubscription)
    {
        $validated = $request->validate([
            'status' => 'sometimes|string|in:active,unsubscribed',
        ]);

        // If unsubscribing, set unsubscribed_at timestamp
        if (isset($validated['status']) && $validated['status'] === 'unsubscribed' && $newsletterSubscription->status === 'active') {
            $validated['unsubscribed_at'] = now();
        } elseif (isset($validated['status']) && $validated['status'] === 'active' && $newsletterSubscription->status === 'unsubscribed') {
            // If reactivating, clear unsubscribed_at and update subscribed_at
            $validated['unsubscribed_at'] = null;
            $validated['subscribed_at'] = now();
        }

        $newsletterSubscription->update($validated);
        return response()->json($newsletterSubscription);
    }

    public function destroy(NewsletterSubscription $newsletterSubscription)
    {
        $newsletterSubscription->delete();
        return response()->json(['message' => 'Newsletter subscription deleted successfully']);
    }

    public function exportCsv()
    {
        $subscriptions = NewsletterSubscription::all();
        
        $filename = 'newsletter_subscriptions_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($subscriptions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Email', 'Status', 'Subscribed At', 'Unsubscribed At', 'Created At']);
            
            foreach ($subscriptions as $subscription) {
                fputcsv($file, [
                    $subscription->id,
                    $subscription->email,
                    $subscription->status,
                    $subscription->subscribed_at,
                    $subscription->unsubscribed_at,
                    $subscription->created_at,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

