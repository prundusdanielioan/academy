@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manage PDFs</h1>
        <a href="{{ route('admin.pdfs.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Upload New PDF
        </a>
    </div>

    @if($pdfs->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @foreach($pdfs as $pdf)
                    <li>
                        <div class="px-4 py-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-16 w-16">
                                    <div class="h-16 w-16 bg-red-100 rounded flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('pdfs.show', $pdf) }}" class="hover:text-blue-600">
                                            {{ $pdf->title }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Uploaded by: {{ $pdf->user->name }} ({{ $pdf->user->email }})
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $pdf->original_filename }} • {{ $pdf->formatted_file_size }}
                                        @if($pdf->page_count)
                                            • {{ $pdf->page_count }} pages
                                        @endif
                                    </div>
                                    @if($pdf->category)
                                        <div class="text-sm text-gray-500">
                                            Category: {{ $pdf->category->name }}
                                        </div>
                                    @endif
                                    <div class="text-sm text-gray-500">
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-eye mr-1"></i>
                                            {{ $pdf->is_public ? 'Public' : 'Private' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($pdf->status === 'completed') bg-green-100 text-green-800
                                    @elseif($pdf->status === 'processing') bg-yellow-100 text-yellow-800
                                    @elseif($pdf->status === 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($pdf->status) }}
                                </span>
                                <a href="{{ route('pdfs.download', $pdf) }}" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                    Download
                                </a>
                                <a href="{{ route('admin.pdfs.edit', $pdf) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Edit
                                </a>
                                <form action="{{ route('admin.pdfs.destroy', $pdf) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this PDF?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="mt-6">
            {{ $pdfs->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="mx-auto h-12 w-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
            </div>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No PDFs</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by uploading a new PDF document.</p>
            <div class="mt-6">
                <a href="{{ route('admin.pdfs.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-upload -ml-1 mr-2 h-5 w-5"></i>
                    Upload PDF
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
