@extends('layouts.app')
@section('title', 'IT Compliance & Controlling')
@section('content')

@php
$user = Auth::user();
$level = $user->level ?? null;
$levelMap = [1 => 'Admin', 2 => 'User'];
$levelName = $levelMap[$level] ?? 'Unknown';
@endphp
<h2 style="text-decoration: underline; margin-bottom: 10px; margin-left:60px">Template Dokumen</h2>

<!-- Container Utama -->
<!-- Tombol Tambah Dokumen -->
@if ($level == 1)
<div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
  <button onclick="openPopup()" onmouseover="this.style.backgroundColor='#5763e1'"
    onmouseout="this.style.backgroundColor='#4CAF50'"
    style="background-color: #4CAF50; color: white; padding: 8px 16px; text-decoration: none; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px;">


    {{-- <img width="20" height="20" src="https://img.icons8.com/wired/64/add-rule.png" alt="add-icon" /> --}}
    Tambah Dokumen
  </button>
</div>
@endif

<!-- Tabel -->
<table style="border-collapse: collapse; width: 100%; text-align: center;">
  <thead>
    <tr style="background-color: #f2f2f2;">
      <th style="padding: 10px;">No.</th>
      <th style="padding: 10px;">Nama File</th>
      <th style="padding: 10px;">Tanggal Terbit</th>
      <th style="padding: 10px;">Perihal</th>
      <th style="padding: 10px;">Aksi</th>

    </tr>
  </thead>
  <tbody>
    @foreach ($data as $key => $item)
    @if($item->visibility == 'public' || ($item->visibility == 'itcc' && $level == 1))
    <tr>
      <td style="padding: 10px; font-size: 12px;">{{ $data->firstItem() + $key }}</td>
      <td style="padding: 10px; font-size: 12px;">{{ $item->nama_file }}</td>
      <td style="padding: 10px; font-size: 12px;">
        {{(\Carbon\Carbon::parse($item->tanggal_terbit)->format('d-M-Y')) }}
      </td>
      <td style="padding: 10px; font-size: 12px;">{{ $item->perihal }}</td>
      <td style="padding: 10px; font-size: 11px;">
        <div style="display: inline-flex; justify-content: center; gap: 10px; flex-wrap: wrap; align-items: center;">

          <!-- Lihat -->
          <a href="{{ asset('storage/dokumen/'.$item->file_dokumen) }}" target="_blank"
            style="display: inline-flex; align-items: center; gap: 5px; text-decoration: none; color: inherit;">
            <img width="18" height="18" src="https://img.icons8.com/ios/50/visible--v1.png" alt="lihat-icon"
              style="display: block;" />
            <!-- <span style="font-size: 12px;">Lihat</span> -->
          </a>


          <a href="{{ asset('storage/dokumen/'.$item->file_dokumen)}}" style="display: inline-flex; align-items: center; gap: 5px;
                    text-decoration: none; color: inherit;">
            <img width="18" height="18" padding-top:30px;
              src="https://img.icons8.com/material-rounded/24/download--v1.png" alt="download--v1"
              style="display: block;" />
          </a>

          <!-- Edit -->
          @if ($level == 1)
          <a href="javascript:void(0);" onclick="editMemo({{ $item->id }})" title="Edit"
            style="display: inline-flex; align-items: center; gap: 5px; text-decoration: none; color: inherit;">
            <img width="18" height="18" src="https://img.icons8.com/ios/50/create-new.png" alt="edit-icon"
              style="display: block;" />
            <!-- <span style="font-size: 12px;">Edit</span> -->
          </a>

          <!-- Hapus -->
          <form action="{{ route('admin.template-dokumen.destroy', $item->id) }}" method="POST"
            onsubmit="return confirmDelete()" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" title="Hapus"
              style="display: inline-flex; align-items: center; background: none; border: none; padding: 0; font-size: 12px; color: inherit; cursor: pointer;">
              <img width="18" height="15" src="https://img.icons8.com/ios/50/trash--v1.png" alt="hapus-icon"
                style="display: block;" />
              <!-- <span>Hapus</span> -->
            </button>
          </form>
          @endif
          @endif

        </div>
      </td>

    </tr>
    @endforeach
  </tbody>
</table>

<!-- Pagination -->
<div style="display: flex; justify-content: flex-end; padding: 10px 0;">
  {{ $data->links() }}
</div>
</div>
</div>

<!-- Modal Edit Memo -->
<div id="editForm" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
  <div
    style="background-color: white; padding: 40px; border-radius: 10px; width: 100%; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); position: relative;">
    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 20px; text-align:center;"><u>Edit Dokumen Template</u>
    </h3>
    <form id="editMemoForm" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div style="margin-bottom: 15px;">
        <label for="edit_nama_file">Nama File:</label>
        <input type="text" name="nama_file" id="edit_nama_file" required style="width: 100%; padding: 8px;">
      </div>
      <div style="margin-bottom: 15px;">
        <label for="edit_tanggal_terbit">Tanggal:</label>
        <input type="date" name="tanggal_terbit" id="edit_tanggal_terbit" required lang="id" min="2000-01-01"
          max="2099-12-31" onkeydown="return false" style="width:100%; padding:8px;" autocomplete="off">
      </div>
      <div style=" margin-bottom: 15px;">
        <label for="edit_perihal">Perihal:</label>
        <textarea name="perihal" id="edit_perihal" required rows="3" style="width: 100%; padding: 8px;"></textarea>
      </div>
      <div style="margin-bottom: 15px;">
        <label for="edit_visibility">Peruntukan:</label>
        <select name="visibility" id="edit_visibility" required style="width: 104%; padding: 8px;">
          <option value="">-- Pilih Peruntukan --</option>
          <option value="itcc">ITCC</option>
          <option value="public">Public</option>
        </select>
      </div>


      <div style="margin-bottom: 20px;">
        <label for="edit_file_dokumen">Ganti Dokumen (PDF):</label>
        <input type="file" name="file_dokumen" id="file_dokumen" accept=".pdf,.doc,.docx,.zip"
          style="width: 100%; padding: 6px;">


      </div>
      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeEdit()"
          style="background-color: #e0e0e0; color: black; padding: 8px 14px; cursor: pointer; border: none; border-radius: 4px; transition: background-color 0.3s ease;"
          onmouseover="this.style.backgroundColor='#c7c7c7'" onmouseout="this.style.backgroundColor='#e0e0e0'">
          Batal
        </button>
        <button type="submit"
          style="background-color: #4caf50; color: white; padding: 8px 14px; cursor: pointer; border: none; border-radius: 4px; transition: background-color 0.3s ease;"
          onmouseover="this.style.backgroundColor='#45a049'" onmouseout="this.style.backgroundColor='#4caf50'">
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
    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 20px; text-align:center;"><u>Tambah Dokumen Template</u>
    </h3>
    <form action="{{ route('admin.template-dokumen.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div style="margin-bottom: 15px;">
        <label for="nama_file">Nama File:</label>
        <input type="text" name="nama_file" id="nama_file" required style="width: 100%; padding: 8px;">
      </div>
      <div style="margin-bottom: 15px;">
        <label for="tanggal_terbit">Tanggal Terbit:</label>
        <input type="date" name="tanggal_terbit" id="tanggal_terbit" required lang="id" min="2000-01-01"
          max="2099-12-31" onkeydown="return false" style="width:100%; padding:8px;" autocomplete="off">
      </div>
      <div style="margin-bottom: 15px;">
        <label for="visibility">Peruntukan:</label>
        <select name="visibility" id="visibility" required style="width: 104%; padding: 8px;">
          <option value="">-- Pilih Peruntukan --</option>
          <option value="itcc">ITCC</option>
          <option value="public">Public</option>
        </select>
      </div>

      <div style="margin-bottom: 15px;">
        <label for="perihal">Perihal:</label>
        <textarea name="perihal" id="perihal" required rows="3" style="width: 100%; padding: 8px;"></textarea>
      </div>
      <div style="margin-bottom: 20px;">
        <label for="file_dokumen">Upload Dokumen (PDF, DOCX, dll):</label>
        <input type="file" name="file_dokumen" id="file_dokumen" accept=".pdf,.doc,.docx,.zip"
          style="width: 100%; padding: 6px;">
      </div>
      <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closePopup()"
          style="background-color: #e0e0e0; color: black; padding: 8px 14px; cursor: pointer; border: none; border-radius: 4px; transition: background-color 0.3s ease;"
          onmouseover="this.style.backgroundColor='#c7c7c7'" onmouseout="this.style.backgroundColor='#e0e0e0'">
          Batal
        </button>
        <button type="submit"
          style="background-color: #4caf50; color: white; padding: 8px 14px; cursor: pointer; border: none; border-radius: 4px; transition: background-color 0.3s ease;"
          onmouseover="this.style.backgroundColor='#45a049'" onmouseout="this.style.backgroundColor='#4caf50'">
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
  fetch(`/admin/template-dokumen/${id}/edit`, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
    .then(res => res.json())
    .then(data => {
        document.getElementById('edit_nama_file').value = data.nama_file;
        document.getElementById('edit_tanggal_terbit').value = data.tanggal_terbit;
        document.getElementById('edit_perihal').value = data.perihal;
        document.getElementById('edit_visibility').value = data.visibility;
        const form = document.getElementById('editMemoForm');
      form.action = `/admin/template-dokumen/${id}`;
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