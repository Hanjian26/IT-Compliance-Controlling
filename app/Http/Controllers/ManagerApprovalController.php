<?php

namespace App\Http\Controllers;

use App\Models\Memos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ManagerApprovalController extends Controller
{
    /**
     * APPROVE MEMO
     */
    public function approve($id)
    {
        $memo = Memos::findOrFail($id);
        $user = Auth::user();

        // Guard: hanya memo pending
        if ($memo->status !== 'pending') {
            abort(400, 'Memo sudah diproses');
        }

        switch ($memo->action_type) {

            /**
             * CREATE
             * Nomor SUDAH ADA (dibuat saat create)
             */
            case 'create':
                $memo->update([
                    'status'      => 'approved',
                    'approved_by' => $user->nik,
                    'approved_at' => now(),
                ]);
                break;

            /**
             * UPDATE
             * Terapkan perubahan dari pending_changes
             */
            case 'update':
                $memo->update(array_merge(
                    $memo->pending_changes ?? [],
                    [
                        'pending_changes' => null,
                        'status'          => 'approved',
                        'approved_by'     => $user->nik,
                        'approved_at'     => now(),
                    ]
                ));
                break;

            /**
             * DELETE
             * Hapus file & data SETELAH approve
             */
            case 'delete':
                if ($memo->file_dokumen) {
                    Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
                }

                $memo->delete();

                return back()->with('success', 'Memo berhasil dihapus');
        }

        return back()->with('success', 'Approval berhasil');
    }

    /**
     * REJECT MEMO
     * (Sesuai requirement Anda: reject = delete fisik)
     */
    public function reject($id)
    {
        $memo = Memos::findOrFail($id);

        // Guard tambahan (opsional tapi aman)
        if ($memo->status !== 'pending') {
            abort(400, 'Memo sudah diproses');
        }

        if ($memo->file_dokumen) {
            Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
        }

        $memo->delete();

        return back()->with('warning', 'Memo ditolak dan dihapus');
    }
}