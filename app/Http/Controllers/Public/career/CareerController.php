<?php

namespace App\Http\Controllers\Public\career;

use App\Http\Controllers\Controller;
use App\Models\Career;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Support\MediaPath;

class CareerController extends Controller
{
    public function index(Request $request)
    {
        $query = Career::where('published', true)
            ->select('id', 'title', 'slug', 'department', 'location', 'employment_type', 'description', 'deadline', 'order', 'created_at', 'updated_at');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->has('department')) {
            $query->where('department', $request->department);
        }

        // Filter by location
        if ($request->has('location')) {
            $query->where('location', $request->location);
        }

        // Filter by employment type
        if ($request->has('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // Filter by deadline (only show active careers if deadline is in the future or null)
        if ($request->has('active_only') && $request->active_only) {
            $today = now()->startOfDay()->toDateString();
            $query->where(function ($q) use ($today) {
                $q->whereNull('deadline')
                  ->orWhereDate('deadline', '>=', $today);
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'order');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['order', 'title', 'created_at', 'deadline'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'order';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }
        
        $query->orderBy($sortBy, $sortDirection);
        
        if ($sortBy !== 'title' && $sortBy !== 'order') {
            $query->orderBy('order', 'asc');
        }

        // Get unique departments and locations for filters
        $departments = Career::where('published', true)
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->filter()
            ->values();

        $locations = Career::where('published', true)
            ->whereNotNull('location')
            ->distinct()
            ->pluck('location')
            ->filter()
            ->values();

        $employmentTypes = Career::where('published', true)
            ->whereNotNull('employment_type')
            ->distinct()
            ->pluck('employment_type')
            ->filter()
            ->values();

        // Paginate results
        $perPage = $request->get('per_page', 12);
        $careers = $query->paginate($perPage);

        // Add application count
        $careers->getCollection()->transform(function ($career) {
            $career->applications_count = $career->applications()->count();
            return $career;
        });

        return response()->json([
            'data' => $careers->items(),
            'current_page' => $careers->currentPage(),
            'last_page' => $careers->lastPage(),
            'per_page' => $careers->perPage(),
            'total' => $careers->total(),
            'filters' => [
                'departments' => $departments,
                'locations' => $locations,
                'employment_types' => $employmentTypes
            ]
        ]);
    }

    public function show($slug)
    {
        $career = Career::where('slug', $slug)
            ->where('published', true)
            ->select('id', 'title', 'slug', 'department', 'location', 'employment_type', 'description', 'responsibilities', 'requirements', 'benefits', 'deadline', 'order', 'created_at', 'updated_at')
            ->firstOrFail();

        // Check if deadline has passed - compare dates properly
        $today = now()->startOfDay();
        if ($career->deadline) {
            $deadline = \Carbon\Carbon::parse($career->deadline)->startOfDay();
            $career->is_active = $deadline->greaterThanOrEqualTo($today);
        } else {
            // No deadline means always active
            $career->is_active = true;
        }
        $career->applications_count = $career->applications()->count();

        return response()->json($career);
    }

    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'career_id' => 'required|exists:careers,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'cover_letter' => 'nullable|string',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB max
            'additional_data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if career exists and is published
        $career = Career::where('id', $request->career_id)
            ->where('published', true)
            ->firstOrFail();

        // Check if deadline has passed - compare dates properly
        if ($career->deadline) {
            $today = now()->startOfDay();
            $deadline = \Carbon\Carbon::parse($career->deadline)->startOfDay();
            
            if ($deadline->lessThan($today)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The application deadline for this position has passed.'
                ], 400);
            }
        }

        try {
            $resumePath = null;

            // Handle resume upload
            if ($request->hasFile('resume')) {
                $file = $request->file('resume');
                $extension = $file->getClientOriginalExtension();
                $filename = 'resume-' . time() . '-' . uniqid() . '.' . $extension;
                
                // Create folder if it doesn't exist
                $folder = 'careers/resumes';
                $publicPath = public_path('uploads/' . $folder);
                if (!file_exists($publicPath)) {
                    mkdir($publicPath, 0755, true);
                }
                
                // Move file
                $file->move($publicPath, $filename);
                chmod($publicPath . '/' . $filename, 0644);
                
                // Normalize path
                $resumePath = MediaPath::normalize('uploads/' . $folder . '/' . $filename);
            }

            // Create job application
            $application = JobApplication::create([
                'career_id' => $request->career_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'cover_letter' => $request->cover_letter,
                'resume_path' => $resumePath,
                'additional_data' => $request->additional_data,
                'status' => 'new',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your application has been submitted successfully!',
                'application' => $application
            ], 201);
        } catch (\Exception $e) {
            // Delete uploaded file if application creation failed
            if (isset($resumePath) && $resumePath) {
                $fullPath = public_path($resumePath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}

