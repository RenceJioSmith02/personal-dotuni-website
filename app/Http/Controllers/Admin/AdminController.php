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
use App\Models\RuleArticle;
use App\Models\RuleSection;
use App\Models\RuleSubSection;
use App\Models\RuleClause;
use App\Models\User;
use App\Models\VisitorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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

            // Rules & Regulations
            $stats['rule_articles'] = RuleArticle::whereNull('deleted_at')->count();
            $stats['rule_sections'] = RuleSection::whereNull('deleted_at')->count();
            $stats['rule_sub_sections'] = RuleSubSection::whereNull('deleted_at')->count();
            $stats['rule_clauses'] = RuleClause::whereNull('deleted_at')->count();

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

            // For the user summary table
            $recentUsers = User::with('roles')->whereNull('deleted_at')->latest()->take(8)->get();



            // ============================
            // VISITOR ANALYTICS (all roles)
            // ============================
            $visitorStats = [
                'today' => VisitorLog::whereDate('created_at', today())->count(),
                'this_week' => VisitorLog::whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
                'this_month' => VisitorLog::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)->count(),
                'total' => VisitorLog::count(),
                'unique_ips' => VisitorLog::distinct('ip_address')->count(),

                'top_pages' => VisitorLog::select('page_name', \DB::raw('count(*) as visits'))
                    ->groupBy('page_name')->orderByDesc('visits')->limit(5)->get(),

                'top_countries' => VisitorLog::select('country', \DB::raw('count(*) as visits'))
                    ->groupBy('country')->orderByDesc('visits')->limit(5)->get(),

                'devices' => VisitorLog::select('device_type', \DB::raw('count(*) as count'))
                    ->groupBy('device_type')->orderByDesc('count')->get(),

                'recent' => VisitorLog::latest()->limit(8)->get(),

                'daily_chart' => VisitorLog::select(
                    \DB::raw('DATE(created_at) as date'),
                    \DB::raw('count(*) as visits')
                )
                    ->where('created_at', '>=', now()->subDays(14))
                    ->groupBy('date')->orderBy('date')->get(),
            ];
        } else {
            $recentUsers = collect();
            $visitorStats = null;
        }

        return view('admin.dashboard', compact(
            'stats',
            'recentCourses',
            'recentAnnouncements',
            'recentDotuniNews',
            'recentUsers',
            'visitorStats'     
        ));
    }



    public function visitorsDataTable(Request $request)
    {
        $query = VisitorLog::query();

        // Global search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('page_name', 'like', "%{$search}%")
                    ->orWhere('browser', 'like', "%{$search}%")
                    ->orWhere('os', 'like', "%{$search}%")
                    ->orWhere('device_type', 'like', "%{$search}%")
                    ->orWhere('referer', 'like', "%{$search}%");
            });
        }

        $totalRecords = VisitorLog::count();
        $filteredCount = $query->count();

        // Ordering
        $orderColumnIndex = $request->input('order.0.column', 8);
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = [null, 'ip_address', 'country', 'page_name', 'browser', 'os', 'device_type', 'referer', 'created_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';
        $query->orderBy($orderColumn, $orderDir);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $visits = $query->skip($start)->take($length)->get();

        $data = $visits->map(function ($visit) {
            $location = '—';
            if ($visit->city || $visit->country) {
                $parts = array_filter([$visit->city, $visit->country]);
                $location = implode(', ', $parts);
            }

            $deviceIcon = match ($visit->device_type) {
                'Mobile' => 'fas fa-mobile-alt',
                'Tablet' => 'fas fa-tablet-alt',
                default => 'fas fa-desktop',
            };

            $referer = $visit->referer
                ? '<span title="' . e($visit->referer) . '">' . \Illuminate\Support\Str::limit($visit->referer, 35) . '</span>'
                : '—';

            return [
                'ip_address' => '<code>' . e($visit->ip_address) . '</code>',
                'location' => e($location),
                'page_name' => '<code>/' . e($visit->page_name) . '</code>',
                'browser' => e($visit->browser ?? '—'),
                'os' => e($visit->os ?? '—'),
                'device_type' => '<i class="' . $deviceIcon . ' mr-1"></i>' . e($visit->device_type ?? '—'),
                'referer' => $referer,
                'created_at' => '<span class="text-nowrap">' . $visit->created_at->diffForHumans() . '</span>',
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredCount,
            'data' => $data,
        ]);
    }
    
}
