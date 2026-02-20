<a href="#" class="content-card">

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

<div class="card-footer">
<span class="news-date">
    {{ \Carbon\Carbon::parse($content['date'])->format('M d, Y') }}
</span>
<span class="read-more">Read More</span>
</div>

</div>

</a>