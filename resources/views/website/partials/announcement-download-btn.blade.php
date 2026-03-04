@php
    $isAnnouncement = ($item['type'] ?? '') === 'announcement';
    $downloadables  = collect();

    if ($isAnnouncement) {
        $downloadables = collect($item['assets'] ?? [])
            ->filter(fn($a) => isset($a->kind) && $a->kind !== 'image')
            ->values();
    }
@endphp

@if($isAnnouncement && $downloadables->isNotEmpty())

    <style>
        .floating-download-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 20;
        }

        .floating-download-btn .download-trigger {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #0f7c2e;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
            transition: background .2s;
        }
        .floating-download-btn .download-trigger:hover { background: #0a5e22; }
        .floating-download-btn .download-trigger svg { flex-shrink: 0; }

        .floating-download-btn .download-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 4px 16px rgba(0,0,0,.15);
            min-width: 220px;
            overflow: hidden;
        }
        .floating-download-btn.open .download-dropdown { display: block; }

        .floating-download-btn .download-dropdown a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #f0f0f0;
            transition: background .15s;
            word-break: break-word;
        }
        .floating-download-btn .download-dropdown a:last-child { border-bottom: none; }
        .floating-download-btn .download-dropdown a:hover { background: #f5f5f5; color: #0f7c2e; }
        .floating-download-btn .download-dropdown a svg { flex-shrink: 0; color: #0f7c2e; }

        .floating-download-btn .download-single {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #0f7c2e;
            color: #fff;
            border-radius: 6px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
            transition: background .2s;
        }
        .floating-download-btn .download-single:hover { background: #0a5e22; color: #fff; }
    </style>

    <div class="floating-download-btn" id="floatingDownloadBtn">

        @if($downloadables->count() === 1)

            {{-- ── Single file: direct download link ── --}}
            @php
                $only     = $downloadables->first();
                $filePath = $only->storage_path;
                $fileName = $only->file_name ?? basename($filePath);
            @endphp

            <a href="{{ asset('storage/' . $filePath) }}"
               download="{{ $fileName }}"
               class="download-single"
               title="Download {{ $fileName }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.1a.5.5 0 0 1 1 0v2.1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.1a.5.5 0 0 1 .5-.5"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                </svg>
                Download
            </a>

        @else

            {{-- ── Multiple files: dropdown ── --}}
            <button class="download-trigger" id="downloadTrigger" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.1a.5.5 0 0 1 1 0v2.1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.1a.5.5 0 0 1 .5-.5"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                </svg>
                Downloads ({{ $downloadables->count() }})
            </button>

            <div class="download-dropdown" id="downloadDropdown">
                @foreach($downloadables as $file)
                    @php
                        $filePath = $file->storage_path;
                        $fileName = $file->file_name ?? basename($filePath);
                        $fileKind = $file->kind ?? 'other';
                    @endphp

                    <a href="{{ asset('storage/' . $filePath) }}"
                       download="{{ $fileName }}"
                       title="{{ $fileName }}">

                        @if($fileKind === 'document')
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1L14 5.5z"/>
                            </svg>
                        @elseif($fileKind === 'video')
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M0 12V4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2m6-3.696v2.392a.5.5 0 0 0 .765.424l2.5-1.196a.5.5 0 0 0 0-.848l-2.5-1.196A.5.5 0 0 0 6 8.304"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.1a.5.5 0 0 1 1 0v2.1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.1a.5.5 0 0 1 .5-.5"/>
                                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                            </svg>
                        @endif

                        {{ $fileName }}
                    </a>
                @endforeach
            </div>

        @endif

    </div>

    <script>
        (function () {
            const wrapper = document.getElementById('floatingDownloadBtn');
            const trigger = document.getElementById('downloadTrigger');

            if (!trigger) return; // single-file mode, no JS needed

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                wrapper.classList.toggle('open');
            });

            document.addEventListener('click', function () {
                wrapper.classList.remove('open');
            });
        })();
    </script>

@endif

