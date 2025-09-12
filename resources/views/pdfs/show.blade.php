@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $pdf->title }}</h1>
                            @if($pdf->description)
                                <p class="text-gray-600 mt-2">{{ $pdf->description }}</p>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.pdfs.edit', $pdf) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Edit
                            </a>
                            @endif
                            <a href="{{ route('pdfs.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Back to PDFs
                            </a>
                        </div>
                    </div>
                </div>

                @if($pdf->isReady())
                    <!-- PDF Viewer Section -->
                    <div class="mb-6">
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">PDF Viewer</h3>
                                <div class="flex space-x-2">
                                    <a href="{{ route('pdfs.view', $pdf) }}" target="_blank" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        Open in New Tab
                                    </a>
                                    <a href="{{ route('pdfs.download', $pdf) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                                        <i class="fas fa-download mr-1"></i>
                                        Download
                                    </a>
                                </div>
                            </div>
                            
                            <!-- PDF Embed -->
                            <div class="border rounded-lg overflow-hidden" style="height: 600px;">
                                <iframe 
                                    src="{{ route('pdfs.view', $pdf) }}" 
                                    width="100%" 
                                    height="100%" 
                                    style="border: none;"
                                    title="{{ $pdf->title }}">
                                    <p>Your browser does not support PDFs. 
                                        <a href="{{ route('pdfs.download', $pdf) }}">Download the PDF</a> instead.
                                    </p>
                                </iframe>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Document Information</h3>
                            <div class="space-y-2 text-sm text-gray-600">
                                @if($pdf->formatted_file_size)
                                    <div><strong>File Size:</strong> {{ $pdf->formatted_file_size }}</div>
                                @endif
                                @if($pdf->page_count)
                                    <div><strong>Pages:</strong> {{ $pdf->page_count }}</div>
                                @endif
                                <div><strong>Original Filename:</strong> {{ $pdf->original_filename }}</div>
                                <div><strong>Uploaded:</strong> {{ $pdf->created_at->format('M d, Y H:i') }}</div>
                                <div><strong>Uploaded by:</strong> {{ $pdf->user->name }}</div>
                                @if($pdf->category)
                                    <div><strong>Category:</strong> 
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background: {{ $pdf->category->color }}20; color: {{ $pdf->category->color }};">
                                            <i class="fas fa-folder mr-1"></i>
                                            {{ $pdf->category->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-900 mb-2">Actions</h3>
                            <div class="space-y-3">
                                <a href="{{ route('pdfs.view', $pdf) }}" target="_blank" class="block w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                    <i class="fas fa-eye mr-2"></i>
                                    View in Browser
                                </a>
                                <a href="{{ route('pdfs.download', $pdf) }}" class="block w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                                    <i class="fas fa-download mr-2"></i>
                                    Download PDF
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.pdfs.edit', $pdf) }}" class="block w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
                                    <i class="fas fa-edit mr-2"></i>
                                    Edit PDF
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        @if($pdf->status === 'uploading')
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Uploading PDF</h3>
                            <p class="text-gray-600">Please wait while your PDF is being uploaded...</p>
                        @elseif($pdf->status === 'processing')
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Processing PDF</h3>
                            <p class="text-gray-600">Your PDF is being processed. This may take a few minutes...</p>
                        @elseif($pdf->status === 'failed')
                            <svg class="mx-auto h-12 w-12 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Processing Failed</h3>
                            <p class="text-gray-600 mb-4">{{ $pdf->processing_log }}</p>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.pdfs.edit', $pdf) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
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
@endsection
