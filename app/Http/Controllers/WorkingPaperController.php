<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\WorkingPaperModel;
use Illuminate\Support\Facades\Auth;

class WorkingPaperController extends Controller
{
    public function index(Request $request)
{
    $query = WorkingPaperModel::query();

    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('divisi', 'LIKE', "%{$request->search}%")
              ->orWhere('kegiatan', 'LIKE', "%{$request->search}%")
              ->orWhere('auditor', 'LIKE', "%{$request->search}%")
              ->orWhere('reviewer', 'LIKE', "%{$request->search}%")
              ->orWhere('status', 'LIKE', "%{$request->search}%")
              ->orWhere('keterangan', 'LIKE', "%{$request->search}%");
              
        });
    }

    $data = $query->orderBy('divisi', 'asc')->paginate(5);
    $data->appends($request->all());

    return view('working_paper', compact('data'));
}


        public function store(Request $request)
    {
        $request->validate([
        'divisi'    => 'required|string|max:255',
        'kegiatan' => 'required|string|max:255',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'nullable|string',
        'auditor' => 'required|string',
        'reviewer' => 'required|string',
        'status' => 'required|string',
        'keterangan' => 'nullable|string',
        'file_wp' => 'required|file|mimes:pdf,doc,docx,zip|max:2048',
        ]);

        $audit = new WorkingPaperModel();
        $audit->divisi = $request->divisi;
        $audit->kegiatan = $request->kegiatan;
        $audit->tanggal_mulai = $request->tanggal_mulai;
        $audit->tanggal_selesai = $request->tanggal_selesai;
        $audit->auditor = $request->auditor;
        $audit->reviewer = $request->reviewer;
        $audit->status = $request->status;
        $audit->keterangan = $request->keterangan;
        $audit->file_wp = $request->file_wp;
        
        if ($request->hasFile('file_wp')) {
        $path = $request->file('file_wp')->store('dokumen', 'public');
        $audit->file_wp = $path;
        
    }
        $audit->save();
        

        activity('working_paper')
            ->causedBy(Auth::user())
            ->performedOn($audit)
            ->withProperties([
                'action' => 'create',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
                'ip' => request()->ip(),
            ])
            ->log('Menambahkan Working Paper Audit');

        return redirect()->back()->with('success', 'Working Paper Audit berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $audit = WorkingPaperModel::findOrFail($id);

        $audit->delete();

        activity('working_paper')
            ->causedBy(Auth::user())
            ->performedOn($audit)
            ->withProperties([
                'action' => 'delete',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
                'ip' => request()->ip(),
            ])
            ->log('Menghapus data working paper audit');

        return redirect()->route('audit.wp')->with('success', 'Data working paper audit berhasil dihapus.');
    }

     public function edit($id)
    {
        $audit = WorkingPaperModel::findOrFail($id);
        return response()->json($audit);
    }

  public function update(Request $request, $id)
{
    $request->validate([
        'divisi'    => 'required|string|max:255',
        'kegiatan' => 'required|string|max:255',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'nullable|string',
        'auditor' => 'required|string',
        'reviewer' => 'required|string',
        'status' => 'required|string',
        'keterangan' => 'nullable|string',
        'file_wp' => 'nullable|file|mimes:pdf,doc,docx,zip|max:2048',
    ]);

    $audit = WorkingPaperModel::findOrFail($id);
    $audit->divisi = $request->divisi;
    $audit->kegiatan = $request->kegiatan;
    $audit->tanggal_mulai = $request->tanggal_mulai;
    $audit->tanggal_selesai = $request->tanggal_selesai;
    $audit->auditor = $request->auditor;
    $audit->reviewer = $request->reviewer;
    $audit->status = $request->status;
    $audit->keterangan = $request->keterangan;

    // 🔥 simpan file baru jika ada
    if ($request->hasFile('file_wp')) {
        $path = $request->file('file_wp')->store('dokumen', 'public');
        $audit->file_wp = $path;
    }

    $audit->save();

    activity('working_paper')
        ->causedBy(Auth::user())
        ->performedOn($audit)
        ->withProperties([
            'action' => 'update',
            'nama' => Auth::user()->nama,
            'nik' => Auth::user()->nik,
            'ip' => request()->ip(),
        ])
        ->log('Memperbarui Working Paper Audit');

    return redirect()->route('audit.wp')->with('success', 'Working Paper Audit berhasil diperbarui!');
}

}
