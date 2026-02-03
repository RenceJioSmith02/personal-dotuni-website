@if ($item->asset)
    @if ($item->asset->kind === 'image')
        <img
            src="{{ asset('storage/' . $item->asset->storage_path) }}"
            class="img-thumbnail"
            style="max-width:50px;"
            alt="{{ $item->title }}">
    @else
        <span class="text-muted">{{ ucfirst($item->asset->kind) }}</span>
    @endif
@else
    <span class="text-muted">—</span>
@endif
