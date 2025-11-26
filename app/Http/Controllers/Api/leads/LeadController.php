<?php

namespace App\Http\Controllers\Api\leads;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by read status
        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['id', 'name', 'email', 'phone', 'type', 'status', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 10);
        $leads = $query->paginate($perPage);
        
        return response()->json($leads);
    }

    public function show(Lead $lead)
    {
        // Mark as read when viewing
        if (!$lead->is_read) {
            $lead->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
        
        return response()->json($lead->load('assignedUser'));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'status' => 'sometimes|string',
            'is_read' => 'sometimes|boolean',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        // If marking as read, set read_at timestamp
        if (isset($validated['is_read']) && $validated['is_read'] && !$lead->is_read) {
            $validated['read_at'] = now();
        } elseif (isset($validated['is_read']) && !$validated['is_read']) {
            $validated['read_at'] = null;
        }

        $lead->update($validated);
        return response()->json($lead);
    }

    /**
     * Get count of unread leads
     */
    public function unreadCount()
    {
        $count = Lead::where('is_read', false)->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Mark lead as read
     */
    public function markAsRead(Lead $lead)
    {
        $lead->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return response()->json($lead);
    }

    public function exportCsv()
    {
        $leads = Lead::all();
        
        $filename = 'leads_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($leads) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Type', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Status', 'Created At']);
            
            foreach ($leads as $lead) {
                fputcsv($file, [
                    $lead->id,
                    $lead->type,
                    $lead->name,
                    $lead->email,
                    $lead->phone,
                    $lead->subject,
                    $lead->message,
                    $lead->status,
                    $lead->created_at,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get statistics about leads
     */
    public function statistics(Request $request)
    {
        $timeRange = $request->get('time_range', '7d'); // 7d, 30d, 90d, 1y
        $startDate = $this->getStartDateForRange($timeRange);
        
        $totalLeads = Lead::count();
        $unreadLeads = Lead::where('is_read', false)->count();
        $readLeads = Lead::where('is_read', true)->count();
        
        // Leads by status
        $leadsByStatus = Lead::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        
        // Leads by type
        $leadsByType = Lead::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
        
        // Recent activity (last 24 hours)
        $recentLeads = Lead::where('created_at', '>=', now()->subDay())->count();
        $recentUnread = Lead::where('is_read', false)
            ->where('created_at', '>=', now()->subDay())
            ->count();
        
        // Time-series data for trends
        $trendsData = $this->getTrendsData($startDate);

        return response()->json([
            'total' => $totalLeads,
            'unread' => $unreadLeads,
            'read' => $readLeads,
            'by_status' => $leadsByStatus,
            'by_type' => $leadsByType,
            'recent' => [
                'total' => $recentLeads,
                'unread' => $recentUnread,
            ],
            'trends' => $trendsData,
        ]);
    }

    /**
     * Get start date based on time range
     */
    private function getStartDateForRange($timeRange)
    {
        switch ($timeRange) {
            case '30d':
                return now()->subDays(30);
            case '90d':
                return now()->subDays(90);
            case '1y':
                return now()->subYear();
            case '7d':
            default:
                return now()->subDays(7);
        }
    }

    /**
     * Get time-series trends data
     */
    private function getTrendsData($startDate)
    {
        $days = [];
        $total = [];
        $byStatus = [];
        
        $currentDate = \Carbon\Carbon::parse($startDate);
        $endDate = now();
        
        // Initialize status arrays
        $statuses = ['new', 'contacted', 'qualified', 'converted', 'lost'];
        foreach ($statuses as $status) {
            $byStatus[$status] = [];
        }
        
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $days[] = $dateStr;
            
            // Get leads for this day
            $dayStart = $currentDate->copy()->startOfDay();
            $dayEnd = $currentDate->copy()->endOfDay();
            
            $dayTotal = Lead::whereBetween('created_at', [$dayStart, $dayEnd])->count();
            $total[] = $dayTotal;
            
            // Get leads by status for this day
            foreach ($statuses as $status) {
                $count = Lead::where('status', $status)
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();
                $byStatus[$status][] = $count;
            }
            
            $currentDate->addDay();
        }
        
        return [
            'labels' => $days,
            'total' => $total,
            'by_status' => $byStatus,
        ];
    }
}
