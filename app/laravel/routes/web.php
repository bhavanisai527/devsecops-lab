<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// Your original routes — untouched
Route::view('/', 'welcome');
Route::view('/about', 'about');
Route::view('/contact', 'contact');

// ============================================================
// VULNERABILITY #1 — SQL Injection
// Accessible from the search form on your welcome page.
// Try: /search?name=' OR '1'='1
// Fix later: DB::select('SELECT * FROM users WHERE name = ?', [$name])
// ============================================================
Route::get('/search', function (Request $request) {
    $name = $request->input('name');

    // VULN: raw SQL with unsanitised user input
    $results = DB::select("SELECT * FROM users WHERE name = '$name'");

    return response()->json($results);
});

// ============================================================
// VULNERABILITY #2 — Path Traversal
// Try: /file?name=../../../etc/passwd
// Fix later: validate $name against an allowlist of safe filenames
// ============================================================
Route::get('/file', function (Request $request) {
    $name = $request->input('name');

    // VULN: no path sanitisation — can read any file on the server
    $contents = file_get_contents(storage_path($name));

    return response($contents, 200, ['Content-Type' => 'text/plain']);
});

// ============================================================
// VULNERABILITY #3 — Debug / Config Dump
// Exposes APP_KEY, DB credentials, AWS keys — everything in config/
// Fix later: delete this route entirely
// ============================================================
Route::get('/debug/config', function () {
    // VULN: dumps all Laravel config values including secrets
    return response()->json(config()->all());
});

// ============================================================
// VULNERABILITY #4 — Mass Assignment via contact form
// Try posting: {"name":"x","role":"admin","email":"x@x.com"}
// Fix later: use $request->only(['name','email','message'])
// ============================================================
Route::post('/contact/submit', function (Request $request) {
    // VULN: no validation, entire request passed to create()
    $entry = \App\Models\User::create($request->all());

    return response()->json(['saved' => $entry->id]);
});