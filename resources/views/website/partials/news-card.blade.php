{{-- <a href="#" class="content-card">

<img
src="{{ $content['image']
? asset('storage/'.$content['image'])
: asset('assets/system_images/placeholder.jpg') }}">

<div class="card-content">

<span class="card-category">
{{ strtoupper($content['type']) }}
</span>

<h4>{{ $content['title'] }}</h4>

<p>{{ $content['description'] }}</p>

<div class="card-footer" style="color: #777">
<span class="news-date">
    {{ \Carbon\Carbon::parse($content['date'])->format('M d, Y') }}
</span>
<span class="read-more">Read More</span>
</div>

</div>

</a> --}}


{{--
    Expects $content array with keys:
    id, title, description, image, date, type, url (clsu only)
--}}

@php
    $href = match($content['type']) {
        'clsu'         => $content['url'] ?? '#',
        'announcement' => route('news.show', ['type' => 'announcement', 'id' => $content['id']]),
        'dotuni'       => route('news.show', ['type' => 'dotuni',        'id' => $content['id']]),
        default        => '#',
    };

    $target = $content['type'] === 'clsu' ? '_blank' : '_self';
@endphp

<a href="{{ $href }}" target="{{ $target }}" class="content-card">

    <img
        src="{{ $content['image']
            ? asset('storage/' . $content['image'])
            : asset('assets/system_images/placeholder.jpg') }}"
        alt="{{ $content['title'] }}"
        loading="lazy">

    <div class="card-content">

        <span class="card-category">
            {{ strtoupper($content['type']) }}
        </span>

        <h4>{{ $content['title'] }}</h4>

        <p>{{ $content['description'] }}</p>

        <div class="card-footer" style="color: #777">
            <span class="news-date">
                {{ \Carbon\Carbon::parse($content['date'])->format('M d, Y') }}
            </span>
            <span class="read-more">Read More</span>
        </div>

    </div>

</a>