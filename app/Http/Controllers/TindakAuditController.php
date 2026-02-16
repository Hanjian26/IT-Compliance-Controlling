<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\TindakAuditDB;
use App\Models\Department; 
use Illuminate\Support\Facades\Auth;

class TindakAuditController extends Controller
{
public function index(Request $request)
{
    $query = TindakAuditDB::query();

    // Ambil user
    $user = Auth::user();

    // Ambil nama department user
    $deptName = Department::where('id', $user->department)->value('department');

    // BATAS AKSES: hanya IT Compliance boleh melihat semua data
    if ($deptName !== "IT Compliance") {
        $query->where('divisi', $deptName);
    }

    // Search
    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('kegiatan', 'LIKE', "%{$request->search}%")
              ->orWhere('auditor', 'LIKE', "%{$request->search}%")
              ->orWhere('reviewer', 'LIKE', "%{$request->search}%")
              ->orWhere('status', 'LIKE', "%{$request->search}%")
              ->orWhere('keterangan', 'LIKE', "%{$request->search}%")
              ->orWhere('pic', 'LIKE', "%{$request->search}%");
        });
    }

    $data = $query->orderBy('divisi', 'asc')->paginate(5);
    $data->appends($request->all());

    $departments = Department::orderBy('department')->get();

    return view('tlha_audit', compact('data', 'departments'));
}
        public function store(Request $request)
    {
        $request->validate([
        'divisi'    => 'required|string|max:255',
        'kegiatan' => 'required|string|max:255',
        // 'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'nullable|string',
        'pic' => 'required|string',
        'auditor' => 'required|string',
        'reviewer' => 'required|string',
        'status' => 'required|string',
        'keterangan' => 'nullable|string',
        'file_laporan' => 'required|file|mimes:pdf,doc,docx,zip|max:2048',
        ]);

        $audit = new TindakAuditDB();
        $audit->divisi = $request->divisi;
        $audit->kegiatan = $request->kegiatan;
        // $audit->tanggal_mulai = $request->tanggal_mulai;
        $audit->tanggal_selesai = $request->tanggal_selesai;
        $audit->pic = json_encode(explode(', ', $request->pic));
        $audit->auditor = $request->auditor;
        $audit->reviewer = $request->reviewer;
        $audit->status = $request->status;
        $audit->keterangan = $request->keterangan;
        $audit->file_laporan = $request->file_laporan;
        
        if ($request->hasFile('file_laporan')) {
        $path = $request->file('file_laporan')->store('dokumen', 'public');
        $audit->file_laporan = $path;
        
    }
        $audit->save();
        

      activity('tlha_audit')
    ->causedBy(Auth::user())
    ->performedOn($audit)
    ->withProperties([
        'action'      => 'create',
        'user_id'     => Auth::user()->id,
        'nama'        => Auth::user()->nama,
        'nik'         => Auth::user()->nik,
        'email'       => Auth::user()->email,
        'department'  => Department::where('id', Auth::user()->department)->value('department'),
        'ip'          => request()->ip(),
        'new_data'    => $audit->toArray(),
    ])
    ->log('User menambahkan TLHA Audit');


        return redirect()->back()->with('success', 'TLHA Audit berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $audit = TindakAuditDB::findOrFail($id);

        $audit->delete();

       activity('tlha_audit')
    ->causedBy(Auth::user())
    ->performedOn($audit)
    ->withProperties([
        'action'      => 'delete',
        'user_id'     => Auth::user()->id,
        'nama'        => Auth::user()->nama,
        'nik'         => Auth::user()->nik,
        'email'       => Auth::user()->email,
        'department'  => Department::where('id', Auth::user()->department)->value('department'),
        'ip'          => request()->ip(),
        'deleted_data'=> $audit->toArray(),
    ])
    ->log('User menghapus TLHA Audit');


        return redirect()->route('admin.audit.tlha.index')->with('success', 'Data TLHA berhasil dihapus.');
    }

     public function edit($id)
    {
        $audit = TindakAuditDB::findOrFail($id);
        return response()->json($audit);
    }

  public function update(Request $request, $id)
{
    $request->validate([
        'divisi'    => 'required|string|max:255',
        'kegiatan' => 'required|string|max:255',
        // 'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'nullable|string',
        'pic' => 'required|string',
        'auditor' => 'required|string',
        'reviewer' => 'required|string',
        'status' => 'required|string',
        'keterangan' => 'nullable|string',
        'file_laporan' => 'nullable|file|mimes:pdf,doc,docx,zip|max:2048',
    ]);

    $audit = TindakAuditDB::findOrFail($id);
    $audit->divisi = $request->divisi;
    $audit->kegiatan = $request->kegiatan;
    // $audit->tanggal_mulai = $request->tanggal_mulai;
    $audit->tanggal_selesai = $request->tanggal_selesai;
    $audit->pic = json_encode(explode(', ', $request->pic));
    $audit->auditor = $request->auditor;
    $audit->reviewer = $request->reviewer;
    $audit->status = $request->status;
    $audit->keterangan = $request->keterangan;


    if ($request->hasFile('file_laporan')) {
        $path = $request->file('file_laporan')->store('dokumen', 'public');
        $audit->file_laporan = $path;
    }

    $audit->save();

  activity('tlha_audit')
    ->causedBy(Auth::user())
    ->performedOn($audit)
    ->withProperties([
        'action'      => 'update',
        'user_id'     => Auth::user()->id,
        'nama'        => Auth::user()->nama,
        'nik'         => Auth::user()->nik,
        'email'       => Auth::user()->email,
        'department'  => Department::where('id', Auth::user()->department)->value('department'),
        'ip'          => request()->ip(),
        'new_data'    => $audit->toArray(),
    ])
    ->log('User memperbarui TLHA Audit');


    return redirect()->route('admin.audit.tlha.index')->with('success', 'TLHA Audit berhasil diperbarui!');
}

}