{{--
    Reusable side panel for news detail pages.
    Accepts: $currentType ('announcement' | 'dotuni')
    Shows the 3 most recent items from the same content type as "Other Updates".
--}}

@php
    use Carbon\Carbon;

    // We resolve the sidebar data here so each layout doesn't need to worry about it.
    // The controller already bound these services, but we need them here.
    // Simplest approach: use the app container to resolve.

    $sideItems = collect();

    if ($currentType === 'announcement') {
        $sideItems = app(\App\Services\AnnouncementService::class)
            ->list()
            ->where('visibility', 'public')
            ->sortByDesc('publish_start')
            ->take(3)
            ->map(fn($i) => [
                'id'    => $i->id,
                'title' => $i->title,
                'image' => optional($i->thumbnail())->storage_path,
                'date'  => $i->publish_start ?? $i->created_at,
                'type'  => 'announcement',
                'url'   => route('news.show', ['type' => 'announcement', 'id' => $i->id]),
            ]);

    } elseif ($currentType === 'dotuni') {
        $sideItems = app(\App\Services\DotuniNewsService::class)
            ->list()
            ->where('status', 'published')
            ->sortByDesc('published_at')
            ->take(3)
            ->map(function ($i) {
                $thumb = $i->attachments->where('is_thumbnail', true)->first();
                return [
                    'id'    => $i->id,
                    'title' => $i->title,
                    'image' => optional(optional($thumb)->asset)->storage_path,
                    'date'  => $i->published_at ?? $i->created_at,
                    'type'  => 'dotuni',
                    'url'   => route('news.show', ['type' => 'dotuni', 'id' => $i->id]),
                ];
            });
    }
@endphp

<div class="side-content-container">
    <div class="other-content-header">
        <span>Other Updates</span>
    </div>

    <div class="side-content">
        @forelse($sideItems as $side)
            @php
                $sideImg = $side['image']
                    ? asset('storage/' . $side['image'])
                    : asset('assets/system_images/placeholder.jpg');
            @endphp

            <a class="content-card" href="{{ $side['url'] }}">
                {{-- <img src="{{ $sideImg }}" alt="{{ $side['title'] }}" loading="lazy"> --}}
                <div class="card-body">
                    <h4>{{ $side['title'] }}</h4>
                    <div class="card-footer">
                        <span class="content-date">
                            {{ Carbon::parse($side['date'])->format('M d, Y') }}
                        </span>
                        <span class="read-more">Read More</span>
                    </div>
                </div>
            </a>
        @empty
            <p style="padding:10px; color:#777; font-size:13px;">No other updates available.</p>
        @endforelse
    </div>

    <div class="btn-wrapper">
        <a class="view-all-btn" href="{{ route('website.news') }}">View All Updates</a>
    </div>
</div>