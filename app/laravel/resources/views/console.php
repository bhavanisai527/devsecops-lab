<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Your original command — untouched
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================
// VULNERABILITY — Artisan command that runs raw shell input
// Try: php artisan run-report --filter="; cat /etc/passwd"
// Fix later: never pass user input to shell_exec / exec
// ============================================================
Artisan::command('run-report {--filter=}', function () {
    $filter = $this->option('filter');

    // VULN: command injection — $filter is passed directly to shell
    $output = shell_exec("grep -r '$filter' storage/logs/laravel.log");

    $this->line($output ?? 'No results.');
})->purpose('Search application logs');