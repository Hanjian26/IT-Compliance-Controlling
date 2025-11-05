<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\TindakAuditDB;
use App\Mail\AuditReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;

class SendAuditH7Reminder extends Command
{
    protected $signature = 'audit:reminder-pending';
    protected $description = 'Kirim email pengingat otomatis untuk audit Pending dengan kondisi: (1) tanggal selesai sudah melewati tanggal mulai, (2) H-10 sebelum tanggal mulai.';

    public function handle()
    {
        $today = Carbon::today();

        // 1️⃣ Kondisi 1: Tanggal selesai sudah melewati tanggal mulai dengan status Pending
        $auditsLate = TindakAuditDB::where('status', 'Pending')
            ->whereColumn('tanggal_selesai', '<', 'tanggal_mulai')
            ->get();

        // 2️⃣ Kondisi 2: Tanggal selesai H-10 dari tanggal mulai dengan status Pending
        $auditsH10 = TindakAuditDB::where('status', 'Pending')
            ->whereRaw('DATEDIFF(tanggal_selesai, tanggal_mulai) = 10')
            ->get();

        $total = $auditsLate->count() + $auditsH10->count();

        if ($total === 0) {
            $this->info('Tidak ada audit Pending yang memenuhi kondisi hari ini.');
            return;
        }

        // Kirim email untuk audit yang sudah lewat
        foreach ($auditsLate as $audit) {
            $this->sendReminder($audit, 'Tanggal selesai melewati tanggal mulai');
        }

        // Kirim email untuk audit H-10
        foreach ($auditsH10 as $audit) {
            $this->sendReminder($audit, 'H-10 dari tanggal mulai');
        }

        $this->info("Total {$total} email pengingat audit Pending berhasil dikirim.");
    }

    protected function sendReminder($audit, $jenis)
    {
        $emailTujuan = 'hanjian.listanto26@gmail.com'; // ganti sesuai kebutuhan

        try {
            Mail::to($emailTujuan)->send(new AuditReminderMail($audit, $jenis));
            Log::info("Email pengingat '{$jenis}' terkirim ke {$emailTujuan} untuk audit: {$audit->kegiatan}");
            $this->info("Email terkirim ke {$emailTujuan} untuk audit: {$audit->kegiatan} ({$jenis})");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim email '{$jenis}' ke {$emailTujuan}: " . $e->getMessage());
            $this->error("Gagal mengirim email ke {$emailTujuan}: " . $e->getMessage());
        }
    }

    // Jalankan otomatis setiap hari
// public static function schedule(Schedule $schedule): void
// {
//     $schedule->command(static::class)
//         ->dailyAt('13:20')
//         ->timezone('Asia/Jakarta');
// }

}