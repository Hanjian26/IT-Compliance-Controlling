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
            $q->Where('id', 'LIKE', "%{$search}%")
              ->orWhere('tanggal_pengajuan', 'LIKE', "%{$search}%")
              ->orWhere('nik', 'LIKE', "%{$search}%")
              ->orWhere('nama', 'LIKE', "%{$search}%")
              ->orWhere('perihal', 'LIKE', "%{$search}%")
              ->orWhere('status', 'LIKE', "%{$search}%");
        });
    }

            $data = $query->orderBy('tanggal_pengajuan', 'asc')
                        ->paginate(10)
                        ->withQueryString(); // agar search tetap saat paging

            return view('track_history', compact('data'));
    }
}