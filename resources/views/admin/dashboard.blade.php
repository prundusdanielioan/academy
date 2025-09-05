@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-blue-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800">Total Users</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</p>
                    </div>
                    
                    <div class="bg-green-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800">Total Videos</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_videos'] }}</p>
                    </div>
                    
                    <div class="bg-purple-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-purple-800">Admin Users</h3>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['admin_users'] }}</p>
                    </div>
                    
                    <div class="bg-orange-100 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-orange-800">Your Role</h3>
                        <p class="text-xl font-bold text-orange-600">{{ auth()->user()->role }}</p>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.users') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Manage Users
                        </a>
                        <a href="{{ route('admin.videos') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Manage Videos
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Manage Categories
                        </a>
                        <a href="{{ route('admin.videos.create') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            Upload Video
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Recent Users -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Recent Users</h3>
                        @if($stats['recent_users']->count() > 0)
                            <div class="space-y-3">
                                @foreach($stats['recent_users'] as $user)
                                    <div class="flex justify-between items-center p-3 bg-white rounded border">
                                        <div>
                                            <p class="font-medium">{{ $user->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($user->role === 'superadmin') bg-red-100 text-red-800
                                            @elseif($user->role === 'admin') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No users found.</p>
                        @endif
                    </div>

                    <!-- Recent Videos -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Recent Videos</h3>
                        @if($stats['recent_videos']->count() > 0)
                            <div class="space-y-3">
                                @foreach($stats['recent_videos'] as $video)
                                    <div class="flex justify-between items-center p-3 bg-white rounded border">
                                        <div>
                                            <p class="font-medium">{{ $video->title }}</p>
                                            <p class="text-sm text-gray-600">by {{ $video->user->name ?? 'Unknown' }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($video->status === 'completed') bg-green-100 text-green-800
                                            @elseif($video->status === 'processing') bg-yellow-100 text-yellow-800
                                            @elseif($video->status === 'failed') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($video->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No videos found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
