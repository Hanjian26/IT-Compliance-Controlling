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
     * APPROVE
     */
public function approve($id)
{
    $memo = Memos::findOrFail($id);
    $user = Auth::user();

    if ($memo->status !== 'pending') {
        abort(400, 'Memo sudah diproses');
    }

    $nomorMemo  = $memo->nomor;
    $pengajuNama = User::where('nik', $memo->user_id)->value('nama');

    switch ($memo->action_type) {

        case 'create':
            $memo->update([
                'status'      => 'approved',
                'approved_by' => $user->nik,
                'approved_at' => now(),
            ]);

            TrackHistoryHelper::log(
                "menyetujui pengajuan oleh {$pengajuNama}",
                $nomorMemo,
                'Approved'
            );
            break;

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

            TrackHistoryHelper::log(
                "menyetujui perubahan oleh {$pengajuNama}",
                $nomorMemo,
                'Approved'
            );
            break;

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
     * REJECT (KONTEKSTUAL)
     */
   public function reject($id)
{
    $memo = Memos::findOrFail($id);
    $pengajuNama = User::where('nik', $memo->user_id)->value('nama');

    if ($memo->status !== 'pending') {
        abort(400, 'Memo sudah diproses');
    }

    switch ($memo->action_type) {

        case 'create':
            if ($memo->file_dokumen) {
                Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
            }
            $memo->delete();
            break;

        case 'update':
            $memo->update([
                'pending_changes' => null,
                'status'          => 'approved',
                'action_type'     => null,
            ]);
            break;

        case 'delete':
            $memo->update([
                'status'      => 'approved',
                'action_type' => null,
            ]);
            break;
    }

    TrackHistoryHelper::log(
        "menolak pengajuan oleh {$pengajuNama}",
        $memo->nomor,
        'Rejected'
    );

    return back()->with('warning', 'Permintaan ditolak');
}

}