<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cek status pembayaran pending ke Midtrans secara berkala.
// Order yang sudah expired akan otomatis di-set 'expired' & stoknya dikembalikan.
Schedule::command('payments:check-pending')
    ->everyFiveMinutes()
    ->withoutOverlapping();
