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

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'LIKE', "%{$search}%")
                  ->orWhere('nama', 'LIKE', "%{$search}%")
                  ->orWhere('perihal', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%");
            });
        }

        // Order by newest first
        $data = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('track_history', compact('data'));
    }
}