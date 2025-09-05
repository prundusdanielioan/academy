<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoProgress;
use App\Services\HlsTranscoderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VideoController extends Controller
{
    protected $transcoderService;

    public function __construct(HlsTranscoderService $transcoderService)
    {
        $this->transcoderService = $transcoderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            // Admins see all videos
            $videos = Video::with('progress', 'user')
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        } else {
            // Regular users see only videos uploaded by admins
            $videos = Video::with('progress', 'user')
                ->whereHas('user', function($query) {
                    $query->whereIn('role', ['admin', 'superadmin']);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        }

        return view('videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('videos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Debug: Log the request data
        Log::info('Video upload request', [
            'has_file' => $request->hasFile('video'),
            'file_valid' => $request->file('video') ? $request->file('video')->isValid() : false,
            'file_error' => $request->file('video') ? $request->file('video')->getError() : 'no file',
            'file_size' => $request->file('video') ? $request->file('video')->getSize() : 'no file',
            'all_data' => $request->all(),
            'files' => $request->allFiles()
        ]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:categories,id',
            'video' => 'required|file|mimes:mp4,avi,mov,wmv,flv,webm|max:102400', // 100MB max
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('video');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('videos/original', $filename, 'public');

            $video = Video::create([
                'title' => $request->title,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'original_filename' => $file->getClientOriginalName(),
                'original_path' => $path,
                'user_id' => Auth::id(),
                'status' => 'uploading'
            ]);

            // Start transcoding in background
            dispatch(function () use ($video) {
                $this->transcoderService->transcode($video);
            })->afterResponse();

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully. Processing will begin shortly.',
                'video_id' => $video->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        // Load the video with its user relationship
        $video->load('user', 'progress');
        
        // Check permissions based on user role
        if (Auth::user()->isAdmin()) {
            // Admins can view all videos
        } else {
            // Regular users can only view videos uploaded by admins
            if (!in_array($video->user->role, ['admin', 'superadmin'])) {
                abort(403, 'Access denied. You can only view videos uploaded by administrators.');
            }
        }

        $userProgress = $video->getUserProgress(Auth::id());

        return view('videos.show', compact('video', 'userProgress'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        if ($video->user_id !== Auth::id()) {
            abort(403);
        }

        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video): JsonResponse
    {
        if ($video->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $video->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Video updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video): JsonResponse
    {
        if ($video->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete files from storage
            if ($video->original_path) {
                Storage::disk('public')->delete($video->original_path);
            }
            if ($video->hls_path) {
                Storage::disk('public')->deleteDirectory($video->hls_path);
            }
            if ($video->thumbnail_path) {
                Storage::disk('public')->delete($video->thumbnail_path);
            }

            $video->delete();

            return response()->json([
                'success' => true,
                'message' => 'Video deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update video progress.
     */
    public function updateProgress(Request $request, Video $video): JsonResponse
    {
        // Allow users to track progress on videos uploaded by admins
        if (Auth::user()->isAdmin()) {
            // Admins can track progress on any video
        } else {
            // Regular users can only track progress on videos uploaded by admins
            if (!in_array($video->user->role, ['admin', 'superadmin'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'current_time' => 'required|integer|min:0',
            'total_time' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $progress = VideoProgress::updateOrCreate(
            [
                'video_id' => $video->id,
                'user_id' => Auth::id(),
            ],
            [
                'current_time' => $request->current_time,
                'total_time' => $request->total_time,
                'progress_percentage' => ($request->current_time / $request->total_time) * 100,
                'is_completed' => ($request->current_time / $request->total_time) >= 0.9,
                'last_watched_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }

    /**
     * Get video status.
     */
    public function status(Video $video): JsonResponse
    {
        // Allow users to check status of videos uploaded by admins
        if (Auth::user()->isAdmin()) {
            // Admins can check status of any video
        } else {
            // Regular users can only check status of videos uploaded by admins
            if (!in_array($video->user->role, ['admin', 'superadmin'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
        }

        return response()->json([
            'success' => true,
            'status' => $video->status,
            'is_ready' => $video->isReady(),
            'hls_url' => $video->hls_url,
            'thumbnail_url' => $video->thumbnail_url,
        ]);
    }

    /**
     * Get user's video progress (view).
     */
    public function progress()
    {
        $progress = Auth::user()->videoProgress()
            ->with('video')
            ->orderBy('last_watched_at', 'desc')
            ->get();

        return view('progress.index', compact('progress'));
    }

    /**
     * Get user's video progress (API).
     */
    public function progressApi(): JsonResponse
    {
        $progress = Auth::user()->videoProgress()
            ->with('video')
            ->orderBy('last_watched_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'progress' => $progress
        ]);
    }
}
