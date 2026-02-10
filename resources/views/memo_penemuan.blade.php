@extends('layouts.app')
@section('title', 'IT Compliance & Controlling')
@section('content')

@php
$user = Auth::user();
$level = $user->level ?? null;
$levelMap = [1 => 'Admin', 2 => 'User'];
$levelName = $levelMap[$level] ?? 'Unknown';
@endphp


<h2 style="text-decoration: underline; margin-bottom: 10px; margin-left:60px">Memo Penemuan
</h2>

<!-- Container Utama -->
<!-- Tombol Tambah Dokumen -->
@if($level == 1)
<div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
    <button onclick="openPopup()" onmouseover="this.style.backgroundColor='#5763e1'"
        onmouseout="this.style.backgroundColor='#4CAF50'"
        style="background-color: #4CAF50; color: white; padding: 8px 16px; text-decoration: none; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px;">

        {{-- <img width="20" height="20" src="https://img.icons8.com/wired/64/add-rule.png" alt="add-icon" /> --}}
        Tambah Dokumen
    </button>
</div>
@endif

<form method="GET" action="{{ route('admin.memo.penemuan.index') }}"
    style="margin-bottom: 10px; margin-left:15px; display: flex; justify-content: flex-start; gap: 10px;">
    <input type="text" name="search" placeholder="Cari data..." value="{{ request('search') }}"
        style="width: 250px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;">
    <button type="submit"
        style="background-color: #2196F3; color: white; padding: 8px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
        Cari
    </button>

    @if($level == 1)
    <button type="button" onclick="openPendingPopup()" style="background-color:#FF0000;color:white;padding:8px 14px;
           border:none;border-radius:4px;cursor:pointer;font-size:13px;">
        Memo Pending
    </button>
    @endif

    @if(request('search'))
    <a href="{{ route('admin.memo.penemuan.index') }}"
        style="background-color: #9e9e9e; color: white; padding: 8px 14px; text-decoration: none; border-radius: 4px; font-size: 13px;">
        Reset
    </a>
    @endif
</form>

<!-- POPUP MEMO PENDING -->
<div id="pendingPopup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">

    <div style="margin-top:-10%; background:white; padding:80px; border-radius:8px; width:90%; max-width:800px;">
        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 20px;">Memo Pending</h3>

        <table style="border-collapse: collapse; width: 100%; text-align: center;">
            <thead>
                <tr style="background:#f2f2f2;">
                    <th style="padding: 10px;">No</th>
                    <th style="padding: 10px;">Nama Pengaju</th>
                    <th style="padding: 10px;">Tanggal Pengajuan</th>
                    <th style="padding: 10px;">Nomor</th>
                    <th style="padding: 10px;">Perihal</th>
                    <th style="padding: 10px;">Permintaan</th>
                    <th style="padding: 10px;">Status</th>

                    @if($level == 1 && Auth::user()->is_manager)
                    <th style="padding: 10px;">Aksi</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @forelse ($pendingData as $item)
                <tr>
                    <td style="padding: 10px; font-size: 12px;">{{ $loop->iteration }}</td>
                    <td style="padding: 10px; font-size: 12px;">
                        {{ $item->nama_pengaju }}
                    </td>

                    <td style="padding: 10px; font-size: 12px;">{{
                        \Carbon\Carbon::parse($item->created_at)->format('d-M-Y') }}
                    </td>
                    <td style="padding: 10px; font-size: 12px;">{{ $item->nomor }}</td>
                    <td style="padding: 10px; font-size: 12px;">{{ $item->perihal }}</td>
                    <td style="padding: 10px; font-size: 12px;">{{ $item->action_type }}</td>


                    <td>
                        <span
                            style="background:#FFC107; color:#000; padding:4px 8px; border-radius:10px;  font-size:11px;">
                            Pending
                        </span>
                    </td>

                    {{-- AKSI HANYA UNTUK MANAGER --}}
                    @if(Auth::user()->is_manager)
                    <td>
                        <div style="display:flex; justify-content:center; gap:6px;">

                            <form method="POST" action="{{ route('admin.approval.memo.approve', $item->id) }}">
                                @csrf
                                <button
                                    style="background:#4CAF50; color:white; border:none; padding:4px 10px; border-radius:4px;">
                                    Approve
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.approval.memo.reject', $item->id) }}"
                                onsubmit="return confirm('Tolak memo ini?')">
                                @csrf
                                <button
                                    style="background:#F44336; color:white; border:none; padding:4px 10px; border-radius:4px;">
                                    Reject
                                </button>
                            </form>

                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:15px;">
                        Tidak ada memo pending
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>

        <div style="text-align:right; margin-top:15px;">
            <button onclick="closePendingPopup()"
                style="background-color: #e0e0e0; color: black; padding: 8px 14px; cursor: pointer; border: none; border-radius: 4px; transition: background-color 0.3s ease;"
                onmouseover="this.style.backgroundColor='#c7c7c7'"
                onmouseout="this.style.backgroundColor='#e0e0e0'">Tutup</button>
        </div>
    </div>
</div>


<!-- Tabel -->
<table style="border-collapse: collapse; width: 100%; text-align: center; margin-left:10px">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="padding: 10px;">No.</th>
            <th style="padding: 10px;">Tipe Memo</th>
            <th style="padding: 10px;">Scope Memo</th>
            <th style="padding: 10px;">Nomor</th>
            <th style="padding: 10px;">Tanggal Terbit</th>
            <th style="padding: 10px;">Perihal</th>
            <th style="padding: 10px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key => $item)
        <tr>
            <td style="padding: 10px; font-size: 12px;">{{ $data->firstItem() + $key }}</td>
            <!-- Tipe Memo (ex: kebijakan/administrasi etc.-->
            <td style="padding: 10px; font-size: 12px;">{{ $item->tipe_memo }}</td>
            <!-- Score Memo (ex: internal/external -->
            <td style="padding: 10px; font-size: 12px;">{{ $item->scope_memo }}</td>
            <td style="padding: 10px; font-size: 12px;">{{ $item->nomor }}</td>
            <td style="padding: 10px; font-size: 12px;">
                {{(\Carbon\Carbon::parse($item->tanggal_terbit)->format('d-M-Y')) }}
            </td>
            <td style="padding: 10px; font-size: 12px;">{{ $item->perihal }}</td>
            <td style="padding: 10px; font-size: 12px;">
                <div style="display: inline-flex; justify-content: center; gap: 10px;">

                    <!-- Lihat -->
                    <a href="{{ asset('storage/dokumen/'.$item->file_dokumen) }}" target="_blank"
                        style="display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
                        <img width="18" height="18" src="https://img.icons8.com/ios/50/visible--v1.png" alt="lihat-icon"
                            style="display: block;" />
                        <!-- <span style="font-size: 12px;">Lihat</span> -->
                    </a>

                    <a href="{{ route('memo.download', $item->id) }}" style="display: inline-flex; align-items: center; gap: 5px;
                    text-decoration: none; color: inherit;">

                        <img width="18" height="18" padding-top:30px;
                            src="https://img.icons8.com/material-rounded/24/download--v1.png" alt="download--v1"
                            style="display: block;" />
                    </a>

                    <!-- Edit -->
                    <a href="javascript:void(0);" onclick="editMemo({{ $item->id }})" title="Edit"
                        style="display: inline-flex; align-items: center; gap: 10px; text-decoration: none; color: inherit;">
                        <img width="18" height="18" src="https://img.icons8.com/ios/50/create-new.png" alt="edit-icon"
                            style="display: block;" />
                        <!-- <span style="font-size: 12px;">Edit</span> -->
                    </a>

                    <!-- Hapus -->
                    <form action="{{ route('admin.memo.penemuan.destroy', $item->id) }}" method="POST"
                        onsubmit="return confirmDelete()" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Hapus"
                            style="display: inline-flex; align-items: center; background: none; border: none; padding: 0; font-size: 12px; color: inherit; cursor: pointer;">
                            <img width="18" height="15" src="https://img.icons8.com/ios/50/trash--v1.png"
                                alt="hapus-icon" style="display: block;" />
                            <!-- <span>Hapus</span> -->
                        </button>
                    </form>

                </div>
            </td>

        </tr>
        @endforeach
    </tbody>
</table>

<!-- Pagination -->
<div style="display: flex; justify-content: flex-end; padding: 10px 0;">
    <div style="display: flex; flex-wrap: nowrap;">
        <ul style="display: flex; list-style: none; padding: 0; margin: 0; gap: 4px;">
            @foreach ($data->links()->elements[0] as $page => $url)
            <li style="display: inline-block;">
                <a href="{{ $url }}"
                    style="text-decoration: none; color: #333; padding: 4px 8px; border: 1px solid #ccc; border-radius: 4px;">{{
                    $page }}</a>
            </li>
            @endforeach
        </ul>
    </div>
</div>
</div>
</div>

<!-- Modal Edit Memo -->
<div id="editForm" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div
        style="background-color: white; padding: 40px; border-radius: 10px; width: 100%; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); position: relative;">
        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 20px; text-align:center;"><u>Edit Memo Penemuan</u>
        </h3>
        <form id="editMemoForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 15px;">
                <label for="edit_tipe_memo">Tipe Memo<span style="color: red;">*</span>:</label>
                <input type="text" name="tipe_memo" id="edit_tipe_memo" value="Penemuan" readonly
                    style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; cursor: not-allowed;">
            </div>



            <div style="margin-bottom: 15px;">
                <label for="edit_scope">Scope Memo<span style="color: red;">*</span>:</label>
                <select name="scope_memo" id="edit_scope" required
                    style="width: 104%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="" disabled selected>-- Pilih Scope --</option>
                    <option value="Internal">Internal</option>
                    <option value="Eksternal">Eksternal</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="edit_tanggal_terbit">Tanggal Terbit<span style="color: red;">*</span>:</label>
                <input type="date" name="tanggal_terbit" id="edit_tanggal_terbit"
                    onchange="generateNomorFromTanggalEdit()" min="2000-01-01" max="2099-12-31" onkeydown="return false"
                    style="width: 100%; padding: 8px;">

            </div>

            <div style="margin-bottom: 15px;">
                <label for="edit_nomor">Nomor<span style="color: red;">*</span>:</label>
                <input type="text" name="nomor" id="edit_nomor" readonly
                    style="cursor: not-allowed; width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="edit_perihal">Perihal<span style="color: red;">*</span>:</label>
                <textarea name="perihal" id="edit_perihal" rows="3" style="width: 100%; padding: 8px;"></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label for="edit_file_dokumen">Upload Dokumen (Max Size: 10 MB)<span
                        style="color: red;"></span>:</label>
                <input type="file" name="file_dokumen" id="file_dokumen" accept=".pdf,.doc,.docx,.zip"
                    style="width: 100%; padding: 6px;">


            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeEdit()"
                    style="background-color: #e0e0e0; color: black; padding: 8px 14px; cursor: pointer; border: none; border-radius: 4px; transition: background-color 0.3s ease;"
                    onmouseover="this.style.backgroundColor='#c7c7c7'"
                    onmouseout="this.style.backgroundColor='#e0e0e0'">
                    Batal
                </button>
                <button type="submit"
                    style="background-color: #4caf50; color: white; padding: 8px 14px; cursor: pointer; border: none; border-radius: 4px; transition: background-color 0.3s ease;"
                    onmouseover="this.style.backgroundColor='#45a049'"
                    onmouseout="this.style.backgroundColor='#4caf50'">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Dokumen -->
<div id="popupForm" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div
        style="background-color: white; padding: 40px; border-radius: 10px; width: 100%; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); position: relative;">
        <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 20px; text-align:center;"><u>Tambah Memo Penemuan</u>
        </h3>
        <form action="{{ route('admin.memo.penemuan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 15px;">
                <label for="tipe_memo">Tipe Memo<span style="color: red;">*</span>:</label>
                <input type="text" name="tipe_memo" id="tipe_memo" value="Penemuan" readonly
                    style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; cursor: not-allowed;">
            </div>


            <div style="margin-bottom: 15px;">
                <label for="scope_memo">Scope Memo<span style="color: red;">*</span>:</label>
                <select name="scope_memo" id="scope_memo" required
                    style="width: 104%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <option value="" disabled selected>-- Pilih Scope --</option>
                    <option value="Internal">Internal</option>
                    <option value="Eksternal">Eksternal</option>
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="tanggal_terbit">Tanggal Terbit<span style="color: red;">*</span>:</label>
                <input type="date" name="tanggal_terbit" id="tanggal_terbit" required lang="id" min="2000-01-01"
                    max="2099-12-31" onkeydown="return false" onchange="generateNomorFromTanggal()"
                    style="width:100%; padding:8px;" autocomplete="off">

            </div>

            <div style="margin-bottom: 15px;">
                <label for="nomor">Nomor<span style="color: red;">*</span>:</label>
                <input type="text" name="nomor" id="nomor" readonly
                    style="cursor: not-allowed; width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="edit_perihal">Perihal<span style="color: red;"></span>:</label>
                <textarea name="perihal" id="perihal" rows="3" style="width: 100%; padding: 8px;"></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label for="file_dokumen">Upload Dokumen (Max Size: 10 MB)<span style="color: red;">*</span>:</label>
                <input type="file" name="file_dokumen" id="file_dokumen" accept=".pdf,.doc,.docx,.zip"
                    style="width: 100%; padding: 6px;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closePopup()"
                    style="background-color: #e0e0e0; color: black; padding: 8px 14px; cursor: pointer; border: none; border-radius: 4px; transition: background-color 0.3s ease;"
                    onmouseover="this.style.backgroundColor='#c7c7c7'"
                    onmouseout="this.style.backgroundColor='#e0e0e0'">
                    Batal
                </button>
                <button type="submit"
                    style="background-color: #4caf50; color: white; padding: 8px 14px; cursor: pointer; border: none; border-radius: 4px; transition: background-color 0.3s ease;"
                    onmouseover="this.style.backgroundColor='#45a049'"
                    onmouseout="this.style.backgroundColor='#4caf50'">
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script -->
<script>
    function openPopup() {
    document.getElementById('popupForm').style.display = 'flex';
  
}

function generateNomorFromTanggal() {
    const tgl = document.getElementById('tanggal_terbit').value;
    if (!tgl) return;

    fetch("{{ route('admin.memo.penemuan.generate_nomor') }}?tanggal=" + tgl)
        .then(res => res.json())
        .then(data => {
            document.getElementById('nomor').value = data.nomor;
        })
        .catch(err => console.error(err));
}

function generateNomorFromTanggalEdit() {
    const tgl = document.getElementById('edit_tanggal_terbit').value;
    if (!tgl) return;

    fetch("{{ route('admin.memo.penemuan.generate_nomor') }}?tanggal=" + tgl)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_nomor').value = data.nomor;
        })
        .catch(err => console.error(err));
}


function closePopup() {
    document.getElementById('popupForm').style.display = 'none';
}

function closeEdit() {
    document.getElementById('editForm').style.display = 'none';
}

function editMemo(id) {
    fetch(`/admin/memo-penemuan/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_tipe_memo').value = data.tipe_memo;
            document.getElementById('edit_scope').value = data.scope_memo;
            document.getElementById('edit_nomor').value = data.nomor;
            document.getElementById('edit_tanggal_terbit').value = data.tanggal_terbit;
            document.getElementById('edit_perihal').value = data.perihal;

            const form = document.getElementById('editMemoForm');
            form.action = `/admin/memo-penemuan/${id}`;
            document.getElementById('editForm').style.display = 'flex';
        })
        .catch(err => console.error(err));
}

function openPendingPopup() {
    document.getElementById('pendingPopup').style.display = 'flex';
}

function closePendingPopup() {
    document.getElementById('pendingPopup').style.display = 'none';
}

function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus file ini?');
}

// Hilang setelah 3 detik
setTimeout(() => {
    const alert = document.getElementById('success-alert');
    if (alert) {
        alert.style.transition = "opacity 0.5s ease"; // animasi
        alert.style.opacity = 0;
        setTimeout(() => alert.remove(), 500); // hapus dari DOM setelah fade out
    }
}, 3000);


document.addEventListener('DOMContentLoaded', function() {
    // Daftar semua input tanggal di halaman
    const dateInputs = document.querySelectorAll('input[type="date"]');

    dateInputs.forEach(input => {
        // Blokir input manual, biar hanya pakai date picker
        input.addEventListener('keydown', e => e.preventDefault());
        input.addEventListener('paste', e => e.preventDefault());

        // Paksa buka date picker ketika input diklik
        input.addEventListener('click', () => {
            try {
                // Cara paling stabil untuk Chrome, Edge, dan Opera
                input.showPicker();
            } catch (err) {
                // Safari / Firefox tidak mendukung showPicker, fallback dengan fokus
                input.focus();
            }
        });
    });
});
</script>

@if (session('success'))
<div id="success-alert" style="position: fixed; bottom: 20px; right: 20px; background-color: #4CAF50; 
              color: white; padding: 12px 20px; border-radius: 5px; z-index: 9999;">
    {{ session('success') }}
</div>

@endif

@endsection