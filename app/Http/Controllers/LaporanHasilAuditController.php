<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\LaporanHasilAuditModel;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class LaporanHasilAuditController extends Controller
{
    public function index(Request $request) 
    {
        $query = LaporanHasilAuditModel::query();
        $user = Auth::user();

        // Ambil nama department user
        $deptName = Department::where('id', $user->department)->value('department');

        // =========================================================
        //  Hanya IT Compliance yang boleh melihat semua data
        // =========================================================
        if ($deptName !== "IT Compliance") {
            $query->where('divisi', $deptName);
        }

        // ====== FITUR SEARCH ======
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

        // ====== PAGINATION ======
        $data = $query->orderBy('divisi', 'asc')->paginate(5);
        $data->appends($request->all());

        // Dropdown divisi
        $departments = Department::orderBy('department')->get();

        return view('laporan_hasil_audit', compact('data', 'departments'));
    }

    public function departmentRelation()
{
    return $this->belongsTo(Department::class, 'department');
}


    public function store(Request $request)
    {
        $request->validate([
            'divisi' => 'required|string|max:255',
            'kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|string',
            'auditor' => 'required|string',
            'reviewer' => 'required|string',
            'status' => 'required|string',
            'keterangan' => 'nullable|string',
            'file_lha' => 'required|file|mimes:pdf,doc,docx,zip|max:2048',
        ]);

        $audit = new LaporanHasilAuditModel();
        $audit->divisi = $request->divisi;
        $audit->kegiatan = $request->kegiatan;
        $audit->tanggal_mulai = $request->tanggal_mulai;
        $audit->tanggal_selesai = $request->tanggal_selesai;
        $audit->auditor = $request->auditor;
        $audit->reviewer = $request->reviewer;
        $audit->status = $request->status;
        $audit->keterangan = $request->keterangan;

        if ($request->hasFile('file_lha')) {
            $path = $request->file('file_lha')->store('dokumen', 'public');
            $audit->file_lha = $path;
        }

        $audit->save();

        activity('laporan_hasil_audit')
            ->causedBy(Auth::user())
            ->performedOn($audit)
            ->withProperties([
                'action' => 'create',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
                'ip' => request()->ip(),
            ])
            ->log('Menambahkan Laporan Hasil Audit');

        return redirect()->back()->with('success', 'Laporan Hasil Audit berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $audit = LaporanHasilAuditModel::findOrFail($id);

        $audit->delete();

        activity('laporan_hasil_audit')
            ->causedBy(Auth::user())
            ->performedOn($audit)
            ->withProperties([
                'action' => 'delete',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
                'ip' => request()->ip(),
            ])
            ->log('Menghapus data laporan hasil audit');

        return redirect()->route('audit.lha')->with('success', 'Data laporan hasil audit berhasil dihapus.');
    }

    public function edit($id)
    {
        $audit = LaporanHasilAuditModel::findOrFail($id);
        return response()->json($audit);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'divisi' => 'required|string|max:255',
            'kegiatan' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|string',
            'auditor' => 'required|string',
            'reviewer' => 'required|string',
            'status' => 'required|string',
            'keterangan' => 'nullable|string',
            'file_lha' => 'nullable|file|mimes:pdf,doc,docx,zip|max:2048',
        ]);

        $audit = LaporanHasilAuditModel::findOrFail($id);

        $audit->divisi = $request->divisi;
        $audit->kegiatan = $request->kegiatan;
        $audit->tanggal_mulai = $request->tanggal_mulai;
        $audit->tanggal_selesai = $request->tanggal_selesai;
        $audit->auditor = $request->auditor;
        $audit->reviewer = $request->reviewer;
        $audit->status = $request->status;
        $audit->keterangan = $request->keterangan;

        if ($request->hasFile('file_lha')) {
            $path = $request->file('file_lha')->store('dokumen', 'public');
            $audit->file_lha = $path;
        }

        $audit->save();

        activity('laporan_hasil_audit')
            ->causedBy(Auth::user())
            ->performedOn($audit)
            ->withProperties([
                'action' => 'update',
                'nama' => Auth::user()->nama,
                'nik' => Auth::user()->nik,
                'ip' => request()->ip(),
            ])
            ->log('Memperbarui Laporan Hasil Audit');

        return redirect()->route('audit.lha')->with('success', 'Laporan Hasil Audit berhasil diperbarui!');
    }
}