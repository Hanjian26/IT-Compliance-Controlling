<?php

namespace App\Helpers;

use App\Models\Memos;
use Carbon\Carbon;

class KodeMemoHelper
{
    /**
     * Generate kode memo otomatis berdasarkan tipe memo.
     *
     * Format:
     * - Administrasi : 0001/ITC/IX/2024
     * - Permintaan Data : ITC/001/XI/2024
     * - Audit : 0001/ITC-A/III/2025
     * - Penemuan : 0001/ITC-B/II/2025
     * - Kebijakan : 0001_ITC_IX_2024
     * - All IT : 004/ITC-IDM-HO/II/2025 
     */
    public static function generate($tipe = 'Administrasi')
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        // Konversi bulan ke angka romawi
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $romanMonth = $romanMonths[$month];

        // Set default values
        $kodeTengah = 'ITC';
        $digit = 4;
        $separator = '/';

        switch (strtolower($tipe)) {
            case 'administrasi':
                // Format: 0001/ITC/XI/2025
                $kodeTengah = 'ITC';
                $digit = 4;
                $separator = '/';
                break;

            case 'permintaan data':
                // Format: ITC/001/XI/2025
                $kodeTengah = 'ITC';
                $digit = 3;
                $separator = '/';
                break;

            case 'All IT':
                // Format: 004/ITC-IDM-HO/II/2025 (pakai underscore)
                $kodeTengah = 'ITC-IDM-HO';
                $digit = 3;
                $separator = '/';
                break;

            case 'audit':
                // Format: 0001/ITC-A/XI/2025
                $kodeTengah = 'ITC-A';
                $digit = 4;
                $separator = '/';
                break;

            case 'penemuan':
                // Format: 0001/ITC-B/XI/2025
                $kodeTengah = 'ITC-B';
                $digit = 4;
                $separator = '/';
                break;

            case 'kebijakan':
                // Format: 0001_ITC_XI_2025 (pakai underscore)
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

        // Ambil memo terakhir dengan tipe dan tahun yang sama
        $last = Memos::where('tipe_memo', $tipe)
            ->whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();

        // Dapatkan nomor terakhir dari format sebelumnya
        if ($last && preg_match('/(\d{3,4})/', $last->nomor, $match)) {
            $lastNumber = (int) $match[1];
        } else {
            $lastNumber = 0;
        }

        // Tambah 1 untuk nomor baru
        $newNumber = $lastNumber + 1;
        $formattedNumber = str_pad($newNumber, $digit, '0', STR_PAD_LEFT);

        // Bangun format berdasarkan tipe
        switch (strtolower($tipe)) {
            case 'administrasi':
            case 'audit':
            case 'penemuan':
                return "{$formattedNumber}{$separator}{$kodeTengah}{$separator}{$romanMonth}{$separator}{$year}";

            case 'permintaan data':
                return "{$kodeTengah}{$separator}{$formattedNumber}{$separator}{$romanMonth}{$separator}{$year}";
            
            case 'All IT':
                return "{$kodeTengah}{$separator}{$formattedNumber}{$separator}{$romanMonth}{$separator}{$year}";

            case 'kebijakan':
                return "{$formattedNumber}{$separator}{$kodeTengah}{$separator}{$romanMonth}{$separator}{$year}";

            default:
                return "{$formattedNumber}{$separator}{$kodeTengah}{$separator}{$romanMonth}{$separator}{$year}";
        }
    }
}