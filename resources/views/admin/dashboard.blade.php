@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h1 class="mb-0 font-weight-bold">Dashboard</h1>
            <small class="text-muted">{{ now()->format('l, F j, Y') }}</small>
        </div>
    </div>
@stop

@section('content')

{{-- ============================================================
     STATS ROW — role-based, info-boxes are clickable quick links
============================================================ --}}
{{-- ============================================================
     STATS ROW — role-based, grouped by section
============================================================ --}}
<div class="row">

    {{-- ==============================
         LEFT / MAIN STATS COLUMN
    ============================== --}}
    <div class="{{ auth()->user()->hasRole('admin') ? 'col-md-9' : 'col-md-12' }}">

        {{-- ADMIN ONLY: User Management --}}
        @if(auth()->user()->hasRole('admin'))
        <div class="row">

            {{-- User Management --}}
            <div class="col-6 col-md-4">
                <p class="text-xs text-uppercase text-muted font-weight-bold mb-1">
                    <i class="fas fa-shield-alt mr-1"></i> User Management
                </p>
                <a href="{{ route('admin.users.index') }}" class="info-box-link">
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
                </a>
            </div>

            {{-- News & Announcements (fills the remaining 2 columns) --}}
            @if(auth()->user()->hasAnyRole(['admin', 'editor', 'publisher']))
            <div class="col-12 col-md-8">
                <p class="text-xs text-uppercase text-muted font-weight-bold mb-1">
                    <i class="fas fa-bullhorn mr-1"></i> News &amp; Announcements
                </p>
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.dotuni_news.index') }}" class="info-box-link">
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
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.announcements.index') }}" class="info-box-link">
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
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>
        @endif

        {{-- EDITOR + PUBLISHER ONLY: News & Announcements (no user management) --}}
        @if(!auth()->user()->hasRole('admin') && auth()->user()->hasAnyRole(['editor', 'publisher']))
        <p class="text-xs text-uppercase text-muted font-weight-bold mb-1 mt-2">
            <i class="fas fa-bullhorn mr-1"></i> News &amp; Announcements
        </p>
        <div class="row">
            <div class="col-6 col-md-4">
                <a href="{{ route('admin.dotuni_news.index') }}" class="info-box-link">
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
                </a>
            </div>
            <div class="col-6 col-md-4">
                <a href="{{ route('admin.announcements.index') }}" class="info-box-link">
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
                </a>
            </div>
        </div>
        @endif

        {{-- ADMIN + EDITOR: Content Management --}}
        @if(auth()->user()->hasAnyRole(['admin', 'editor']))
        <p class="text-xs text-uppercase text-muted font-weight-bold mb-1 mt-2">
            <i class="fas fa-folder-open mr-1"></i> Content Management
        </p>
        <div class="row">
            <div class="col-6 col-md-4">
                <a href="{{ route('admin.courses.index') }}" class="info-box-link">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-gradient-success"><i class="fas fa-graduation-cap"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Courses</span>
                            <span class="info-box-number">{{ $stats['courses'] }}</span>
                            <span class="info-box-text text-sm text-warning">{{ $stats['courses_archived'] }} archived</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4">
                <a href="{{ route('admin.programs.index') }}" class="info-box-link">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-gradient-primary"><i class="fas fa-book-open"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Programs</span>
                            <span class="info-box-number">{{ $stats['programs'] }}</span>
                            <span class="info-box-text text-sm text-warning">{{ $stats['programs_archived'] }} archived</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4">
                <a href="{{ route('admin.gallery.index') }}" class="info-box-link">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-gradient-warning"><i class="fas fa-images"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Gallery</span>
                            <span class="info-box-number">{{ $stats['gallery'] }}</span>
                            <span class="info-box-text text-sm text-warning">{{ $stats['gallery_archived'] }} archived</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4">
                <a href="{{ route('admin.faqs_questions.index') }}" class="info-box-link">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-gradient-danger"><i class="fas fa-question-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">FAQs</span>
                            <span class="info-box-number">{{ $stats['faq_questions'] }}</span>
                            <span class="info-box-text text-sm text-warning">{{ $stats['faq_questions_archived'] }} archived</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4">
                <a href="{{ route('admin.linkages.index') }}" class="info-box-link">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-gradient-secondary"><i class="fas fa-link"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Linkages</span>
                            <span class="info-box-number">{{ $stats['linkages'] }}</span>
                            <span class="info-box-text text-sm text-warning">{{ $stats['linkages_archived'] }} archived</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4">
                <a href="{{ route('admin.forms.index') }}" class="info-box-link">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-gradient-info"><i class="fas fa-file-alt"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Forms</span>
                            <span class="info-box-number">{{ $stats['forms'] }}</span>
                            <span class="info-box-text text-sm text-warning">{{ $stats['forms_archived'] }} archived</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4">
                <a href="{{ route('admin.e_resources.index') }}" class="info-box-link">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-gradient-success"><i class="fas fa-database"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">E-Resources</span>
                            <span class="info-box-number">{{ $stats['eresources'] }}</span>
                            <span class="info-box-text text-sm text-warning">{{ $stats['eresources_archived'] }} archived</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4">
                <a href="{{ route('admin.fees.index') }}" class="info-box-link">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-gradient-primary"><i class="fas fa-money-bill-wave"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Fees</span>
                            <span class="info-box-number">{{ $stats['fees'] }}</span>
                            <span class="info-box-text text-sm text-warning">{{ $stats['fees_archived'] }} archived</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-6 col-md-4">
                <a href="{{ route('admin.rule_articles.index') }}" class="info-box-link">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-gradient-info"><i class="fas fa-balance-scale"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Rules &amp; Regulations</span>
                            <span class="info-box-number">{{ $stats['rule_articles'] }}</span>
                            <span class="info-box-text text-sm text-muted">
                                {{ $stats['rule_sections'] }} sec
                                · {{ $stats['rule_sub_sections'] }} sub
                                · {{ $stats['rule_clauses'] }} cls
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endif {{-- end admin+editor --}}



    </div>{{-- end left/main stats --}}


    {{-- ==============================
         RIGHT COLUMN: ANALYTICS (admin only)
    ============================== --}}
    @if(auth()->user()->hasRole('admin'))
    <div class="col-md-3">
        <p class="text-xs text-uppercase text-muted font-weight-bold mb-1">
            <i class="fas fa-chart-line mr-1"></i> Visitor Analytics
        </p>

        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-teal"><i class="fas fa-eye"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Today's Visits</span>
                <span class="info-box-number">{{ number_format($visitorStats['today']) }}</span>
                <span class="info-box-text text-sm text-muted">{{ number_format($visitorStats['unique_ips']) }} unique IPs</span>
            </div>
        </div>

        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-purple"><i class="fas fa-calendar-week"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">This Week</span>
                <span class="info-box-number">{{ number_format($visitorStats['this_week']) }}</span>
                <span class="info-box-text text-sm text-muted">visits this week</span>
            </div>
        </div>

        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-gradient-olive"><i class="fas fa-chart-bar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">This Month</span>
                <span class="info-box-number">{{ number_format($visitorStats['this_month']) }}</span>
                <span class="info-box-text text-sm text-muted">{{ number_format($visitorStats['total']) }} all-time</span>
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
        <div class="card card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-newspaper mr-2"></i>Recent DotUni News</h3>
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

        {{-- ADMIN + EDITOR: Recent Announcements --}}
        @if(auth()->user()->hasAnyRole(['admin', 'editor']))
        <div class="card card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bullhorn mr-2"></i>Recent Announcements</h3>
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
        @endif

    </div>{{-- end left column --}}


    {{-- RIGHT COLUMN --}}
    <div class="col-md-4">

        {{-- ADMIN: User Summary --}}
        @if(auth()->user()->hasRole('admin'))
        <div class="card card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>User Summary</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Roles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentUsers as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>
                                @forelse($u->roles as $role)
                                    <span class="badge badge-info">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted small">No role</span>
                                @endforelse
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted small d-flex justify-content-between py-2">
                <span>Total: <strong>{{ $stats['users'] }}</strong></span>
            </div>
        </div>
        @endif

    </div>{{-- end right column --}}

</div>{{-- end main row --}}


{{-- ============================================================
     VISITOR ANALYTICS — ADMIN ONLY
============================================================ --}}
@if(auth()->user()->hasRole('admin'))

<hr class="mt-2 mb-4">

<div class="d-flex align-items-center mb-3">
    <h5 class="mb-0 font-weight-bold"><i class="fas fa-chart-area mr-2 text-primary"></i>Visitor Analytics</h5>
</div>

{{-- Visits Line Chart --}}
<div class="row">
    <div class="col-12">
        <div class="card card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Website Visits — Last 14 Days</h3>
            </div>
            <div class="card-body">
                <canvas id="visitorChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">

    {{-- Top Pages --}}
    <div class="col-md-4">
        <div class="card card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-file mr-2"></i>Top Pages</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Page</th>
                            <th>Visits</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitorStats['top_pages'] as $page)
                        <tr>
                            <td><code>/{{ $page->page_name }}</code></td>
                            <td><span class="badge badge-primary">{{ $page->visits }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">No data yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Top Countries --}}
    <div class="col-md-4">
        <div class="card card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-globe mr-2"></i>Top Countries</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Country</th>
                            <th>Visits</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitorStats['top_countries'] as $country)
                        <tr>
                            <td>{{ $country->country ?? 'Unknown' }}</td>
                            <td><span class="badge badge-success">{{ $country->visits }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-3">No data yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Devices --}}
    <div class="col-md-4">
        <div class="card card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-desktop mr-2"></i>Devices</h3>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="deviceChart" height="220"></canvas>
            </div>
        </div>
    </div>

</div>
{{-- Recent Visits Table --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title-dt">
                    <i class="fas fa-history mr-2"></i>Recent Visits
                </h3>
            </div>
            <div class="card-body">
                <table id="visitorTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>IP Address</th>
                            <th>Location</th>
                            <th>Page</th>
                            <th>Browser</th>
                            <th>OS</th>
                            <th>Device</th>
                            <th>Referer</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


@endif {{-- end admin-only analytics --}}

@stop

@push('css')
<style>
    .info-box { border-radius: 6px; margin-bottom: 1rem; }
    .info-box-icon { border-radius: 6px 0 0 6px; }
    .info-box-text { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .card { border-radius: 8px; }
    .card-outline { border-top-width: 3px; }
    .list-group-item { font-size: 0.9rem; }

    /* Clickable info-box styles */
    .info-box-link {
        display: block;
        text-decoration: none;
        color: inherit;
    }
    .info-box-link:hover {
        text-decoration: none;
        color: inherit;
    }
    .info-box-link:hover .info-box {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.12) !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .info-box {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
</style>
@endpush

@push('js')
@if(auth()->user()->hasRole('admin'))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Visits Line Chart
    const dailyData = @json($visitorStats['daily_chart']);

    new Chart(document.getElementById('visitorChart'), {
        type: 'line',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: 'Visits',
                data: dailyData.map(d => d.visits),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.08)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#007bff',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Device Doughnut Chart
    const deviceData = @json($visitorStats['devices']);

    new Chart(document.getElementById('deviceChart'), {
        type: 'doughnut',
        data: {
            labels: deviceData.map(d => d.device_type),
            datasets: [{
                data: deviceData.map(d => d.count),
                backgroundColor: ['#007bff', '#28a745', '#ffc107'],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });


    // Visitor DataTable
    if ($.fn.DataTable.isDataTable('#visitorTable')) {
        $('#visitorTable').DataTable().destroy();
    }

    $('#visitorTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 10,
        autoWidth: true,
        scrollCollapse: true,
        scrollX: true,
        order: [[8, 'desc']],
        ajax: "{{ route('admin.analytics.visitors') }}",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, type, row, meta) =>
                    meta.row + meta.settings._iDisplayStart + 1
            },
            { data: 'ip_address',  orderable: true  },
            { data: 'location',    orderable: true  },
            { data: 'page_name',   orderable: true  },
            { data: 'browser',     orderable: true  },
            { data: 'os',          orderable: true  },
            { data: 'device_type', orderable: true  },
            { data: 'referer',     orderable: true  },
            { data: 'created_at',  orderable: true  },
        ]
    });

</script>
@endif
@endpush

