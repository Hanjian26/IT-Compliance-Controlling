@extends('layouts.app')
@section('title', 'IT Compliance')
@section('content')
<h2 style="text-decoration: underline; margin-bottom: 10px; margin-left:60px">Working Paper Audit</h2>

<!-- Container Utama -->
<!-- Tombol Tambah Dokumen -->
<div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
  <button onclick="openPopup()"
    style="background-color: #4CAF50; color: white; padding: 8px 16px; text-decoration: none; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px;">

    <img width="20" height="20" src="https://img.icons8.com/wired/64/add-rule.png" alt="add-icon" />
    Tambah Working Paper
  </button>
</div>
<form method="GET" action="{{ route('audit.wp') }}"
  style="margin-bottom: 10px; margin-left:15px; display: flex; justify-content: flex-start; gap: 10px;">

  <input type="text" name="search" placeholder="Cari data..." value="{{ request('search') }}"
    style="width: 250px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;">

  <button type="submit"
    style="background-color: #2196F3; color: white; padding: 8px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
    Cari
  </button>

  @if(request('search'))
  <a href="{{ route('audit.wp') }}"
    style="background-color: #9e9e9e; color: white; padding: 8px 14px; text-decoration: none; border-radius: 4px; font-size: 13px;">
    Reset
  </a>
  @endif
</form>

<!-- Bungkus tabel dengan div agar bisa scroll kanan-kiri -->
<div style="width: 100%; overflow-x: auto; margin-bottom: 15px;">
  <table style="border-collapse: collapse; width: 100%; min-width: 1200px; text-align: center;">
    <thead>
      <tr style="background-color: #f2f2f2;">
        <th style="padding: 10px;">No.</th>
        <th style="padding: 10px;">Divisi</th>
        <th style="padding: 10px;">Kegiatan</th>
        <th style="padding: 10px;">Tanggal Mulai</th>
        <th style="padding: 10px;">Tanggal Selesai</th>
        <th style="padding: 10px;">Auditor</th>
        <th style="padding: 10px;">Reviewer</th>
        <th style="padding: 10px;">Status</th>
        <th style="padding: 10px;">Keterangan</th>
        <th style="padding: 10px;">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $key => $item)
      <tr>
        <td style="padding: 10px; font-size: 12px;">{{ $data->firstItem() + $key }}</td>
        <td style="padding: 10px; font-size: 12px;">{{ $item->divisi }}</td>
        <td style="padding: 10px; font-size: 12px;">{{ $item->kegiatan }}</td>
        <td style="padding: 10px; font-size: 12px;">
          {{(\Carbon\Carbon::parse($item->tanggal_mulai)->format('d-M-Y')) }}
        </td>
        <td style="padding: 10px; font-size: 12px;">
          @if ($item->tanggal_selesai)
          {{(\Carbon\Carbon::parse($item->tanggal_selesai)->format('d-M-Y')) }}
          @else
          TBA
          @endif
        </td>

        <td style="padding: 10px; font-size: 12px;">{{ $item->auditor }}</td>
        <td style="padding: 10px; font-size: 12px;">{{ $item->reviewer }}</td>
        <td style="padding: 10px; font-size: 12px; color: {{ $item->status == 'Pending' ? 'red' : 'inherit' }};">
          {{ $item->status }}
        </td>
        <td style="padding: 10px; font-size: 12px;">{{ $item->keterangan }}</td>
        <td style="padding: 10px; font-size: 11px;">
          <div style="display: inline-flex; justify-content: center; gap: 10px; flex-wrap: wrap; align-items: center;">
            <!-- Lihat -->
            <a href="{{ asset('storage/'.$item->file_wp) }}" target="_blank"
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
            </a>
            <!-- Hapus -->
            <form action="{{ route('audit.wp.destroy', $item->id) }}" method="POST" onsubmit="return confirmDelete()"
              style="display: inline;">
              @csrf
              @method('DELETE')
              <button type="submit" title="Hapus"
                style="display: inline-flex; align-items: center; background: none; border: none; padding: 0; font-size: 12px; color: inherit; cursor: pointer;">
                <img width="18" height="15" src="https://img.icons8.com/ios/50/trash--v1.png" alt="hapus-icon"
                  style="display: block;" />
              </button>
            </form>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

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
  <div style="background-color: white; padding: 20px; border-radius: 10px; 
              width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto;
              box-shadow: 0 4px 12px rgba(0,0,0,0.2); position: relative;">

    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 20px; text-align:center;">
      <u>Edit Working Paper</u>
    </h3>

    <form id="editMemoForm" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div style="margin-bottom: 15px;">
        <label for="edit_divisi">Divisi<span style="color: red;">*</span>:</label>
        <select name="divisi" id="edit_divisi" required style="width: 100%; padding: 8px; box-sizing: border-box;">
          <option value="#">-- Pilih Department --</option>
          <option value="SD1">SD1</option>
          <option value="SD2PR">SD2 Payroll</option>
          <option value="SD2NPR">SD2 Non Payroll</option>
          <option value="SD3">SD3</option>
          <option value="SD4">SD4</option>
          <option value="SD5">SD5</option>
          <option value="SD6">SD6</option>
          <option value="SD7">SD7</option>
          <option value="">TBA</option>
        </select>
      </div>

      <div style="margin-bottom: 15px;">
        <label for="edit_kegiatan">Kegiatan<span style="color: red;">*</span>:</label>
        <input type="text" name="kegiatan" id="edit_kegiatan" required
          style="width: 100%; padding: 8px; box-sizing: border-box;">
      </div>

      <div style="margin-bottom: 15px;">
        <label for="edit_tanggal_mulai">Tanggal Mulai<span style="color: red;">*</span>:</label>
        <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" lang="id" min="2000-01-01" max="2099-12-31"
          onkeydown="return false" required style="width: 100%; padding: 8px;"
          style="width: 100%; padding: 8px; box-sizing: border-box;">
      </div>

      <div style="margin-bottom: 15px;">
        <label for="edit_tanggal_selesai">Tanggal Selesai:</label>
        <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" lang="id" min="2000-01-01" max="2099-12-31"
          onkeydown="return false" required style="width: 100%; padding: 8px;">
      </div>

      <div style="margin-bottom: 15px;">
        <label for="edit_auditor">Auditor<span style="color: red;">*</span>:</label>
        <input type="text" name="auditor" id="edit_auditor" required
          style="width: 100%; padding: 8px; box-sizing: border-box;">
      </div>

      <div style="margin-bottom: 15px;">
        <label for="edit_reviewer">Reviewer<span style="color: red;">*</span>:</label>
        <input type="text" name="reviewer" id="edit_reviewer" required
          style="width: 100%; padding: 8px; box-sizing: border-box;">
      </div>


      <div style="margin-bottom: 15px;">
        <label for="edit_status">Status<span style="color: red;">*</span>:</label>
        <select name="status" id="edit_status" required style="width: 100%; padding: 8px; box-sizing: border-box;">
          <option value="">-- Pilih Status -- </option>
          <option value="Pending">Pending</option>
          <option value="Selesai">Selesai</option>
        </select>
      </div>

      <div style="margin-bottom: 15px;">
        <label for="edit_keterangan">Keterangan:</label>
        <textarea name="keterangan" id="edit_keterangan" rows="2"
          style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
      </div>

      <div style="margin-bottom: 20px;">
        <label for="edit_file_wp">Upload Dokumen (PDF, DOCX, dll)<span style="color: red;">*</span>:</label>
        <input type="file" name="file_wp" id="edit_file_wp" accept=".pdf,.doc,.docx,.zip"
          style="width: 100%; padding: 6px;">


      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap;">
        <button type="button" onclick="closeEdit()" style="background-color: #e0e0e0; color: black; padding: 8px 14px; cursor: pointer; 
                       border: none; border-radius: 4px;">
          Batal
        </button>
        <button type="submit" style="background-color: #4caf50; color: white; padding: 8px 14px; cursor: pointer; 
                       border: none; border-radius: 4px;">
          Perbarui
        </button>
      </div>
    </form>
  </div>
</div>


<!-- Modal Tambah Dokumen -->
<div id="popupForm" style="display: none; position: fixed; top: 0; left: 0; 
            width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); 
            z-index: 999; align-items: center; justify-content: center;">
  <div style="background-color: white; padding: 20px; border-radius: 10px; 
              width: 90%; max-width: 600px; max-height: 90vh; 
              overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.2); position: relative;">

    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 20px; text-align:center;">
      <u>Tambah Working Paper</u>
    </h3>

    <form action="{{ route('audit.wp.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div style="margin-bottom: 15px;">
        <label for="divisi">Divisi<span style="color: red;">*</span>:</label>
        <select name="divisi" id="divisi" required style="width: 100%; padding: 8px; box-sizing: border-box;">
          <option value="">-- Pilih Department -- </option>
          <option value="SD1">SD1</option>
          <option value="SD2PR">SD2 Payroll</option>
          <option value="SD2NPR">SD2 Non Payroll</option>
          <option value="SD3">SD3</option>
          <option value="SD4">SD4</option>
          <option value="SD5">SD5</option>
          <option value="SD6">SD6</option>
          <option value="SD7">SD7</option>
          <option value="">TBA</option>
        </select>
      </div>


      <div style="margin-bottom: 15px;">
        <label for="kegiatan">Kegiatan<span style="color: red;">*</span>:</label>
        <input type="text" name="kegiatan" id="kegiatan" required
          style="width: 100%; padding: 8px; box-sizing: border-box;">
      </div>

      <div style="margin-bottom: 15px;">
        <label for="tanggal_mulai">Tanggal Mulai<span style="color: red;">*</span>:</label>
        <input type="date" name="tanggal_mulai" id="tanggal_mulai" lang="id" min="2000-01-01" max="2099-12-31"
          onkeydown="return false" required style="width: 100%; padding: 8px;">
      </div>

      <div style="margin-bottom: 15px;">
        <label for="tanggal_selesai">Tanggal Selesai:</label>
        <input type="date" name="tanggal_selesai" id="tanggal_selesai" lang="id" min="2000-01-01" max="2099-12-31"
          onkeydown="return false" required style="width: 100%; padding: 8px;">
      </div>

      <div style="margin-bottom: 15px;">
        <label for="auditor">Auditor<span style="color: red;">*</span>:</label>
        <input type="text" name="auditor" id="auditor" required
          style="width: 100%; padding: 8px; box-sizing: border-box;">
      </div>

      <div style="margin-bottom: 15px;">
        <label for="reviewer">Reviewer<span style="color: red;">*</span>:</label>
        <input type="text" name="reviewer" id="reviewer" required
          style="width: 100%; padding: 8px; box-sizing: border-box;">
      </div>

      <div style="margin-bottom: 15px;">
        <label for="status">Status<span style="color: red;">*</span>:</label>
        <select name="status" id="status" required style="width: 100%; padding: 8px; box-sizing: border-box;">
          <option value="">-- Pilih Status -- </option>
          <option value="Pending">Pending</option>
          <option value="Selesai">Selesai</option>
        </select>
      </div>

      <div style="margin-bottom: 15px;">
        <label for="keterangan">Keterangan:</label>
        <textarea name="keterangan" id="keterangan" rows="2"
          style="width: 100%; padding: 8px; box-sizing: border-box;"></textarea>
      </div>

      <div style="margin-bottom: 20px;">
        <label for="file_wp">Upload Dokumen (PDF, DOCX, dll)<span style="color: red;">*</span>:</label>
        <input type="file" name="file_wp" id="file_wp" accept=".pdf,.doc,.docx,.zip" style="width: 100%; padding: 6px;">
      </div>

      <div style="display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap;">
        <button type="button" onclick="closePopup()"
          style="background-color: #e0e0e0; color: black; padding: 8px 14px; cursor: pointer; border: none; border-radius: 4px;">
          Batal
        </button>
        <button type="submit"
          style="background-color: #4caf50; color: white; padding: 8px 14px; cursor: pointer; border: none; border-radius: 4px;">
          Tambah
        </button>
      </div>
    </form>
  </div>
</div>


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
    fetch(`/admin/audit-working-paper/${id}/edit`) // <-- pakai backtick atau string
    .then(res => res.json())
    .then(data => {
        document.getElementById('edit_divisi').value = data.divisi;
        document.getElementById('edit_kegiatan').value = data.kegiatan;
        document.getElementById('edit_tanggal_mulai').value = data.tanggal_mulai;
        document.getElementById('edit_tanggal_selesai').value = data.tanggal_selesai;
        document.getElementById('edit_auditor').value = data.auditor;
        document.getElementById('edit_reviewer').value = data.reviewer;
        document.getElementById('edit_status').value = data.status;
        document.getElementById('edit_keterangan').value = data.keterangan;

        const form = document.getElementById('editMemoForm');
        form.action = `/admin/audit-working-paper/${id}`; // <-- perbaiki jadi string
        document.getElementById('editForm').style.display = 'flex';
    });
}


  function confirmDelete() {
    return confirm('Apakah Anda yakin ingin menghapus file ini?');
  }

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