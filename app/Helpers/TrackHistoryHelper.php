<?php

namespace App\Helpers;

use App\Models\TrackHistory;
use Illuminate\Support\Facades\Auth;

class TrackHistoryHelper
{
    public static function log(
        string $aksi,
        string $tipeMemo,
        string $nomorMemo,
        string $status,
        ?string $namaPengaju = null
    ) {
        $user = Auth::user();

        $perihal = "{$user->nama} {$aksi} Memo {$tipeMemo} No: {$nomorMemo}";

        if ($namaPengaju) {
            $perihal .= " yang diajukan oleh {$namaPengaju}";
        }

        TrackHistory::create([
            'tanggal_pengajuan' => now()->toDateString(),
            'nik' => $user->nik,
            'nama' => $user->nama,
            'perihal' => $perihal,
            'status' => $status,
            'created_at' => now(),
        ]);
    }
}