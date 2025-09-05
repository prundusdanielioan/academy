@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Dashboard</h2>
                    <div class="flex space-x-3">
                        <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Browse Categories
                        </a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.videos.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Upload Video
                        </a>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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
                    
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ auth()->user()->isAdmin() ? 'My Videos' : 'Total Videos' }}</h3>
                                <p class="text-2xl font-bold text-blue-600">{{ $totalVideos }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">{{ auth()->user()->isAdmin() ? 'Ready to Watch' : 'Available Now' }}</h3>
                                <p class="text-2xl font-bold text-green-600">{{ $completedVideos }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if(auth()->user()->isAdmin())
                    <div class="bg-yellow-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Processing</h3>
                                <p class="text-2xl font-bold text-yellow-600">{{ $processingVideos }}</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-gray-900">Watched</h3>
                                <p class="text-2xl font-bold text-purple-600">{{ auth()->user()->videoProgress()->where('is_completed', true)->count() }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Videos -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Videos</h3>
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
                            <div class="space-y-3">
                                @foreach($recentVideos as $video)
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                                        <div class="flex items-center space-x-3">
                                            @if($video->thumbnail_url)
                                                <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-12 h-8 object-cover rounded">
                                            @else
                                                <div class="w-12 h-8 bg-gray-200 rounded flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">{{ $video->title }}</h4>
                                                <p class="text-xs text-gray-500">{{ $video->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="status-badge status-{{ $video->status }}">
                                                @if(auth()->user()->isAdmin())
                                                    {{ ucfirst($video->status) }}
                                                @else
                                                    {{ $video->status === 'completed' ? 'Ready' : ucfirst($video->status) }}
                                                @endif
                                            </span>
                                            @if($video->isReady())
                                                <a href="{{ route('videos.show', $video) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                    Watch
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No videos yet</p>
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('videos.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View all videos →
                            </a>
                        </div>
                    </div>
                    
                    <!-- Watch Progress (only for regular users) -->
                    @if(!auth()->user()->isAdmin())
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Watch Progress</h3>
                        @php
                            $recentProgress = auth()->user()->videoProgress()->with('video')->latest('last_watched_at')->take(5)->get();
                        @endphp
                        
                        @if($recentProgress->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentProgress as $progress)
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                                        <div class="flex items-center space-x-3">
                                            @if($progress->video->thumbnail_url)
                                                <img src="{{ $progress->video->thumbnail_url }}" alt="{{ $progress->video->title }}" class="w-12 h-8 object-cover rounded">
                                            @else
                                                <div class="w-12 h-8 bg-gray-200 rounded flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-900">{{ $progress->video->title }}</h4>
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-16 bg-gray-200 rounded-full h-1">
                                                        <div class="bg-blue-600 h-1 rounded-full" style="width: {{ $progress->progress_percentage }}%"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-500">{{ number_format($progress->progress_percentage, 1) }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{ route('videos.show', $progress->video) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                            Continue
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No watch history yet</p>
                        @endif
                        
                        <div class="mt-4">
                            <a href="{{ route('videos.user-progress') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View all progress →
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                
                @if(auth()->user()->isAdmin())
                <div class="mt-8 text-center">
                    <a href="{{ route('admin.videos.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Upload New Video
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
