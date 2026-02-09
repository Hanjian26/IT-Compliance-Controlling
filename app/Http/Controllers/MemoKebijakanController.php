<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Memos;
use App\Helpers\KodeMemoHelper;
<<<<<<< HEAD
use App\Helpers\TrackHistoryHelper;
use Carbon\Carbon;
=======
use App\Models\TrackHistory;

>>>>>>> cc65200a9547c5b0516d493e2bce8307619226f1

class MemoKebijakanController extends Controller
{
    /* =====================================================
     * INDEX (APPROVED + PENDING)
     * ===================================================== */
    public function index(Request $request)
    {
        $user = Auth::user();

        /* ===============================
         * DATA APPROVED (TABEL UTAMA)
         * =============================== */
        $approvedQuery = Memos::where('tipe_memo', 'Kebijakan')
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
         * DATA PENDING (POPUP)
         * =============================== */
        $pendingQuery = Memos::where('memos.tipe_memo', 'Kebijakan')
            ->where('memos.status', 'pending')
            ->leftJoin('users as requester', 'memos.requested_by', '=', 'requester.nik')
            ->select(
                'memos.*',
                'requester.nama as nama_pengaju'
            );

        /*
        |--------------------------------------------------------------------------
        | RULE VISIBILITY
        |--------------------------------------------------------------------------
        | level = 1  → lihat SEMUA pending
        | manager    → lihat pending bawahan
        | bawahan    → lihat pending yang dia ajukan
        */
        if ($user->level != 1) {
            if ($user->is_manager) {
                $pendingQuery->where('memos.manager_id', $user->nik);
            } else {
                $pendingQuery->where('memos.requested_by', $user->nik);
            }
        }

        $pendingData = $pendingQuery
            ->orderBy('memos.created_at', 'desc')
            ->get();

        return view('memo_kebijakan', [
            'data'        => $approvedData,
            'pendingData' => $pendingData,
            'isAdmin'     => $user->level == 1,
            'isManager'   => $user->is_manager
        ]);
    }

    /* =====================================================
     * STORE (CREATE MEMO)
     * ===================================================== */
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
        $memo->nomor          = KodeMemoHelper::generate(
        $request->tipe_memo,
        $request->tanggal_terbit
        );

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->storeAs('dokumen', $filename, 'public');
            $memo->file_dokumen = $filename;
        }

<<<<<<< HEAD
        $memo->user_id    = $user->nik;              // PEMBUAT
        $memo->requested_by = $user->nik;            // PENGAJU
        $memo->manager_id = $user->manager_id ?? $user->nik;
        $memo->action_type = 'create';

        if ($user->manager_id) {
            $memo->status = 'pending';
        } else {
            $memo->status = 'approved';
            $memo->approved_by = $user->nik;
            $memo->approved_at = now();
        }

        $memo->save();

        TrackHistoryHelper::log(
            'mengajukan penambahan memo',
            $memo->nomor,
            ucfirst($memo->status)
        );

        return back()->with(
            'success',
            $memo->status === 'pending'
                ? 'Memo dikirim dan menunggu approval'
                : 'Memo berhasil disimpan'
        );
    }

    /* =====================================================
     * EDIT (AJAX)
     * ===================================================== */
=======
        $memo->save();
        $this->recordHistory($memo, 'menambahkan', 'Masih Develop (Harusnya Pending)');



        activity('memo_kebijakan')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'create',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
            ])
            ->log('Menambahkan memo kebijakan');

        return redirect()->back()->with('success', 'Memo berhasil ditambahkan!');
    }

   public function destroy($id)
{
    $memo = Memos::findOrFail($id);
    // Hapus file jika ada
    if ($memo->file_dokumen && Storage::disk('public')->exists('dokumen/' . $memo->file_dokumen)) {
        Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
    }

    $memo->delete();
    $this->recordHistory($memo, 'menghapus', 'Masih Develop (Harusnya Pending)');


    return redirect()->route('memo.index')->with('success', 'Dokumen berhasil dihapus.');
}

>>>>>>> cc65200a9547c5b0516d493e2bce8307619226f1
    public function edit($id)
    {
        $memo = Memos::findOrFail($id);

        $data = $memo->pending_changes
            ? array_merge(
                $memo->only(['tipe_memo','scope_memo','nomor','tanggal_terbit','perihal']),
                $memo->pending_changes
            )
            : $memo->toArray();

        return response()->json([
            'id'             => $memo->id,
            'tipe_memo'      => $data['tipe_memo'],
            'scope_memo'     => $data['scope_memo'],
            'nomor'          => $memo->nomor,
            'tanggal_terbit' => Carbon::parse($data['tanggal_terbit'])->format('Y-m-d'),
            'perihal'        => $data['perihal'],
        ]);
    }

    /* =====================================================
     * UPDATE (REQUEST / APPROVE)
     * ===================================================== */
public function update(Request $request, $id)
{
    $memo = Memos::findOrFail($id);
    $user = Auth::user(); // user yang sedang login, misal sandi

    // pastikan memo belum diproses
    if ($memo->status !== 'approved') {
        return back()->with('warning', 'Memo sedang dalam proses atau belum bisa diedit');
    }

    // siapkan perubahan pending
    $pendingChanges = $request->only([
        'perihal',
        'scope_memo',
        'tanggal_terbit',
        'file_dokumen'
    ]);

    // jika ada file baru, simpan di storage
    if ($request->hasFile('file_dokumen')) {
        // hapus file lama jika ada
        if ($memo->file_dokumen) {
            Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
        }

<<<<<<< HEAD
        $fileName = $request->file('file_dokumen')->store('dokumen', 'public');
        $pendingChanges['file_dokumen'] = basename($fileName);
=======
        $memo->save();
        $this->recordHistory($memo, 'mengubah', 'Masih Develop (Harusnya Pending)'); // misal $memo->status = 'Pending'
        activity('memo_kebijakan')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'update',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
            ])
            ->log('Memperbarui memo kebijakan');

        return redirect()->route('memo.index')->with('success', 'Memo berhasil diperbarui!');
>>>>>>> cc65200a9547c5b0516d493e2bce8307619226f1
    }

      TrackHistoryHelper::log(
                'mengajukan perubahan memo',
                $memo->nomor,
                'Pending'
            );

    // update memo dengan pending_changes
    $memo->update([
        'pending_changes' => $pendingChanges,
        'status'         => 'pending',
        'action_type'    => 'update',
        'requested_by'   => $user->nik, // ⬅️ pengaju sekarang
    ]);

    return back()->with('success', 'Permintaan perubahan berhasil dikirim ke manager');
}

<<<<<<< HEAD

    /* =====================================================
     * DELETE (REQUEST / APPROVE)
     * ===================================================== */
    public function destroy($id)
    {
        $memo = Memos::findOrFail($id);
        $user = Auth::user();

        if ($user->level !== 1) {
            abort(403);
        }

        if (!$user->is_manager && $user->manager_id) {

            if ($memo->status === 'pending' && $memo->action_type === 'delete') {
                return back()->with('warning', 'Penghapusan sudah menunggu approval');
            }

            $memo->update([
                'status'       => 'pending',
                'action_type'  => 'delete',
                'requested_by' => $user->nik,
            ]);

            TrackHistoryHelper::log(
                'mengajukan penghapusan memo',
                $memo->nomor,
                'Pending'
            );

            return back()->with('info', 'Permintaan hapus menunggu approval');
        }

        if ($memo->file_dokumen) {
            Storage::disk('public')->delete('dokumen/'.$memo->file_dokumen);
        }

        TrackHistoryHelper::log(
            'menghapus memo',
            $memo->nomor,
            'Approved'
        );

        $memo->delete();

        return back()->with('success', 'Memo berhasil dihapus');
    }

    /* =====================================================
     * GENERATE NOMOR (AJAX)
     * ===================================================== */
    public function generateNomor(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
        ]);

        return response()->json([
            'nomor' => KodeMemoHelper::generate('Kebijakan', $request->tanggal)
        ]);
    }
=======
private function recordHistory($memo, $action, $status)
{
    TrackHistory::create([
        'tanggal_pengajuan' => now()->toDateString(),
        'nik' => Auth::user()->nik,
        'nama' => Auth::user()->nama,
        'perihal' => Auth::user()->nama . " " . $action . " Data Memo Kebijakan No: " . $memo->nomor,
        'status' => $status, // Pending, Rejected, Approved
        'created_at' => now(),
    ]);
}

>>>>>>> cc65200a9547c5b0516d493e2bce8307619226f1
}