<x-layout title="Home">
 
    <h1>Hello World</h1>
 
    {{--
        VULNERABILITY — SQL Injection entry point
        This form hits GET /search in web.php which runs:
        DB::select("SELECT * FROM users WHERE name = '$name'")
        Try typing:  ' OR '1'='1  to dump all users
        Fix later: use parameterised queries in web.php
    --}}
    <x-card>
        <p style="font-size:13px; color:#555;">Search users:</p>
        <form method="GET" action="/search">
            <input type="text" name="name" placeholder="Enter a name..."
                   style="width:100%; padding:6px; margin-bottom:8px;">
            <button type="submit" style="padding:6px 12px;">Search</button>
        </form>
    </x-card>
 
    {{--
        VULNERABILITY — XSS via unescaped search term reflected back
        Whatever the user typed is printed raw into the page.
        Try: /search?name=<img src=x onerror=alert(1)>
        Fix later: {{ request('name') }} instead of {!! !!}
    --}}
    @if(request('name'))
        <p style="margin-top:1rem; font-size:13px; color:#555;">
            Showing results for: {!! request('name') !!}
        </p>
    @endif
 
    {{--
        VULNERABILITY — direct link to config dump in the UI
        Exposes APP_KEY, DB credentials, AWS keys to anyone who clicks it.
        Fix later: delete the /debug/config route and this link entirely.
    --}}
    <p style="margin-top:2rem; font-size:11px;">
        <a href="/debug/config" style="color:red;">⚠ debug: view config</a>
    </p>
 
</x-layout>