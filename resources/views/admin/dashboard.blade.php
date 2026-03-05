@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0 font-weight-bold">Dashboard</h1>
            <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
        </div>
        <div class="text-right">
            <span class="text-muted small">Logged in as</span><br>
            <strong>{{ auth()->user()->name }}</strong>
            @foreach(auth()->user()->roles as $role)
                <span class="badge badge-primary ml-1">{{ $role->name }}</span>
            @endforeach
        </div>
    </div>
@stop

@section('content')

{{-- ============================================================
     STATS ROW — role-based
============================================================ --}}
<div class="row">

    {{-- ADMIN ONLY --}}
    @if(auth()->user()->hasAnyRole(['admin']))

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-info"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Users</span>
                <span class="info-box-number">{{ $stats['users'] }}</span>
                <span class="info-box-text text-sm">
                    <span class="text-warning">{{ $stats['users_archived'] }} archived</span>
                </span>
            </div>
        </div>
    </div>

    @endif

    {{-- ADMIN + EDITOR --}}
    @if(auth()->user()->hasAnyRole(['admin', 'editor']))

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-success"><i class="fas fa-graduation-cap"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Courses</span>
                <span class="info-box-number">{{ $stats['courses'] }}</span>
                <span class="info-box-text text-sm text-warning">{{ $stats['courses_archived'] }} archived</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-primary"><i class="fas fa-book-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Programs</span>
                <span class="info-box-number">{{ $stats['programs'] }}</span>
                <span class="info-box-text text-sm text-warning">{{ $stats['programs_archived'] }} archived</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-warning"><i class="fas fa-images"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Gallery</span>
                <span class="info-box-number">{{ $stats['gallery'] }}</span>
                <span class="info-box-text text-sm text-warning">{{ $stats['gallery_archived'] }} archived</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-danger"><i class="fas fa-question-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">FAQ Questions</span>
                <span class="info-box-number">{{ $stats['faq_questions'] }}</span>
                <span class="info-box-text text-sm text-warning">{{ $stats['faq_questions_archived'] }} archived</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-secondary"><i class="fas fa-link"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Linkages</span>
                <span class="info-box-number">{{ $stats['linkages'] }}</span>
                <span class="info-box-text text-sm text-warning">{{ $stats['linkages_archived'] }} archived</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-info"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Forms</span>
                <span class="info-box-number">{{ $stats['forms'] }}</span>
                <span class="info-box-text text-sm text-warning">{{ $stats['forms_archived'] }} archived</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-success"><i class="fas fa-database"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">E-Resources</span>
                <span class="info-box-number">{{ $stats['eresources'] }}</span>
                <span class="info-box-text text-sm text-warning">{{ $stats['eresources_archived'] }} archived</span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-primary"><i class="fas fa-money-bill-wave"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Fees</span>
                <span class="info-box-number">{{ $stats['fees'] }}</span>
                <span class="info-box-text text-sm text-warning">{{ $stats['fees_archived'] }} archived</span>
            </div>
        </div>
    </div>

    @endif {{-- end admin+editor --}}

    {{-- ADMIN + EDITOR + PUBLISHER --}}
    @if(auth()->user()->hasAnyRole(['admin', 'editor', 'publisher']))

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-warning"><i class="fas fa-newspaper"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">DotUni News</span>
                <span class="info-box-number">{{ $stats['dotuni_news'] }}</span>
                <span class="info-box-text text-sm">
                    <span class="text-success">{{ $stats['dotuni_news_published'] }} published</span>
                </span>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-danger"><i class="fas fa-bullhorn"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Announcements</span>
                <span class="info-box-number">{{ $stats['announcements'] }}</span>
                <span class="info-box-text text-sm">
                    <span class="text-success">{{ $stats['announcements_public'] }} public</span>
                </span>
            </div>
        </div>
    </div>

    @endif

</div>{{-- end stats row --}}


{{-- ============================================================
     MAIN CONTENT AREA
============================================================ --}}
<div class="row">

    {{-- LEFT COLUMN --}}
    <div class="col-md-8">

        {{-- ADMIN + EDITOR + PUBLISHER: Recent News --}}
        @if(auth()->user()->hasAnyRole(['admin', 'editor', 'publisher']))
        <div class="card card-outline card-warning shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-newspaper mr-2"></i>Recent DotUni News</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.dotuni_news.index') }}" class="btn btn-sm btn-warning">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Title</th>
                            <th>Visibility</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDotuniNews as $news)
                        <tr>
                            <td>{{ Str::limit($news->title, 45) }}</td>
                            <td>
                                @if($news->visibility === 'public')
                                    <span class="badge badge-success">Public</span>
                                @elseif($news->visibility === 'unlisted')
                                    <span class="badge badge-warning">Unlisted</span>
                                @else
                                    <span class="badge badge-secondary">Private</span>
                                @endif
                            </td>
                            <td>
                                @if($news->status === 'published')
                                    <span class="badge badge-success">Published</span>
                                @else
                                    <span class="badge badge-secondary">Draft</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $news->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No news yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ADMIN + EDITOR: Recent Courses --}}
        @if(auth()->user()->hasAnyRole(['admin', 'editor']))
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-graduation-cap mr-2"></i>Recent Courses</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCourses as $course)
                        <tr>
                            <td><code>{{ $course->code }}</code></td>
                            <td>{{ Str::limit($course->name, 40) }}</td>
                            <td>
                                @if(is_null($course->deleted_at))
                                    <span class="badge badge-{{ $course->is_active ? 'success' : 'secondary' }}">
                                        {{ $course->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                @else
                                    <span class="badge badge-warning">Archived</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $course->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No courses yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Announcements --}}
        <div class="card card-outline card-danger shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bullhorn mr-2"></i>Recent Announcements</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-danger">View All</a>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Title</th>
                            <th>Visibility</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAnnouncements as $announcement)
                        <tr>
                            <td>{{ Str::limit($announcement->title, 50) }}</td>
                            <td>
                                @if($announcement->visibility === 'public')
                                    <span class="badge badge-success">Public</span>
                                @elseif($announcement->visibility === 'unlisted')
                                    <span class="badge badge-warning">Unlisted</span>
                                @else
                                    <span class="badge badge-secondary">Private</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $announcement->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No announcements yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif {{-- end admin+editor --}}

    </div>{{-- end left column --}}


    {{-- RIGHT COLUMN --}}
    <div class="col-md-4">

        {{-- QUICK LINKS --}}
        <div class="card card-outline card-secondary shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bolt mr-2"></i>Quick Links</h3>
            </div>
            <div class="card-body p-2">
                <div class="row no-gutters">

                    @if(auth()->user()->hasAnyRole(['admin', 'editor', 'publisher']))
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.dotuni_news.index') }}" class="btn btn-block btn-outline-warning btn-sm">
                            <i class="fas fa-newspaper mr-1"></i> DotUni News
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-block btn-outline-danger btn-sm">
                            <i class="fas fa-bullhorn mr-1"></i> Announcements
                        </a>
                    </div>
                    @endif

                    @if(auth()->user()->hasAnyRole(['admin', 'editor']))
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-block btn-outline-success btn-sm">
                            <i class="fas fa-graduation-cap mr-1"></i> Courses
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.programs.index') }}" class="btn btn-block btn-outline-primary btn-sm">
                            <i class="fas fa-book-open mr-1"></i> Programs
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.clsu_news.index') }}" class="btn btn-block btn-outline-info btn-sm">
                            <i class="fas fa-rss mr-1"></i> CLSU News
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.gallery.index') }}" class="btn btn-block btn-outline-warning btn-sm">
                            <i class="fas fa-images mr-1"></i> Gallery
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.forms.index') }}" class="btn btn-block btn-outline-secondary btn-sm">
                            <i class="fas fa-file-alt mr-1"></i> Forms
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.e_resources.index') }}" class="btn btn-block btn-outline-success btn-sm">
                            <i class="fas fa-database mr-1"></i> E-Resources
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.fees.index') }}" class="btn btn-block btn-outline-primary btn-sm">
                            <i class="fas fa-money-bill-wave mr-1"></i> Fees
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.faqs_questions.index') }}" class="btn btn-block btn-outline-danger btn-sm">
                            <i class="fas fa-question-circle mr-1"></i> FAQs
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.linkages.index') }}" class="btn btn-block btn-outline-secondary btn-sm">
                            <i class="fas fa-link mr-1"></i> Linkages
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.rule_articles.index') }}" class="btn btn-block btn-outline-info btn-sm">
                            <i class="fas fa-balance-scale mr-1"></i> Rules
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.prospective_student_categories.index') }}" class="btn btn-block btn-outline-warning btn-sm">
                            <i class="fas fa-user-graduate mr-1"></i> Prospective
                        </a>
                    </div>
                    @endif

                    @if(auth()->user()->hasAnyRole(['admin']))
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-block btn-outline-info btn-sm">
                            <i class="fas fa-users mr-1"></i> Users
                        </a>
                    </div>
                    <div class="col-6 p-1">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-block btn-outline-danger btn-sm">
                            <i class="fas fa-shield-alt mr-1"></i> Roles
                        </a>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- ARCHIVED ITEMS NEEDING ATTENTION --}}
        @if(auth()->user()->hasAnyRole(['admin', 'editor']))
        <div class="card card-outline card-warning shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-archive mr-2"></i>Archived Summary</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @php
                        $archivedItems = [
                            ['label' => 'Courses',       'count' => $stats['courses_archived'],       'route' => 'admin.courses.index'],
                            ['label' => 'Programs',      'count' => $stats['programs_archived'],      'route' => 'admin.programs.index'],
                            ['label' => 'Linkages',      'count' => $stats['linkages_archived'],      'route' => 'admin.linkages.index'],
                            ['label' => 'E-Resources',   'count' => $stats['eresources_archived'],    'route' => 'admin.e_resources.index'],
                            ['label' => 'Forms',         'count' => $stats['forms_archived'],         'route' => 'admin.forms.index'],
                            ['label' => 'Fees',          'count' => $stats['fees_archived'],          'route' => 'admin.fees.index'],
                            ['label' => 'Gallery',       'count' => $stats['gallery_archived'],       'route' => 'admin.gallery.index'],
                            ['label' => 'FAQ Questions', 'count' => $stats['faq_questions_archived'], 'route' => 'admin.faqs_questions.index'],
                        ];
                    @endphp
                    @foreach($archivedItems as $item)
                        @if($item['count'] > 0)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                            <a href="{{ route($item['route']) }}" class="text-dark">{{ $item['label'] }}</a>
                            <span class="badge badge-warning badge-pill">{{ $item['count'] }}</span>
                        </li>
                        @endif
                    @endforeach
                    @if(collect($archivedItems)->sum('count') === 0)
                    <li class="list-group-item text-center text-muted py-3">
                        <i class="fas fa-check-circle text-success mr-1"></i> No archived items
                    </li>
                    @endif
                </ul>
            </div>
        </div>
        @endif

        {{-- ADMIN: User Summary --}}
        @if(auth()->user()->hasRole('admin'))
        <div class="card card-outline card-info shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>User Summary</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-info">Manage</a>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between py-2">
                        <span>Total Users</span>
                        <strong>{{ $stats['users'] }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-2">
                        <span>Active</span>
                        <span class="badge badge-success badge-pill">{{ $stats['users_active'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-2">
                        <span>Inactive</span>
                        <span class="badge badge-secondary badge-pill">{{ $stats['users_inactive'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-2">
                        <span>Archived</span>
                        <span class="badge badge-warning badge-pill">{{ $stats['users_archived'] }}</span>
                    </li>
                </ul>
            </div>
        </div>
        @endif

    </div>{{-- end right column --}}

</div>{{-- end main row --}}

@stop

@section('css')
<style>
    .info-box { border-radius: 6px; margin-bottom: 1rem; }
    .info-box-icon { border-radius: 6px 0 0 6px; }
    .info-box-text { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .card { border-radius: 8px; }
    .card-outline { border-top-width: 3px; }
    .list-group-item { font-size: 0.9rem; }
    .btn-outline-secondary:hover, .btn-outline-info:hover,
    .btn-outline-primary:hover, .btn-outline-success:hover,
    .btn-outline-warning:hover, .btn-outline-danger:hover { color: #fff; }
</style>
@stop

@section('js')
@stop