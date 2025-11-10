<?php

namespace App\Helpers;

use App\Models\Memos;
use Carbon\Carbon;

class KodeMemoHelper
{
    /**
     * Generate kode memo otomatis berdasarkan tipe memo.
     */
    public static function generate($tipe = 'Administrasi', $tanggal)
    {
        // Parse tanggal dari input user (YYYY-MM-DD)
        $date = Carbon::parse($tanggal);
        $year = $date->year;
        $month = $date->month;

        // Konversi bulan ke angka romawi
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $romanMonth = $romanMonths[$month];

        // Default format
        $kodeTengah = 'ITC';
        $digit = 4;
        $separator = '/';

        // Tentukan format berdasarkan tipe memo
        switch (strtolower($tipe)) {
            case 'administrasi':
                $kodeTengah = 'ITC';
                $digit = 4;
                $separator = '/';
                break;

            case 'permintaan data':
                $kodeTengah = 'ITC';
                $digit = 3;
                $separator = '/';
                break;

            case 'all it':
                $kodeTengah = 'ITC-IDM-HO';
                $digit = 3;
                $separator = '/';
                break;

            case 'audit':
                $kodeTengah = 'ITC-A';
                $digit = 4;
                $separator = '/';
                break;

            case 'penemuan':
                $kodeTengah = 'ITC-B';
                $digit = 4;
                $separator = '/';
                break;

            case 'kebijakan':
                $kodeTengah = 'ITC';
                $digit = 4;
                $separator = '_';
                break;

            default:
                $kodeTengah = 'ITC-X';
                $digit = 4;
                $separator = '/';
                break;
        }

        /**
         * AMAN DARI RESET
         * ---------------------------
         * Ambil memo terakhir BERDASARKAN TIPE SAJA
         * TANPA membatasi tahun.
         * 
         * Ini memastikan nomor selalu naik:
         * 0001, 0002, 0003, ... dst
         */
        $last = Memos::where('tipe_memo', $tipe)
            ->orderByDesc('id')
            ->first();

        // Ambil nomor terakhir (jika ada)
        if ($last && preg_match('/(\d{3,4})/', $last->nomor, $match)) {
            $lastNumber = (int) $match[1];
        } else {
            $lastNumber = 0; // jika belum ada memo
        }

        // Nomor berikutnya
        $newNumber = $lastNumber + 1;

        // Format nomor (padding 3 atau 4 digit)
        $formattedNumber = str_pad($newNumber, $digit, '0', STR_PAD_LEFT);

        // Build final format
        switch (strtolower($tipe)) {

            case 'administrasi':
            case 'audit':
            case 'penemuan':
                // Contoh: 0001/ITC/XI/2025
                return "{$formattedNumber}{$separator}{$kodeTengah}{$separator}{$romanMonth}{$separator}{$year}";

            case 'permintaan data':
                // Contoh: ITC/001/XI/2025
                return "{$kodeTengah}{$separator}{$formattedNumber}{$separator}{$romanMonth}{$separator}{$year}";

            case 'all it':
                // Contoh: ITC-IDM-HO/004/II/2025
                return "{$kodeTengah}{$separator}{$formattedNumber}{$separator}{$romanMonth}{$separator}{$year}";

            case 'kebijakan':
                // Contoh: 0001_ITC_XI_2025
                return "{$formattedNumber}{$separator}{$kodeTengah}{$separator}{$romanMonth}{$separator}{$year}";

            default:
                return "{$formattedNumber}{$separator}{$kodeTengah}{$separator}{$romanMonth}{$separator}{$year}";
        }
    }
}