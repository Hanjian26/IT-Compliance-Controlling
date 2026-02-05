<?php

namespace App\Http\Controllers;

use App\Models\Memos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ManagerApprovalController extends Controller
{
    /**
     * APPROVE
     */
    public function approve($id)
    {
        $memo = Memos::findOrFail($id);
        $user = Auth::user();

        if ($memo->status !== 'pending') {
            abort(400, 'Memo sudah diproses');
        }

        switch ($memo->action_type) {

            // ✅ CREATE → sahkan memo
            case 'create':
                $memo->update([
                    'status'      => 'approved',
                    'approved_by' => $user->nik,
                    'approved_at' => now(),
                ]);
                break;

            // ✅ UPDATE → terapkan perubahan
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

            // ✅ DELETE → hapus fisik
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
     * REJECT (KONTEKSTUAL)
     */
    public function reject($id)
    {
        $memo = Memos::findOrFail($id);

        if ($memo->status !== 'pending') {
            abort(400, 'Memo sudah diproses');
        }

        switch ($memo->action_type) {

            // ❌ CREATE → memo tidak pernah ada
            case 'create':
                if ($memo->file_dokumen) {
                    Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
                }
                $memo->delete();
                break;

            // 🔄 UPDATE → rollback
            case 'update':
                $memo->update([
                    'pending_changes' => null,
                    'status'          => 'approved',
                    'action_type'     => null,
                ]);
                break;

            // 🔄 DELETE → rollback (INI YANG SEBELUMNYA SALAH)
            case 'delete':
                $memo->update([
                    'status'      => 'approved',
                    'action_type' => null,
                ]);
                break;
        }

        return back()->with('warning', 'Permintaan ditolak');
    }
}