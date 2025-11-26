<?php

namespace App\Http\Controllers\Api\career;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Career;
use Illuminate\Http\Request;
use App\Support\MediaPath;

class JobApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = JobApplication::with('career');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('cover_letter', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by career
        if ($request->has('career_id')) {
            $query->where('career_id', $request->career_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['id', 'name', 'email', 'phone', 'status', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 10);
        $applications = $query->paginate($perPage);

        // Transform resume paths to URLs
        $applications->getCollection()->transform(function ($application) {
            if ($application->resume_path) {
                $application->resume_url = MediaPath::url($application->resume_path);
            }
            return $application;
        });
        
        return response()->json($applications);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'career_id' => 'required|exists:careers,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'cover_letter' => 'nullable|string',
            'resume_path' => 'nullable|string',
            'additional_data' => 'nullable|array',
            'status' => 'nullable|string|in:new,reviewed,shortlisted,rejected,hired',
            'notes' => 'nullable|string',
        ]);

        $application = JobApplication::create($validated);
        
        if ($application->resume_path) {
            $application->resume_url = MediaPath::url($application->resume_path);
        }
        
        return response()->json($application, 201);
    }

    public function show($id)
    {
        $application = JobApplication::with('career')->findOrFail($id);
        
        if ($application->resume_path) {
            $application->resume_url = MediaPath::url($application->resume_path);
        }
        
        return response()->json($application);
    }

    public function update(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);
        
        $validated = $request->validate([
            'career_id' => 'sometimes|required|exists:careers,id',
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'cover_letter' => 'nullable|string',
            'resume_path' => 'nullable|string',
            'additional_data' => 'nullable|array',
            'status' => 'nullable|string|in:new,reviewed,shortlisted,rejected,hired',
            'notes' => 'nullable|string',
        ]);

        $application->update($validated);
        
        if ($application->resume_path) {
            $application->resume_url = MediaPath::url($application->resume_path);
        }
        
        return response()->json($application);
    }

    public function destroy($id)
    {
        $application = JobApplication::findOrFail($id);
        
        // Delete resume file if exists
        if ($application->resume_path) {
            $normalizedPath = MediaPath::normalize($application->resume_path);
            $relativePath = $normalizedPath ? ltrim($normalizedPath, '/') : '';
            $fullPath = public_path($relativePath);
            
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        
        $application->delete();
        return response()->json(['message' => 'Job application deleted successfully']);
    }

    /**
     * Get statistics for job applications
     */
    public function statistics()
    {
        $stats = [
            'total' => JobApplication::count(),
            'new' => JobApplication::where('status', 'new')->count(),
            'reviewed' => JobApplication::where('status', 'reviewed')->count(),
            'shortlisted' => JobApplication::where('status', 'shortlisted')->count(),
            'rejected' => JobApplication::where('status', 'rejected')->count(),
            'hired' => JobApplication::where('status', 'hired')->count(),
            'by_career' => JobApplication::selectRaw('career_id, COUNT(*) as count')
                ->groupBy('career_id')
                ->with('career:id,title')
                ->get()
        ];

        return response()->json($stats);
    }
}

