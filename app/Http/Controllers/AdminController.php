<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use App\Models\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        // Admin middleware is already applied in routes/web.php
    }

    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_videos' => Video::count(),
            'total_pdfs' => Pdf::count(),
            'admin_users' => User::whereIn('role', ['admin', 'superadmin'])->count(),
            'recent_videos' => Video::latest()->take(5)->get(),
            'recent_pdfs' => Pdf::latest()->take(5)->get(),
            'recent_users' => User::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Display all users (admin only).
     */
    public function users()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users', compact('users'));
    }

    /**
     * Display all videos (admin only).
     */
    public function allVideos()
    {
        $videos = Video::with('user')->latest()->paginate(15);
        return view('admin.videos', compact('videos'));
    }

    /**
     * Display all PDFs (admin only).
     */
    public function allPdfs()
    {
        $pdfs = Pdf::with('user')->latest()->paginate(15);
        return view('admin.pdfs', compact('pdfs'));
    }
}
