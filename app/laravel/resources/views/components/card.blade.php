<div class="card" style="background: #e3e3e3; padding: 1rem; text-align: center;">

    {{--
        VULNERABILITY — XSS via {!! !!}
        If anything passed into this card comes from user input or the
        database, it will execute as raw HTML in the browser.
        Try passing: <script>alert('XSS from card')</script>
        Fix later: change back to {{ $slot }}
    --}}
    {!! $slot !!}

</div>