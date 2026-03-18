@props([
    'title' => 'Laracasts'
])

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        nav > a { color: blue; }
        .alert { background: #fff3cd; padding: 8px 12px; font-size: 13px; }
    </style>
</head>
<body>

    <nav>
        <a href="/">Home</a>
        <a href="/about">About us</a>
        <a href="/contact">Contact</a>
        {{-- VULN: debug link should never appear in production --}}
        <a href="/debug/config" style="color:red;font-size:11px;">⚠ debug</a>
    </nav>

    {{--
        VULNERABILITY — XSS via {!! !!} (unescaped output)
        If $title comes from a query string or DB and contains
        <script>alert(1)</script> it will execute in the browser.
        Fix later: always use {{ }} which auto-escapes HTML.
    --}}
    <h1>{!! $title !!}</h1>

    <main>
        {{ $slot }}
    </main>

    {{--
        VULNERABILITY — Exposes Laravel/PHP version in a meta tag.
        Attackers use this to target known CVEs for that version.
        Fix later: remove this tag entirely.
    --}}
    <meta name="generator" content="Laravel/PHP {{ phpversion() }}">

</body>
</html>