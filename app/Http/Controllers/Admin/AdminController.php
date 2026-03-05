<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\DotuniNews;
use App\Models\EResource;
use App\Models\FaqQuestion;
use App\Models\Fee;
use App\Models\Form;
use App\Models\Gallery;
use App\Models\Linkage;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $user = Auth::user();
        $stats = [];

        // ============================
        // ADMIN + EDITOR stats
        // ============================
        if ($user->hasAnyRole(['admin', 'editor'])) {

            $stats['courses'] = Course::whereNull('deleted_at')->count();
            $stats['courses_archived'] = Course::whereNotNull('deleted_at')->count();

            $stats['programs'] = Program::whereNull('deleted_at')->count();
            $stats['programs_archived'] = Program::whereNotNull('deleted_at')->count();

            $stats['gallery'] = Gallery::whereNull('deleted_at')->count();
            $stats['gallery_archived'] = Gallery::whereNotNull('deleted_at')->count();

            $stats['faq_questions'] = FaqQuestion::whereNull('deleted_at')->count();
            $stats['faq_questions_archived'] = FaqQuestion::whereNotNull('deleted_at')->count();

            $stats['linkages'] = Linkage::whereNull('deleted_at')->count();
            $stats['linkages_archived'] = Linkage::whereNotNull('deleted_at')->count();

            $stats['forms'] = Form::whereNull('deleted_at')->count();
            $stats['forms_archived'] = Form::whereNotNull('deleted_at')->count();

            $stats['eresources'] = EResource::whereNull('deleted_at')->count();
            $stats['eresources_archived'] = EResource::whereNotNull('deleted_at')->count();

            $stats['fees'] = Fee::whereNull('deleted_at')->count();
            $stats['fees_archived'] = Fee::whereNotNull('deleted_at')->count();

            // Recent records
            $recentCourses = Course::whereNull('deleted_at')->latest()->take(6)->get();
            $recentAnnouncements = Announcement::latest()->take(5)->get();
        } else {
            $recentCourses = collect();
            $recentAnnouncements = collect();
        }

        // ============================
        // ADMIN + EDITOR + PUBLISHER stats
        // ============================
        if ($user->hasAnyRole(['admin', 'editor', 'publisher'])) {

            $stats['dotuni_news'] = DotuniNews::count();
            $stats['dotuni_news_published'] = DotuniNews::where('status', 'published')->count();

            $stats['announcements'] = Announcement::count();
            $stats['announcements_public'] = Announcement::where('visibility', 'public')->count();

            $recentDotuniNews = DotuniNews::latest()->take(6)->get();
        } else {
            $recentDotuniNews = collect();
        }

        // ============================
        // ADMIN ONLY stats
        // ============================
        if ($user->hasRole('admin')) {
            $stats['users'] = User::count();
            $stats['users_active'] = User::whereNull('deleted_at')->where('is_active', true)->count();
            $stats['users_inactive'] = User::whereNull('deleted_at')->where('is_active', false)->count();
            $stats['users_archived'] = User::whereNotNull('deleted_at')->count();
        }

        return view('admin.dashboard', compact(
            'stats',
            'recentCourses',
            'recentAnnouncements',
            'recentDotuniNews'
        ));
    }
}
