<?php

namespace App\Http\Controllers;

use App\Models\Memos;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\TrackHistoryHelper;

class ManagerApprovalController extends Controller
{
    /**
     * =========================
     * APPROVE
     * =========================
     */
    public function approve($id)
    {
        $memo = Memos::findOrFail($id);
        $user = Auth::user();

        if ($memo->status !== 'pending') {
            abort(400, 'Memo sudah diproses');
        }

        $nomorMemo   = $memo->nomor;
        $pengajuNama = User::where('nik', $memo->user_id)->value('nama');

        switch ($memo->action_type) {

            /**
             * APPROVE CREATE
             */
            case 'create':
                $memo->update([
                    'status'      => 'approved',
                    'approved_by' => $user->nik,
                    'approved_at' => now(),
                    'action_type' => null,
                ]);

                TrackHistoryHelper::log(
                    "menyetujui pengajuan penambahan oleh {$pengajuNama}",
                    $nomorMemo,
                    'Approved'
                );
                break;

            /**
             * APPROVE UPDATE
             */
            case 'update':

                // hapus file lama jika ada file baru
                if (
                    !empty($memo->pending_changes['file_dokumen']) &&
                    $memo->file_dokumen
                ) {
                    Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
                }

                $memo->update(array_merge(
                    $memo->pending_changes ?? [],
                    [
                        'pending_changes' => null,
                        'status'          => 'approved',
                        'approved_by'     => $user->nik,
                        'approved_at'     => now(),
                        'action_type'     => null,
                    ]
                ));

                TrackHistoryHelper::log(
                    "menyetujui perubahan oleh {$pengajuNama}",
                    $nomorMemo,
                    'Approved'
                );
                break;

            /**
             * APPROVE DELETE
             */
            case 'delete':

                TrackHistoryHelper::log(
                    "menyetujui penghapusan oleh {$pengajuNama}",
                    $nomorMemo,
                    'Approved'
                );

                if ($memo->file_dokumen) {
                    Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
                }

                $memo->delete();
                return back()->with('success', 'Memo berhasil dihapus');
        }

        return back()->with('success', 'Approval berhasil');
    }

    /**
     * =========================
     * REJECT
     * =========================
     */
    public function reject($id)
    {
        $memo = Memos::findOrFail($id);

        if ($memo->status !== 'pending') {
            abort(400, 'Memo sudah diproses');
        }

        $pengajuNama = User::where('nik', $memo->user_id)->value('nama');
        $nomorMemo   = $memo->nomor;

        /**
         * LOG HARUS DI AWAL
         */
        TrackHistoryHelper::log(
            "menolak pengajuan penambahan oleh {$pengajuNama}",
            $nomorMemo,
            'Rejected'
        );

        switch ($memo->action_type) {

            /**
             * REJECT CREATE
             */
            case 'create':
                if ($memo->file_dokumen) {
                    Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
                }

                $memo->delete();
                return back()->with('warning', 'Permintaan ditolak');

            /**
             * REJECT UPDATE
             * REJECT DELETE
             */
            case 'update':
            case 'delete':
                $memo->update([
                    'pending_changes' => null,
                    'status'          => 'rejected',
                    'action_type'     => null,
                ]);
                break;
        }

        return back()->with('warning', 'Permintaan ditolak');
    }
}