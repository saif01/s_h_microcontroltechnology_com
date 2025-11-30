<?php

namespace App\Http\Controllers\Public\announcements;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Support\MediaPath;

class AnnouncementController extends Controller
{
    /**
     * Get active announcements for public display
     */
    public function index(Request $request)
    {
        $query = Announcement::active()
            ->select('id', 'title', 'slug', 'short_description', 'content', 'type', 'display_type', 'image', 'video', 'external_link', 'open_in_new_tab', 'priority', 'start_date', 'end_date', 'created_at');

        // Filter by type if provided
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by display type if provided
        if ($request->has('display_type')) {
            $query->where('display_type', $request->display_type);
        }

        // Filter by language if provided
        if ($request->has('language')) {
            $query->where('language', $request->language);
        }

        // Sort by priority (lower number = higher priority) then by created date
        $query->orderBy('priority', 'asc')
            ->orderBy('created_at', 'desc');

        // Support pagination
        if ($request->has('per_page') || $request->has('page')) {
            $perPage = (int) $request->get('per_page', 10);
            $perPage = max(1, min($perPage, 50)); // Limit between 1 and 50
            
            $announcements = $query->paginate($perPage);
            
            $announcements->getCollection()->transform(function ($announcement) {
                return $this->transformAnnouncement($announcement);
            });
            
            return response()->json($announcements);
        }

        // Default: return all active announcements
        $announcements = $query->get();
        
        $announcements->transform(function ($announcement) {
            return $this->transformAnnouncement($announcement);
        });
        
        return response()->json($announcements);
    }

    /**
     * Get a single announcement by slug
     */
    public function show($slug)
    {
        $announcement = Announcement::where('slug', $slug)
            ->active()
            ->firstOrFail();
        
        return response()->json($this->transformAnnouncement($announcement));
    }

    /**
     * Transform announcement with resolved image URLs
     */
    private function transformAnnouncement(Announcement $announcement)
    {
        // Transform image URL if present (handle null, empty string, or valid path)
        $imagePath = trim($announcement->image ?? '');
        if (!empty($imagePath)) {
            $resolvedUrl = MediaPath::url($imagePath);
            $announcement->image = $resolvedUrl ?: null;
        } else {
            $announcement->image = null;
        }

        // Transform video URL if present (handle null, empty string, or valid path)
        $videoPath = trim($announcement->video ?? '');
        if (!empty($videoPath)) {
            $resolvedUrl = MediaPath::url($videoPath);
            $announcement->video = $resolvedUrl ?: null;
        } else {
            $announcement->video = null;
        }

        // Convert dates to ISO strings for frontend
        if ($announcement->start_date) {
            $announcement->start_date = $announcement->start_date->toISOString();
        }
        if ($announcement->end_date) {
            $announcement->end_date = $announcement->end_date->toISOString();
        }
        if ($announcement->created_at) {
            $announcement->created_at = $announcement->created_at->toISOString();
        }

        return $announcement;
    }
}

