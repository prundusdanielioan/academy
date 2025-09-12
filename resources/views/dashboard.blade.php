@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="hero-section">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="hero-title">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="hero-subtitle">
                @if(auth()->user()->isAdmin())
                    Manage your learning content and track student progress
                @else
                    Continue your learning journey with our curated video content
                @endif
            </p>
            <div class="flex justify-center space-x-4">
                <a href="{{ route('categories.index') }}" class="btn-secondary" style="background: rgba(255,255,255,0.2); color: white; border-color: rgba(255,255,255,0.3);">
                    <i class="fas fa-folder"></i>
                    Browse Categories
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.videos.create') }}" class="btn-primary" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-upload"></i>
                    Upload Video
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Grid -->
        <div class="stats-grid">
            @php
                if (auth()->user()->isAdmin()) {
                    // Admins see their uploaded videos
                    $totalVideos = auth()->user()->videos()->count();
                    $completedVideos = auth()->user()->videos()->where('status', 'completed')->count();
                    $processingVideos = auth()->user()->videos()->where('status', 'processing')->count();
                } else {
                    // Regular users see videos they can watch
                    $totalVideos = \App\Models\Video::whereHas('user', function($query) {
                        $query->whereIn('role', ['admin', 'superadmin']);
                    })->count();
                    $completedVideos = \App\Models\Video::whereHas('user', function($query) {
                        $query->whereIn('role', ['admin', 'superadmin']);
                    })->where('status', 'completed')->count();
                    $processingVideos = \App\Models\Video::whereHas('user', function($query) {
                        $query->whereIn('role', ['admin', 'superadmin']);
                    })->where('status', 'processing')->count();
                }
            @endphp
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-video"></i>
                </div>
                <div class="stat-number">{{ $totalVideos }}</div>
                <div class="stat-label">{{ auth()->user()->isAdmin() ? 'My Videos' : 'Total Videos' }}</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number">{{ $completedVideos }}</div>
                <div class="stat-label">{{ auth()->user()->isAdmin() ? 'Ready to Watch' : 'Available Now' }}</div>
            </div>
            
            @if(auth()->user()->isAdmin())
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number">{{ $processingVideos }}</div>
                <div class="stat-label">Processing</div>
            </div>
            @else
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-number">{{ auth()->user()->videoProgress()->where('is_completed', true)->count() }}</div>
                <div class="stat-label">Completed</div>
            </div>
            @endif
        </div>

        <!-- Content Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12">
            <!-- Recent Videos -->
            <div class="card">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold" style="color: var(--text-primary);">
                        <i class="fas fa-clock mr-2 text-blue-500"></i>
                        Recent Videos
                    </h3>
                    <a href="{{ route('videos.index') }}" class="text-sm font-medium" style="color: var(--primary-color);">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @php
                    if (auth()->user()->isAdmin()) {
                        // Admins see their uploaded videos
                        $recentVideos = auth()->user()->videos()->latest()->take(5)->get();
                    } else {
                        // Regular users see videos uploaded by admins
                        $recentVideos = \App\Models\Video::whereHas('user', function($query) {
                            $query->whereIn('role', ['admin', 'superadmin']);
                        })->latest()->take(5)->get();
                    }
                @endphp
                
                @if($recentVideos->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentVideos as $video)
                            <div class="flex items-center justify-between p-4 rounded-xl transition-all duration-200 hover:shadow-md" style="background: var(--background-color); border: 1px solid var(--border-color);">
                                <div class="flex items-center space-x-4">
                                    @if($video->thumbnail_url)
                                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-16 h-12 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-video text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="font-medium" style="color: var(--text-primary);">{{ $video->title }}</h4>
                                        <p class="text-sm" style="color: var(--text-secondary);">{{ $video->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="status-badge status-{{ $video->status }}">
                                        @if(auth()->user()->isAdmin())
                                            {{ ucfirst($video->status) }}
                                        @else
                                            {{ $video->status === 'completed' ? 'Ready' : ucfirst($video->status) }}
                                        @endif
                                    </span>
                                    @if($video->isReady())
                                        <a href="{{ route('videos.show', $video) }}" class="btn-primary text-sm py-2 px-3">
                                            <i class="fas fa-play"></i>
                                            Watch
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-video text-4xl mb-4" style="color: var(--text-secondary);"></i>
                        <p style="color: var(--text-secondary);">No videos yet</p>
                    </div>
                @endif
            </div>
            
            <!-- Watch Progress (only for regular users) -->
            @if(!auth()->user()->isAdmin())
            <div class="card">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold" style="color: var(--text-primary);">
                        <i class="fas fa-chart-line mr-2 text-green-500"></i>
                        Watch Progress
                    </h3>
                    <a href="{{ route('videos.user-progress') }}" class="text-sm font-medium" style="color: var(--primary-color);">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                @php
                    $recentProgress = auth()->user()->videoProgress()->with('video')->latest('last_watched_at')->take(5)->get();
                @endphp
                
                @if($recentProgress->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentProgress as $progress)
                            <div class="flex items-center justify-between p-4 rounded-xl transition-all duration-200 hover:shadow-md" style="background: var(--background-color); border: 1px solid var(--border-color);">
                                <div class="flex items-center space-x-4">
                                    @if($progress->video->thumbnail_url)
                                        <img src="{{ $progress->video->thumbnail_url }}" alt="{{ $progress->video->title }}" class="w-16 h-12 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-video text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-medium" style="color: var(--text-primary);">{{ $progress->video->title }}</h4>
                                        <div class="flex items-center space-x-3 mt-2">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progress->progress_percentage }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium" style="color: var(--text-secondary);">{{ number_format($progress->progress_percentage, 1) }}%</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('videos.show', $progress->video) }}" class="btn-primary text-sm py-2 px-3">
                                    <i class="fas fa-play"></i>
                                    Continue
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-chart-line text-4xl mb-4" style="color: var(--text-secondary);"></i>
                        <p style="color: var(--text-secondary);">No watch history yet</p>
                    </div>
                @endif
            </div>
            @endif
        </div>

        @if(auth()->user()->isAdmin())
        <div class="mt-12 text-center">
            <a href="{{ route('admin.videos.create') }}" class="btn-primary text-lg py-4 px-8">
                <i class="fas fa-upload"></i>
                Upload New Video
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
