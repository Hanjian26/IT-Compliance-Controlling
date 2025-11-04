@extends('layouts.app')
@section('title', 'IT Compliance')
@section('content')
<h2 style="text-decoration: underline; margin-bottom: 10px; margin-left:60px">Memo Penemuan
</h2>

<!-- Container Utama -->
<!-- Tombol Tambah Dokumen -->
<div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
    <button onclick="openPopup()"
        style="background-color: #4CAF50; color: white; padding: 8px 16px; text-decoration: none; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px;">

        <img width="20" height="20" src="https://img.icons8.com/wired/64/add-rule.png" alt="add-icon" />
        Tambah Dokumen
    </button>
</div>

<!-- Tabel -->
<table style="border-collapse: collapse; width: 100%; text-align: center;">
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
            <td style="padding: 10px; font-size: 11px;">
                <div
                    style="display: inline-flex; justify-content: center; gap: 10px; flex-wrap: wrap; align-items: center;">

                    <!-- Lihat -->
                    <a href="{{ asset('storage/dokumen/'.$item->file_dokumen) }}" target="_blank"
                        style="display: inline-flex; align-items: center; gap: 5px; text-decoration: none; color: inherit;">
                        <img width="18" height="18" src="https://img.icons8.com/ios/50/visible--v1.png" alt="lihat-icon"
                            style="display: block;" />
                        <!-- <span style="font-size: 12px;">Lihat</span> -->
                    </a>

                    <!-- Edit -->
                    <a href="javascript:void(0);" onclick="editMemo({{ $item->id }})" title="Edit"
                        style="display: inline-flex; align-items: center; gap: 5px; text-decoration: none; color: inherit;">
                        <img width="18" height="18" src="https://img.icons8.com/ios/50/create-new.png" alt="edit-icon"
                            style="display: block;" />
                        <!-- <span style="font-size: 12px;">Edit</span> -->
                    </a>

                    <!-- Hapus -->
                    <form action="{{ route('memo.penemuan.destroy', $item->id) }}" method="POST"
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
                    style="text-decoration: none; color: #333; padding: 4px 8px; border: 1px solid #ccc; border-radius: 4px;">{{ $page }}</a>
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
                <label for="edit_nomor">Nomor<span style="color: red;">*</span>:</label>
                <input type="text" name="nomor" id="edit_nomor" required style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label for="edit_tanggal_terbit">Tanggal Terbit<span style="color: red;">*</span>:</label>
                <input type="date" name="tanggal_terbit" id="edit_tanggal_terbit" lang="id" min="2000-01-01"
                    max="2099-12-31" onkeydown="return false" required style="width: 100%; padding: 8px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label for="edit_perihal">Perihal<span style="color: red;">*</span>:</label>
                <textarea name="perihal" id="edit_perihal" rows="3" style="width: 100%; padding: 8px;"></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label for="edit_file_dokumen">Ganti Dokumen (PDF)<span style="color: red;"></span>:</label>
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
        <form action="{{ route('memo.penemuan.store') }}" method="POST" enctype="multipart/form-data">
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
                <label for="nomor">Nomor<span style="color: red;">*</span>:</label>
                <input type="text" name="nomor" id="nomor" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="tanggal_terbit">Tanggal Terbit<span style="color: red;">*</span>:</label>
                <input type="date" name="tanggal_terbit" id="tanggal_terbit" required lang="id" min="2000-01-01"
                    max="2099-12-31" onkeydown="return false" style="width:100%; padding:8px;" autocomplete="off">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="edit_perihal">Perihal<span style="color: red;"></span>:</label>
                <textarea name="perihal" id="perihal" rows="3" style="width: 100%; padding: 8px;"></textarea>
            </div>
            <div style="margin-bottom: 20px;">
                <label for="file_dokumen">Upload Dokumen (PDF, DOCX, dll)<span style="color: red;">*</span>:</label>
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