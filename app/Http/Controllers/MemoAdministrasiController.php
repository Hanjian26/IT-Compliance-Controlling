<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Memos;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use App\Helpers\KodeMemoHelper;

class MemoAdministrasiController extends Controller
{
    public function index(Request $request)
{
    $user = Auth::user();
    /* ===============================
     * DATA APPROVED (UNTUK TABEL UTAMA)
     * =============================== */
     $approvedQuery = Memos::where('tipe_memo', 'Administrasi')
        ->where('status', 'approved');

     if ($request->filled('search')) {
        $approvedQuery->where(function ($q) use ($request) {
            $q->where('scope_memo', 'like', "%{$request->search}%")
              ->orWhere('nomor', 'like', "%{$request->search}%")
              ->orWhere('perihal', 'like', "%{$request->search}%");
        });
    }

        $approvedData = $approvedQuery
        ->orderBy('tanggal_terbit', 'desc')
        ->paginate(5);

     /* ===============================
     * DATA PENDING (UNTUK POPUP)
     * =============================== */
    $pendingQuery = Memos::where('tipe_memo', 'Administrasi')
        ->where('status', 'pending');

    // Admin bawahan → hanya memo miliknya
    if ($user->manager_id && !$user->is_manager) {
        $pendingQuery->where('user_id', $user->nik);
    }

    // Manager → memo bawahan
    if ($user->is_manager) {
        $pendingQuery->where('manager_id', $user->nik);
    }

    $pendingData = $pendingQuery
        ->orderBy('created_at', 'desc')
        ->get();

    /* ===============================
     * RETURN VIEW
     * =============================== */
    return view('memo_administrasi', [
        'data'        => $approvedData,   // tabel utama
        'pendingData' => $pendingData,    // popup pending
        'isAdmin'     => $user->level == 1,
        'isManager'   => $user->is_manager
    ]);
}

 public function store(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'tipe_memo'      => 'required',
        'scope_memo'     => 'required',
        'tanggal_terbit' => 'required|date',
        'perihal'        => 'nullable',
        'file_dokumen'   => 'nullable|mimes:pdf,doc,docx,zip|max:10240',
    ]);

    $memo = new Memos();
    $memo->tipe_memo      = $request->tipe_memo;
    $memo->scope_memo     = $request->scope_memo;
    $memo->tanggal_terbit = $request->tanggal_terbit;
    $memo->perihal        = $request->perihal;

    $memo->nomor = KodeMemoHelper::generate(
        $request->tipe_memo,
        $request->tanggal_terbit
    );

    // Upload file
    if ($request->hasFile('file_dokumen')) {
        $file = $request->file('file_dokumen');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('dokumen', $filename, 'public');
        $memo->file_dokumen = $filename;
    }

    $memo->user_id    = $user->nik;
    $memo->manager_id = $user->manager_id ?? $user->nik; 
    $memo->action_type = 'create';

    // Approval logic
    if ($user->manager_id) {
        $memo->status = 'pending';
    } else {
        $memo->status = 'approved';
        $memo->approved_by = $user->nik;
        $memo->approved_at = now();
    }

    $memo->save();

    return back()->with(
        'success',
        $user->manager_id
            ? 'Memo dikirim dan menunggu approval atasan'
            : 'Memo berhasil disimpan'
    );
}


  public function edit($id)
{
    $memo = Memos::findOrFail($id);

    return response()->json([
        'id'             => $memo->id,
        'tipe_memo'      => $memo->tipe_memo,
        'scope_memo'     => $memo->scope_memo,
        'nomor'          => $memo->nomor,
        'tanggal_terbit' => $memo->tanggal_terbit
                                ? $memo->tanggal_terbit->format('Y-m-d')
                                : null,
        'perihal'        => $memo->perihal,
    ]);
}

  
    /* =====================================================
     * UPDATE
     * ===================================================== */
public function update(Request $request, $id)
{
    $memo = Memos::findOrFail($id);
    $user = Auth::user();

    $request->validate([
        'scope_memo'     => 'required',
        'tanggal_terbit' => 'required|date',
        'perihal'        => 'nullable',
        'file_dokumen'   => 'nullable|mimes:pdf,doc,docx,zip|max:10240',
    ]);

    /*
    |--------------------------------------------------------------------------
    | ADMIN BAWAHAN → REQUEST UPDATE (PENDING)
    |--------------------------------------------------------------------------
    */
    if (!$user->is_manager && !is_null($user->manager_id)) {

        $pendingChanges = [
            'scope_memo'     => $request->scope_memo,
            'tanggal_terbit' => $request->tanggal_terbit,
            'perihal'        => $request->perihal,
        ];

        // Jika upload file, simpan dulu (BELUM replace file lama)
        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('dokumen', $filename, 'public');

            $pendingChanges['file_dokumen'] = $filename;
        }

        $memo->update([
            'pending_changes' => $pendingChanges,
            'status'          => 'pending',
            'action_type'     => 'update',
        ]);

        return back()->with('info', 'Perubahan menunggu approval atasan');
    }

    /*
    |--------------------------------------------------------------------------
    | MANAGER → UPDATE LANGSUNG
    |--------------------------------------------------------------------------
    */
    if ($request->hasFile('file_dokumen')) {
        if ($memo->file_dokumen) {
            Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
        }

        $file = $request->file('file_dokumen');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('dokumen', $filename, 'public');

        $memo->file_dokumen = $filename;
    }

    $memo->update([
        'scope_memo'     => $request->scope_memo,
        'tanggal_terbit' => $request->tanggal_terbit,
        'perihal'        => $request->perihal,
        'status'         => 'approved',
        'approved_by'    => $user->nik,
        'approved_at'    => now(),
        'pending_changes'=> null,
    ]);

    return back()->with('success', 'Memo berhasil diperbarui');
}

/* =====================================================
     * DELETE
     * ===================================================== */
public function destroy($id)
{
    $memo = Memos::findOrFail($id);
    $user = Auth::user();

    // 🔐 Hanya admin level 1
    if ($user->level !== 1) {
        abort(403, 'Anda tidak berhak menghapus memo ini');
    }

    // 👨‍💼 Admin BAWAHAN (punya atasan & bukan manager)
    if (!$user->is_manager && !is_null($user->manager_id)) {

        // Cegah double request
        if ($memo->status === 'pending' && $memo->action_type === 'delete') {
            return back()->with('warning', 'Menghapus Memo sudah menunggu approval');
        }

        $memo->update([
            'status'      => 'pending',
            'action_type' => 'delete',
        ]);

        return back()->with('info', 'Permintaan hapus menunggu approval atasan');
    }

    // 👑 MANAGER → DELETE FINAL
    if ($memo->file_dokumen) {
        Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
    }

    $memo->delete();

    return back()->with('success', 'Memo berhasil dihapus');
}


public function generateNomor(Request $request)
{
    $request->validate([
        'tanggal' => 'required|date',
    ]);

    $nomor = KodeMemoHelper::generate(
        'Administrasi',
        $request->tanggal
    );

    return response()->json([
        'nomor' => $nomor
    ]);
}

public function pendingList()
{
    $user = Auth::user();

    $query = Memos::where('status', 'pending');

    // Admin bawahan → hanya memo miliknya
    if ($user->manager_id) {
        $query->where('user_id', $user->nik);
    }

    // Manager → memo bawahan
    if ($user->is_manager) {
        $query->where('manager_id', $user->nik);
    }

    $data = $query
        ->orderBy('created_at', 'desc')
        ->paginate(5);

    return view('memo_pending', [
        'data' => $data,
        'isManager' => $user->is_manager
    ]);
}

}