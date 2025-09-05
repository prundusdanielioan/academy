<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of active categories for users.
     */
    public function index()
    {
        $categories = Category::active()
            ->ordered()
            ->withCount(['videos' => function($query) {
                $query->whereHas('user', function($userQuery) {
                    $userQuery->whereIn('role', ['admin', 'superadmin']);
                })->where('status', 'completed');
            }])
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Display videos in a specific category.
     */
    public function show(Category $category)
    {
        $videos = Video::where('category_id', $category->id)
            ->whereHas('user', function($query) {
                $query->whereIn('role', ['admin', 'superadmin']);
            })
            ->where('status', 'completed')
            ->with('user')
            ->latest()
            ->paginate(12);

        return view('categories.show', compact('category', 'videos'));
    }
}
