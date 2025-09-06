@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $video->title }}</h1>
                            @if($video->description)
                                <p class="text-gray-600 mt-2">{{ $video->description }}</p>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.videos.edit', $video) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Edit
                            </a>
                            @endif
                            <a href="{{ route('videos.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Back to Videos
                            </a>
                        </div>
                    </div>
                </div>

                @if($video->isReady())
                    <div class="video-container mb-6">
                        <video id="videoPlayer" class="video-player" controls preload="metadata">
                            Your browser does not support the video tag.
                        </video>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Watch Progress</span>
                            <span id="progressText" class="text-sm text-gray-500">
                                @if($userProgress)
                                    {{ number_format($userProgress->progress_percentage, 1) }}% watched
                                @else
                                    0% watched
                                @endif
                            </span>
                        </div>
                        <div class="progress-bar">
                            <div id="progressFill" class="progress-fill" 
                                 style="width: {{ $userProgress ? $userProgress->progress_percentage : 0 }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Video Information</h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                @if($video->duration)
                                    <div><strong>Duration:</strong> {{ gmdate('H:i:s', $video->duration) }}</div>
                                @endif
                                @if($video->resolution)
                                    <div><strong>Resolution:</strong> {{ $video->resolution }}</div>
                                @endif
                                @if($video->file_size)
                                    <div><strong>File Size:</strong> {{ number_format($video->file_size / 1024 / 1024, 2) }} MB</div>
                                @endif
                                <div><strong>Uploaded:</strong> {{ $video->created_at->format('M d, Y H:i') }}</div>
                                @if($userProgress && $userProgress->last_watched_at)
                                    <div><strong>Last Watched:</strong> {{ $userProgress->last_watched_at->diffForHumans() }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Watch Statistics</h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                @if($userProgress)
                                    <div><strong>Current Position:</strong> <span id="currentTime">{{ $userProgress->formatted_current_time }}</span></div>
                                    <div><strong>Total Time:</strong> <span id="totalTime">{{ $userProgress->formatted_total_time }}</span></div>
                                    <div><strong>Progress:</strong> {{ number_format($userProgress->progress_percentage, 1) }}%</div>
                                    @if($userProgress->is_completed)
                                        <div class="text-green-600 font-medium">✓ Completed</div>
                                    @endif
                                @else
                                    <div>No watch history yet</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        @if($video->status === 'uploading')
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Uploading Video</h3>
                            <p class="text-gray-600">Please wait while your video is being uploaded...</p>
                        @elseif($video->status === 'processing')
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Processing Video</h3>
                            <p class="text-gray-600">Your video is being processed. This may take a few minutes...</p>
                            <div class="mt-4">
                                <button onclick="checkStatus()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Check Status
                                </button>
                            </div>
                        @elseif($video->status === 'failed')
                            <svg class="mx-auto h-12 w-12 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Processing Failed</h3>
                            <p class="text-gray-600 mb-4">{{ $video->processing_log }}</p>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.videos.edit', $video) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Try Again
                            </a>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let hls = null;
let progressUpdateInterval = null;
let lastProgressUpdate = 0;

@if($video->isReady())
// Initialize HLS player
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('videoPlayer');
    const videoSrc = '{{ $video->hls_url }}';
    
    console.log('Video element:', video);
    console.log('Video source:', videoSrc);
    console.log('HLS supported:', Hls.isSupported());
    
    if (Hls.isSupported()) {
        console.log('Using HLS.js');
        hls = new Hls();
        hls.loadSource(videoSrc);
        hls.attachMedia(video);
        
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            console.log('HLS manifest loaded successfully');
            
            // Resume from last position if available
            @if($userProgress && $userProgress->current_time > 0)
                video.currentTime = {{ $userProgress->current_time }};
            @endif
            
            // Start progress tracking
            startProgressTracking();
        });
        
        hls.on(Hls.Events.ERROR, function(event, data) {
            console.error('HLS error:', event, data);
            if (data.fatal) {
                switch(data.type) {
                    case Hls.ErrorTypes.NETWORK_ERROR:
                        console.log('Fatal network error encountered, try to recover');
                        hls.startLoad();
                        break;
                    case Hls.ErrorTypes.MEDIA_ERROR:
                        console.log('Fatal media error encountered, try to recover');
                        hls.recoverMediaError();
                        break;
                    default:
                        console.log('Fatal error, cannot recover');
                        hls.destroy();
                        break;
                }
            }
        });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        console.log('Using native HLS support (Safari)');
        // Native HLS support (Safari)
        video.src = videoSrc;
        video.addEventListener('loadedmetadata', function() {
            console.log('Native HLS metadata loaded');
            @if($userProgress && $userProgress->current_time > 0)
                video.currentTime = {{ $userProgress->current_time }};
            @endif
            startProgressTracking();
        });
    } else {
        console.error('HLS not supported in this browser');
    }
});

function startProgressTracking() {
    const video = document.getElementById('videoPlayer');
    
    // Update progress every 5 seconds
    progressUpdateInterval = setInterval(function() {
        if (!video.paused && video.currentTime > 0) {
            updateProgress(video.currentTime, video.duration);
        }
    }, 5000);
    
    // Update progress on video end
    video.addEventListener('ended', function() {
        updateProgress(video.duration, video.duration);
    });
    
    // Update progress on seek
    video.addEventListener('seeked', function() {
        updateProgress(video.currentTime, video.duration);
    });
}

function updateProgress(currentTime, totalTime) {
    // Throttle updates to avoid too many requests
    const now = Date.now();
    if (now - lastProgressUpdate < 2000) return;
    lastProgressUpdate = now;
    
    const progressPercentage = (currentTime / totalTime) * 100;
    
    // Update UI
    document.getElementById('progressFill').style.width = progressPercentage + '%';
    document.getElementById('progressText').textContent = progressPercentage.toFixed(1) + '% watched';
    document.getElementById('currentTime').textContent = formatTime(currentTime);
    document.getElementById('totalTime').textContent = formatTime(totalTime);
    
    // Send to server
    fetch('{{ route("videos.progress", $video) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken,
        },
        body: JSON.stringify({
            current_time: Math.floor(currentTime),
            total_time: Math.floor(totalTime)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Progress updated');
        }
    })
    .catch(error => {
        console.error('Error updating progress:', error);
    });
}

function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = Math.floor(seconds % 60);
    
    if (hours > 0) {
        return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    }
    return String(minutes).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
}
@endif

@if(!$video->isReady())
function checkStatus() {
    fetch('{{ route("videos.status", $video) }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.is_ready) {
                window.location.reload();
            } else if (data.status === 'failed') {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error checking status:', error);
        });
}

// Auto-check status every 10 seconds for processing videos
@if($video->status === 'processing')
setInterval(checkStatus, 10000);
@endif
@endif

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (progressUpdateInterval) {
        clearInterval(progressUpdateInterval);
    }
    if (hls) {
        hls.destroy();
    }
});
</script>
@endpush
@endsection
