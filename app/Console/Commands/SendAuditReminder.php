<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TindakAuditDB;
use App\Mail\AuditReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendAuditReminder extends Command
{
    protected $signature = 'audit:reminder-pending';
    protected $description = 'Kirim email pengingat otomatis untuk audit Pending: H-10 sebelum tanggal selesai, H+7 setelah tanggal selesai, dan H+14 setelah tanggal selesai.';

    public function handle()
    {
        $today = Carbon::today();

        // 🔹 Kondisi 1: H-10 sebelum tanggal selesai (belum jatuh tempo)
        $auditsH10 = TindakAuditDB::where('status', 'Pending')
            ->whereDate('tanggal_selesai', '=', $today->copy()->addDays(10))
            ->get();

        // 🔹 Kondisi 2: H+7 setelah tanggal selesai (sudah lewat 7 hari)
        $auditsH7 = TindakAuditDB::where('status', 'Pending')
            ->whereDate('tanggal_selesai', '=', $today->copy()->subDays(7))
            ->get();

        // 🔹 Kondisi 3: H+14 setelah tanggal selesai (sudah lewat 14 hari)
        $auditsH14 = TindakAuditDB::where('status', 'Pending')
            ->whereDate('tanggal_selesai', '=', $today->copy()->subDays(14))
            ->get();

        $total = $auditsH10->count() + $auditsH7->count() + $auditsH14->count();

        if ($total === 0) {
            $this->info('✅ Tidak ada audit Pending yang memenuhi kondisi hari ini.');
            return;
        }

        // Kirim email untuk setiap kondisi
        foreach ($auditsH10 as $audit) {
            $this->sendReminder($audit, 'Reminder H-10 sebelum tanggal selesai');
        }

        foreach ($auditsH7 as $audit) {
            $this->sendReminder($audit, 'Reminder H+7 setelah tanggal selesai');
        }

        foreach ($auditsH14 as $audit) {
            $this->sendReminder($audit, 'Reminder H+14 setelah tanggal selesai');
        }

        $this->info("📨 Total {$total} email pengingat audit Pending berhasil dikirim.");
    }

    /**
     * Fungsi untuk kirim email dan logging
     */
    protected function sendReminder($audit, $jenis)
    {
        // Pastikan field 'nama_divisi' ada di tabel TindakAuditDB
        $divisi = $audit->divisi ?? 'Tidak diketahui';

        // Ganti sesuai kebutuhan (atau gunakan $audit->email_penerima jika ada di DB)
        $emailTujuan = $audit->email ?? 'hanjian.listanto26@gmail.com'; // Sesuaikan email penerima

        try {
            // Kirim email (kamu bisa kirim juga data divisi ke Mailable)
            Mail::to($emailTujuan)->send(new AuditReminderMail($audit, $jenis));

            // Logging lengkap
            Log::info("Email '{$jenis}' terkirim ke {$emailTujuan} untuk audit: {$audit->kegiatan} | Divisi: {$divisi}");
            $this->info("✅ Email terkirim ke {$emailTujuan} untuk audit: {$audit->kegiatan} ({$jenis}) | Divisi: {$divisi}");
        } catch (\Exception $e) {
            Log::error("❌ Gagal mengirim email '{$jenis}' ke {$emailTujuan}: " . $e->getMessage());
            $this->error("Gagal mengirim email ke {$emailTujuan}: " . $e->getMessage());
        }
    }
}