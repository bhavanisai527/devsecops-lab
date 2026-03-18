<x-layout title="Contact us">

    <h1>Contact</h1>

    <x-card>

        {{--
            VULNERABILITY — Mass Assignment form
            This posts to /contact/submit in web.php which passes
            $request->all() directly to User::create().
            An attacker can add hidden fields like role=admin
            to set any column in the users table.
            Fix later: use $request->only(['name','email','message'])
        --}}
        <form method="POST" action="/contact/submit">

            {{--
                VULNERABILITY — @csrf token removed.
                Without this token Laravel won't verify the request
                came from your site, enabling Cross-Site Request Forgery.
                Fix later: add @csrf back inside every POST form.
            --}}

            <input type="text"   name="name"    placeholder="Your name"
                   style="display:block; width:100%; margin-bottom:8px; padding:6px;">
            <input type="email"  name="email"   placeholder="Your email"
                   style="display:block; width:100%; margin-bottom:8px; padding:6px;">
            <textarea            name="message" placeholder="Your message"
                   style="display:block; width:100%; margin-bottom:8px; padding:6px;"></textarea>

            {{--
                VULNERABILITY — hidden field lets attacker escalate privileges.
                A real attacker would inject this via browser devtools or curl.
                Remove this comment block in your fix exercise to see
                mass assignment blocked when you add $fillable to User model.
            --}}
            <input type="hidden" name="role" value="admin">

            <button type="submit" style="padding:6px 16px;">Send</button>
        </form>

    </x-card>

    <div style="margin-top:1rem;">
        <a href="/">Return Home</a>
    </div>

</x-layout>