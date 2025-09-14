<?php

namespace App\Http\Controllers;

use App\Models\Pdf;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PdfController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pdf::with(['user', 'category'])
            ->public()
            ->completed();

        // Handle category filtering
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Handle sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'most_downloaded':
                    // For now, just sort by creation date since we don't have download tracking
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $pdfs = $query->paginate(12);
        $categories = Category::active()->ordered()->get();

        return view('pdfs.index', compact('pdfs', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->ordered()->get();
        return view('pdfs.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'pdf_file' => 'required|file|mimes:pdf|max:102400', // 100MB max
            'is_public' => 'boolean',
        ]);

        $file = $request->file('pdf_file');
        $filename = time() . '_' . Str::slug($request->title) . '.pdf';
        $filePath = $file->storeAs('pdfs', $filename, 'public');

        $pdf = Pdf::create([
            'title' => $request->title,
            'description' => $request->description,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $file->getSize(),
            'status' => 'completed', // PDFs don't need processing like videos
            'is_public' => $request->boolean('is_public', true),
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('pdfs.show', $pdf)
            ->with('success', 'PDF uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pdf $pdf)
    {
        if (!$pdf->is_public && $pdf->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'This PDF is not public.');
        }

        $pdf->load(['user', 'category']);
        return view('pdfs.show', compact('pdf'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pdf $pdf)
    {
        if ($pdf->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only edit your own PDFs.');
        }

        $categories = Category::active()->ordered()->get();
        return view('pdfs.edit', compact('pdf', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pdf $pdf)
    {
        if ($pdf->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only edit your own PDFs.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'is_public' => 'boolean',
        ]);

        $pdf->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'is_public' => $request->boolean('is_public', true),
        ]);

        return redirect()->route('pdfs.show', $pdf)
            ->with('success', 'PDF updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pdf $pdf)
    {
        if ($pdf->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You can only delete your own PDFs.');
        }

        // Delete the file from storage
        if ($pdf->file_path && Storage::disk('public')->exists($pdf->file_path)) {
            Storage::disk('public')->delete($pdf->file_path);
        }

        $pdf->delete();

        return redirect()->route('pdfs.index')
            ->with('success', 'PDF deleted successfully!');
    }

    /**
     * Download the PDF file.
     */
    public function download(Pdf $pdf)
    {
        if (!$pdf->is_public && $pdf->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'This PDF is not public.');
        }

        if (!Storage::disk('public')->exists($pdf->file_path)) {
            abort(404, 'PDF file not found.');
        }

        return response()->download(Storage::disk('public')->path($pdf->file_path), $pdf->original_filename);
    }

    /**
     * View PDF in browser.
     */
    public function view(Pdf $pdf)
    {
        if (!$pdf->is_public && $pdf->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'This PDF is not public.');
        }

        if (!Storage::disk('public')->exists($pdf->file_path)) {
            abort(404, 'PDF file not found.');
        }

        return response()->file(Storage::disk('public')->path($pdf->file_path));
    }
}
