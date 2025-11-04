<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\BeritaAcara;
use Illuminate\Support\Facades\Auth;

class BeritaAcaraController extends Controller
{
    public function index()
    {
        $data = BeritaAcara::orderBy('tanggal_terbit', 'asc')->paginate(5);
        return view('berita_acara', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|string|max:255',
            'nomor_berita' => 'required|string|max:255',
            'dari' => 'required|string|max:50',
            'kepada' => 'required|string|max:50',
            'tanggal_terbit' => 'required|date',
            'file_dokumen' => 'nullable|mimes:pdf,doc,docx,zip|max:10240',
            'perihal' => 'required|string', 
        ]);

        $memo = new BeritaAcara();
        $memo->tipe = $request->tipe;
        $memo->nomor_berita = $request->nomor_berita;
        $memo->dari = $request->dari;
        $memo->kepada = $request->kepada;   
        $memo->tanggal_terbit = $request->tanggal_terbit;
        $memo->perihal = $request->perihal;

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('dokumen', $filename, 'public');
            $memo->file_dokumen = $filename;
        }

        $memo->save();

        activity('berita_acara')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'create',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
                'ip' => request()->ip(),
            ])
            ->log('Menambahkan dokumen Berita Acara');

        return redirect()->back()->with('success', 'Berita Acara berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $memo = BeritaAcara::findOrFail($id);

        if ($memo->file_dokumen && Storage::disk('public')->exists('dokumen/' . $memo->file_dokumen)) {
            Storage::disk('public')->delete('dokumen/' . $memo->file_dokumen);
        }

        $memo->delete();

        activity('berita_acara')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'delete',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
                'ip' => request()->ip(),
            ])
            ->log('Menghapus dokumen Berita Acara');

        return redirect()->route('berita.acara')->with('success', 'Berita Acara berhasil dihapus.');
    }

    public function edit($id)
    {
        $memo = BeritaAcara::findOrFail($id);
        return response()->json($memo);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipe' => 'required|string|max:255',
            'nomor_berita' => 'required|string|max:255',
            'dari' => 'required|string|max:50',
            'kepada' => 'required|string|max:50',
            'tanggal_terbit' => 'required|date',
            'file_dokumen' => 'nullable|mimes:pdf,doc,docx,zip|max:10240',
            'perihal' => 'required|string',
        ]);

        $memo = BeritaAcara::findOrFail($id);
        $memo->tipe = $request->tipe;
        $memo->nomor_berita = $request->nomor_berita;
        $memo->dari = $request->dari;
        $memo->kepada = $request->kepada;   
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

        activity('berita_acara')
            ->causedBy(Auth::user())
            ->performedOn($memo)
            ->withProperties([
                'action' => 'update',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
                'ip' => request()->ip(),
            ])
            ->log('Memperbarui Berita Acara');

        return redirect()->route('berita.acara')->with('success', 'Berita Acara berhasil diperbarui!');
    }
}
