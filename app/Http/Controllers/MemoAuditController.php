<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Memos;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use App\Helpers\KodeMemoHelper;


class MemoAuditController extends Controller
{
    public function index()
    {
          $data = Memos::where('tipe_memo', 'Audit')
            ->orderBy('tanggal_terbit', 'asc')
            ->paginate(5);
            return view('memo_audit', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_memo' => 'required|string|max:255',
            'scope_memo' => 'required|string|max:255',
            'nomor' => 'required|string|max:255',
            'tanggal_terbit' => 'required|date',
            'perihal'        => 'nullable|string',
            'file_dokumen' => 'nullable|mimes:pdf,doc,docx,zip|max:10240',
        ]);

        $memo = new Memos();
        $memo->tipe_memo = $request->tipe_memo;
        $memo->scope_memo = $request->scope_memo;
        $memo->nomor = KodeMemoHelper::generate($request->tipe_memo);
        $memo->tanggal_terbit = $request->tanggal_terbit;
        $memo->perihal = $request->perihal;

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('dokumen', $filename, 'public');
            $memo->file_dokumen = $filename;
        }

        $memo->save();

        activity('memo_audit')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'create',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
            ])
            ->log('Menambahkan memo audit');

        return redirect()->back()->with('success', 'Memo berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $memo = Memos::findOrFail($id);

        activity('memo_audit')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'delete',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
            ])
            ->log('Menghapus memo audit');

        if ($memo->file_dokumen && Storage::disk('public')->exists('dokumen/' . $memo->file_dokumen)) {
            Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
        }

        $memo->delete();

        return redirect()->route('memo.audit')->with('success', 'Dokumen berhasil dihapus.');
    }

    public function edit($id)
    {
        $memo = Memos::findOrFail($id);

        activity('memo_audit')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'view_edit',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
            ])
            ->log('Melihat detail memo audit untuk diedit');

        return response()->json($memo);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipe_memo' => 'required|string|max:255',
            'scope_memo' => 'required|string|max:255',
            'nomor' => 'required|string|max:255',
            'tanggal_terbit' => 'required|date',
           'perihal'        => 'nullable|string',
            'file_dokumen' => 'nullable|mimes:pdf,doc,docx,zip|max:10240',
        ]);

        $memo = Memos::findOrFail($id);
        $memo->tipe_memo = $request->tipe_memo;
        $memo->scope_memo = $request->scope_memo;
        $memo->nomor = $request->nomor;
        $memo->tanggal_terbit = $request->tanggal_terbit;
        $memo->perihal = $request->perihal;

        if ($request->hasFile('file_dokumen')) {
            if ($memo->file_dokumen && Storage::disk('public')->exists('dokumen/' . $memo->file_dokumen)) {
                Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
            }

            $file = $request->file('file_dokumen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('dokumen', $filename, 'public');
            $memo->file_dokumen = $filename;
        }

        $memo->save();

        activity('memo_audit')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'update',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
            ])
            ->log('Memperbarui memo audit');

        return redirect()->route('memo.audit')->with('success', 'Memo berhasil diperbarui!');
    }
        public function generateNomor()
    {
        $nomor = KodeMemoHelper::generate('Audit');
        return response()->json(['nomor' => $nomor]);
    }
}