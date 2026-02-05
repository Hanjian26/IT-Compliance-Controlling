<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\TemplateDokumen;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class TemplateDokumenController extends Controller
{
    public function index()
    {
        $data = TemplateDokumen::orderBy('tanggal_terbit', 'asc')->paginate(5);

        // Logging lihat daftar
        activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'nama' => Auth::user()->nama ?? '',
                'nik' => Auth::user()->nik ?? '',
            ])
            ->log('Melihat daftar Template Dokumen');

        return view('template_dokumen', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_file' => 'required|string|max:255',
            'tanggal_terbit' => 'required|date',
            'perihal' => 'required|string',
            'file_dokumen' => 'nullable|mimes:pdf,doc,docx,zip|max:10240',
        ]);

        $memo = new TemplateDokumen();
        $memo->nama_file = $request->nama_file;
        $memo->tanggal_terbit = $request->tanggal_terbit;
        $memo->perihal = $request->perihal;

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('dokumen', $filename, 'public');
            $memo->file_dokumen = $filename;
        }

        $memo->save();

        activity('template_dokumen')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'create',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
            ])
            ->log('Menambahkan template dokumen');

        return redirect()->back()->with('success', 'Template Dokumen berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $memo = TemplateDokumen::findOrFail($id);

        activity('template_dokumen')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'delete',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
            ])
            ->log('Menghapus Template Dokumen');

        if ($memo->file_dokumen && Storage::disk('public')->exists('dokumen/' . $memo->file_dokumen)) {
            Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
        }

        $memo->delete();

        return redirect()->route('admin.template-dokumen.index')->with('success', 'Template Dokumen berhasil dihapus.');

    }

    public function edit($id)
    {
        $memo = TemplateDokumen::findOrFail($id);

        activity('template_dokumen')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'view_edit',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
            ])
            ->log('Melihat detail memo untuk diedit');

        return response()->json($memo);
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nama_file' => 'required|string|max:255',
        'tanggal_terbit' => 'required|date',
        'perihal' => 'required|string',
        'file_dokumen' => 'nullable|mimes:pdf,doc,docx,zip|max:10240',
    ]);

    // ✅ Ambil data lama
    $memo = TemplateDokumen::findOrFail($id); 

    $memo->nama_file = $request->nama_file;
    $memo->tanggal_terbit = $request->tanggal_terbit;
    $memo->perihal = $request->perihal;

    // ✅ Ganti file jika ada upload baru
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

    activity('template_dokumen')
        ->causedBy(Auth::user())
        ->performedOn($memo)
        ->withProperties([
            'action' => 'update',
            'nama' => Auth::user()->nama,
            'nik' => Auth::user()->nik,
        ])
        ->log('Memperbarui Template Dokumen');

    return redirect()->route('admin.template-dokumen.index')
    ->with('success', 'Template Dokumen berhasil diperbarui!');

}
}