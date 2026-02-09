<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Memos;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use App\Helpers\KodeMemoHelper;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Helpers\TrackHistoryHelper;

class MemoAdministrasiController extends Controller
{
     /* =====================================================
     * INDEX (APPROVED + PENDING)
     * ===================================================== */
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

         $pendingQuery = Memos::where('memos.tipe_memo', 'Administrasi')
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

    /* ===============================
     * RETURN VIEW
     * =============================== */
    return view('memo_administrasi', [
            'data'        => $approvedData,
            'pendingData' => $pendingData,
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
    $memo->nomor          = KodeMemoHelper::generate(
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

    $memo->user_id    = $user->nik;              // PEMBUAT
    $memo->requested_by = $user->nik;            // PENGAJU
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
     * UPDATE
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

        $fileName = $request->file('file_dokumen')->store('dokumen', 'public');
        $pendingChanges['file_dokumen'] = basename($fileName);
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

/* =====================================================
     * DELETE
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

public function downloadPdf($id)
    {
        $user = Auth::user();

        // Ambil PIN plain-text dari kolom 'pin'
        $pdfPin = $user->pin;

        if (!$pdfPin) {
            abort(403, "Anda belum memiliki PIN untuk membuka PDF");
        }

        // Ambil record memo
        $memo = Memos::findOrFail($id);

        // Lokasi file di storage/public/dokumen
        $sourceFile = storage_path("app/public/dokumen/" . $memo->file_dokumen);

        if (!file_exists($sourceFile)) {
            abort(404, "File tidak ditemukan");
        }

        // Load PDF menggunakan FPDI
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($sourceFile);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $template = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($template);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($template);
        }

        // Proteksi PDF menggunakan PIN dari user
        $pdf->SetProtection([], $pdfPin);

        // Kirim file sebagai download
        return response($pdf->Output($memo->nomor . '.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$memo->nomor.'.pdf"');
    }
}