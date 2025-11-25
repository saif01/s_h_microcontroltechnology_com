<?php

namespace App\Http\Controllers\Api\about;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;
use App\Support\MediaPath;

class AboutController extends Controller
{
    /**
     * Get the about content (singleton - only one record)
     */
    public function index()
    {
        $about = About::first();
        
        if (!$about) {
            return response()->json(['message' => 'About content not found'], 404);
        }

        return response()->json($this->transformAboutWithImages($about));
    }

    /**
     * Show the about content (same as index for singleton)
     */
    public function show()
    {
        return $this->index();
    }

    /**
     * Create or update about content
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hero' => 'nullable|array',
            'hero.overline' => 'nullable|string|max:255',
            'hero.title' => 'nullable|string|max:255',
            'hero.subtitle' => 'nullable|string',
            'story' => 'nullable|array',
            'story.overline' => 'nullable|string|max:255',
            'story.title' => 'nullable|string|max:255',
            'story.description' => 'nullable|string',
            'story.image' => 'nullable|string',
            'story.stats' => 'nullable|array',
            'story.stats.*.value' => 'nullable|string|max:255',
            'story.stats.*.label' => 'nullable|string|max:255',
            'values_title' => 'nullable|string|max:255',
            'values_subtitle' => 'nullable|string',
            'values' => 'nullable|array',
            'values.*.title' => 'nullable|string|max:255',
            'values.*.description' => 'nullable|string',
            'values.*.icon' => 'nullable|string|max:255',
            'team_overline' => 'nullable|string|max:255',
            'team_title' => 'nullable|string|max:255',
            'team' => 'nullable|array',
            'team.*.name' => 'nullable|string|max:255',
            'team.*.role' => 'nullable|string|max:255',
            'team.*.image' => 'nullable|string',
            'team.*.linkedin' => 'nullable|url',
            'team.*.twitter' => 'nullable|url',
        ]);

        // Normalize image paths
        if (!empty($validated['story']['image'])) {
            $validated['story']['image'] = MediaPath::normalize($validated['story']['image']);
        }

        if (!empty($validated['team'])) {
            foreach ($validated['team'] as &$member) {
                if (!empty($member['image'])) {
                    $member['image'] = MediaPath::normalize($member['image']);
                }
            }
        }

        $about = About::firstOrCreate([], $validated);
        
        if ($about->wasRecentlyCreated) {
            return response()->json($this->transformAboutWithImages($about), 201);
        }

        // If record exists, update it
        $about->update($validated);
        return response()->json($this->transformAboutWithImages($about));
    }

    /**
     * Update about content
     */
    public function update(Request $request)
    {
        $about = About::first();
        
        if (!$about) {
            // If no record exists, create it
            return $this->store($request);
        }

        // Accept both camelCase (from frontend) and snake_case
        $validated = $request->validate([
            'hero' => 'nullable|array',
            'hero.overline' => 'nullable|string|max:255',
            'hero.title' => 'nullable|string|max:255',
            'hero.subtitle' => 'nullable|string',
            'story' => 'nullable|array',
            'story.overline' => 'nullable|string|max:255',
            'story.title' => 'nullable|string|max:255',
            'story.description' => 'nullable|string',
            'story.image' => 'nullable|string',
            'story.stats' => 'nullable|array',
            'story.stats.*.value' => 'nullable|string|max:255',
            'story.stats.*.label' => 'nullable|string|max:255',
            'valuesTitle' => 'nullable|string|max:255',
            'values_title' => 'nullable|string|max:255',
            'valuesSubtitle' => 'nullable|string',
            'values_subtitle' => 'nullable|string',
            'values' => 'nullable|array',
            'values.*.title' => 'nullable|string|max:255',
            'values.*.description' => 'nullable|string',
            'values.*.icon' => 'nullable|string|max:255',
            'teamOverline' => 'nullable|string|max:255',
            'team_overline' => 'nullable|string|max:255',
            'teamTitle' => 'nullable|string|max:255',
            'team_title' => 'nullable|string|max:255',
            'team' => 'nullable|array',
            'team.*.name' => 'nullable|string|max:255',
            'team.*.role' => 'nullable|string|max:255',
            'team.*.image' => 'nullable|string',
            'team.*.linkedin' => 'nullable|url',
            'team.*.twitter' => 'nullable|url',
        ]);

        // Convert camelCase to snake_case for database
        if (isset($validated['valuesTitle'])) {
            $validated['values_title'] = $validated['valuesTitle'];
            unset($validated['valuesTitle']);
        }
        if (isset($validated['valuesSubtitle'])) {
            $validated['values_subtitle'] = $validated['valuesSubtitle'];
            unset($validated['valuesSubtitle']);
        }
        if (isset($validated['teamOverline'])) {
            $validated['team_overline'] = $validated['teamOverline'];
            unset($validated['teamOverline']);
        }
        if (isset($validated['teamTitle'])) {
            $validated['team_title'] = $validated['teamTitle'];
            unset($validated['teamTitle']);
        }

        // Normalize image paths
        if (isset($validated['story']['image']) && !empty($validated['story']['image'])) {
            $validated['story']['image'] = MediaPath::normalize($validated['story']['image']);
        }

        if (!empty($validated['team'])) {
            foreach ($validated['team'] as &$member) {
                if (!empty($member['image'])) {
                    $member['image'] = MediaPath::normalize($member['image']);
                }
            }
        }

        $about->update($validated);
        return response()->json($this->transformAboutWithImages($about));
    }

    /**
     * Transform about data with image URLs and camelCase keys
     */
    private function transformAboutWithImages(About $about): array
    {
        $data = $about->toArray();

        // Transform story image
        if (!empty($data['story']['image'])) {
            $data['story']['image'] = MediaPath::url($data['story']['image']);
        }

        // Transform team member images
        if (!empty($data['team'])) {
            foreach ($data['team'] as &$member) {
                if (!empty($member['image'])) {
                    $member['image'] = MediaPath::url($member['image']);
                }
            }
        }

        // Transform snake_case to camelCase for frontend
        $transformed = [
            'hero' => $data['hero'] ?? null,
            'story' => $data['story'] ?? null,
            'valuesTitle' => $data['values_title'] ?? null,
            'valuesSubtitle' => $data['values_subtitle'] ?? null,
            'values' => $data['values'] ?? null,
            'teamOverline' => $data['team_overline'] ?? null,
            'teamTitle' => $data['team_title'] ?? null,
            'team' => $data['team'] ?? null,
        ];

        return $transformed;
    }
}
