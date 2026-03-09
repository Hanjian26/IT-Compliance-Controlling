<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\TrackHistory;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;


class HistoryController extends Controller
{
 public function index(Request $request)
    {
        $query = TrackHistory::query();

if ($request->filled('search')) {
    $search = $request->search;

    $query->where(function ($q) use ($search) {
        $q->where('id', 'LIKE', "%{$search}%")
          ->orWhere('tanggal_pengajuan', 'LIKE', "%{$search}%")
          ->orWhere('nik', 'LIKE', "%{$search}%")
          ->orWhere('nama', 'LIKE', "%{$search}%")
          ->orWhere('perihal', 'LIKE', "%{$search}%")
          ->orWhere('status', 'LIKE', "%{$search}%");
    });
}

// FILTER TAHUN
if ($request->filled('year')) {
    $query->whereYear('created_at', $request->year);
}

    // Filter tanggal & waktu
    // Filter range tanggal
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereDate('created_at', '>=', $request->start_date)
              ->whereDate('created_at', '<=', $request->end_date);
    } elseif ($request->filled('start_date')) {
        $query->whereDate('created_at', '>=', $request->start_date);
    } elseif ($request->filled('end_date')) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

        $data = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('track_history', compact('data'));

        $data = $query->orderBy('tanggal_pengajuan', 'asc')
                      ->paginate(10)
                      ->withQueryString();

        return view('track_history', compact('data'));
    }
}