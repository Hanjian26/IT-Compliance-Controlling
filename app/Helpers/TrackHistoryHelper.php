<?php

namespace App\Helpers;

use App\Models\TrackHistory;
use Illuminate\Support\Facades\Auth;

class TrackHistoryHelper
{
    public static function log(
        string $aksi,
        string $nomorMemo,
        string $status,
        ?string $namaPengaju = null // ← OPTIONAL
    ) {
        $user = Auth::user(); // pelaku aksi (admin / manager)

        $perihal = "{$user->nama} {$aksi} Data Memo Kebijakan No: {$nomorMemo}";

        if ($namaPengaju) {
            $perihal .= " yang diajukan oleh {$namaPengaju}";
        }

        TrackHistory::create([
            'tanggal_pengajuan' => now()->toDateString(),
            'nik'               => $user->nik,
            'nama'              => $user->nama,
            'perihal'           => $perihal,
            'status'            => $status,
            'created_at'        => now(),
        ]);
    }
}