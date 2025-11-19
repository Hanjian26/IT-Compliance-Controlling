<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('audit:reminder-pending')
    ->dailyAt('10:20') //Ganti Sesuai yang di inginkan
    ->timezone('Asia/Jakarta'); // waktu sesuai WIB

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');