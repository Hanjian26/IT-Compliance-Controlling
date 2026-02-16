<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\AuditBRDB;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class AuditDatabaseController extends Controller
{
 public function index(Request $request)
{
    $query = AuditBRDB::query();

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

      $departments = Department::orderBy('department', 'asc')->get();

    return view('audit_brdb', compact('data', 'departments'));
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
        ]);

        $audit = new AuditBRDB();
        $audit->divisi = $request->divisi;
        $audit->kegiatan = $request->kegiatan;
        $audit->tanggal_mulai = $request->tanggal_mulai;
        $audit->tanggal_selesai = $request->tanggal_selesai;
        $audit->auditor = $request->auditor;
        $audit->reviewer = $request->reviewer;
        $audit->status = $request->status;
        $audit->keterangan = $request->keterangan;
        $audit->save();
    

        activity('audit_brdb')
            ->causedBy(Auth::user())
            ->performedOn($audit)
            ->withProperties([
                'action' => 'create',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
                'ip' => request()->ip(),
            ])
            ->log('Menambahkan Jadwal Audit Backup Restore Database');

        return redirect()->back()->with('success', 'Jadwal Audit Backup Restore Database berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $audit = AuditBRDB::findOrFail($id);

        $audit->delete();

        activity('audit-brdb')
            ->causedBy(Auth::user())
            ->performedOn($audit)
            ->withProperties([
                'action' => 'delete',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
                'ip' => request()->ip(),
            ])
            ->log('Menghapus Jadwal Audit Backup Restore Database');

        return redirect()->route('admin.audit.brdb.index')->with('success', 'Jadwal Audit Backup Restore berhasil dihapus.');
    }

    public function edit($id)
    {
        $audit = AuditBRDB::findOrFail($id);
        return response()->json($audit);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
        'divisi'          => 'required|string|max:255',
        'kegiatan'        => 'required|string|max:255',
        'tanggal_mulai'   => 'required|date',
        'tanggal_selesai' => 'nullable|string',
        'auditor'         => 'required|string',
        'reviewer'        => 'required|string',
        'status'          => 'required|string',
        'keterangan' => 'nullable|string',
        ]);

        $audit = AuditBRDB::findOrFail($id);
        $audit->divisi = $request->divisi;
        $audit->kegiatan = $request->kegiatan;
        $audit->tanggal_mulai = $request->tanggal_mulai;
        $audit->tanggal_selesai = $request->tanggal_selesai;
        $audit->auditor = $request->auditor;
        $audit->reviewer = $request->reviewer;
        $audit->status = $request->status;
        $audit->keterangan = $request->keterangan;
        $audit->save();

        activity('audit-brdb')
            ->causedBy(Auth::user())
            ->performedOn($audit)
            ->withProperties([
                'action' => 'update',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
                'ip' => request()->ip(),
            ])
            ->log('Memperbarui Jadwal Audit Backup Restore Database');

        return redirect()->route('admin.audit.brdb.index')->with('success', 'Jadwal Audit Backup Restore Database berhasil diperbarui!');
    }
}