<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ isset($dj) ? 'DJ ' . $dj->name : 'Video' }}</title>
    @if (!empty($videoUrl))
        <meta property="og:title" content="{{ 'DJ ' . ($dj->name ?? 'Performance') }}" />
        <meta property="og:description" content="Watch the DJ performance." />
        <meta property="og:type" content="video.other" />
        <meta property="og:video" content="{{ $videoUrl }}" />
        @if (!empty($posterUrl))
            <meta property="og:image" content="{{ $posterUrl }}" />
        @endif
    @endif
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            background: #000
        }

        .video-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 16px
        }

        video {
            max-width: 100%;
            max-height: 100%;
            border-radius: 6px;
            box-shadow: 0 6px 30px rgba(0, 0, 0, .6)
        }

        .msg {
            color: #fff;
            text-align: center;
            font-family: system-ui, Segoe UI, Roboto, Helvetica, Arial, sans-serif
        }
    </style>
</head>

<body>
    <div class="video-wrap">
        @if (!empty($hlsUrl) || !empty($videoUrl))
            <video id="sharedVideo" controls playsinline poster="{{ $posterUrl ?? '' }}">
                @if (empty($hlsUrl) && !empty($videoUrl))
                    <source src="{{ $videoUrl }}" type="video/mp4">
                @endif
                Your browser does not support the video tag.
            </video>
            <script src="https://cdn.jsdelivr.net/npm/hls.js@1"></script>
            <script>
                (function() {
                    const hlsUrl = @json($hlsUrl ?? null);
                    const fallback = @json($videoUrl ?? null);
                    const video = document.getElementById('sharedVideo');
                    if (!video) return;

                    if (hlsUrl) {
                        try {
                            if (window.Hls && window.Hls.isSupported()) {
                                const hls = new window.Hls();
                                hls.loadSource(hlsUrl);
                                hls.attachMedia(video);
                            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                                video.src = hlsUrl;
                            } else if (fallback) {
                                video.src = fallback;
                            }
                        } catch (e) {
                            console.error('HLS playback error, falling back:', e);
                            if (fallback) video.src = fallback;
                        }
                    }
                    try {
                        video.load();
                    } catch (e) {
                        /* ignore */ }
                })();
            </script>
        @else
            <div class="msg">
                <p>Video not available.</p>
            </div>
        @endif
    </div>
</body>

</html>
