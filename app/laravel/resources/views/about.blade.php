<x-layout title="About us">

    <h1>About us</h1>

    {{--
        VULNERABILITY — Reflected XSS
        The ?message= query param is printed unescaped.
        Try: /about?message=<script>alert('reflected XSS')</script>
        Fix later: {{ request('message') }} escapes HTML automatically
    --}}
    @if(request('message'))
        <div class="alert" style="color:red;">
            {!! request('message') !!}
        </div>
    @endif

    {{--
        VULNERABILITY — Path traversal entry point
        This form posts to the /file route in web.php which reads
        any file on the server with no sanitisation.
        Try: entering  ../../../etc/passwd  in the input
        Fix later: remove this form, or validate against an allowlist
    --}}
    <x-card>
        <p style="font-size:13px; color:#555;">Read a storage file:</p>
        <form method="GET" action="/file">
            <input type="text" name="name" placeholder="e.g. logs/laravel.log"
                   style="width:100%; padding:6px; margin-bottom:8px;">
            <button type="submit" style="padding:6px 12px;">Read file</button>
        </form>
    </x-card>

    <div>
        <a href="/">Return Home</a>
    </div>

</x-layout>