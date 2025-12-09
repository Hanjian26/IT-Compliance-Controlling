@extends('layouts.app')
@section('title', 'IT Compliance')
@section('content')
<style>
    .btn-tambah-pic,
    .btn-edit-pic {
        padding: 8px;
        background-color: #2b2d42;
        color: white;
        border: none;
        border-radius: 4px;
        margin-top: 10px;
        font-size: 12px;
        transition: background-color 0.2s ease;
    }

    /* Efek hover untuk dua tombol */
    .btn-tambah-pic:hover,
    .btn-edit-pic:hover {
        background-color: #5763e1;
        cursor: pointer;
    }
</style>

@php
$user = Auth::user();
$level = $user->level ?? null;
@endphp


<!-- Search Bar -->


<h2 style="text-decoration: underline; margin-bottom: 10px; margin-left:60px">Tindak Lanjut Hasil Audit (TLHA)</h2>

<!-- Container Utama -->
<!-- Tombol Tambah Dokumen -->
<br>
@if($level != 2)

<div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">

    <button onclick="openPopup()" onmouseover="this.style.backgroundColor='#5763e1'"
        onmouseout="this.style.backgroundColor='#4CAF50'"
        style="background-color: #4CAF50; color: white; padding: 8px 16px; text-decoration: none; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px;">

        {{-- <img width="20" height="20" src="https://img.icons8.com/wired/64/add-rule.png" alt="add-icon" /> --}}
        Tambah Tindak Lanjut Hasil Audit
    </button>
</div>




<form method="GET" action="{{ route('audit.tlha') }}"
    style="margin-bottom: 10px; margin-left:15px; display: flex; justify-content: flex-start; gap: 10px;">
    <input type="text" name="search" placeholder="Cari data..." value="{{ request('search') }}"
        style="width: 250px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;">
    <button type="submit"
        style="background-color: #2196F3; color: white; padding: 8px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
        Cari
    </button>
    @if(request('search'))
    <a href="{{ route('audit.tlha') }}"
        style="background-color: #9e9e9e; color: white; padding: 8px 14px; text-decoration: none; border-radius: 4px; font-size: 13px;">
        Reset
    </a>
    @endif

</form>
@endif
<!-- Bungkus tabel dengan div agar bisa scroll kanan-kiri -->
<div style="width: 100%; overflow-x: auto; margin-bottom: 15px; margin-left:10px">
    <table style="border-collapse: collapse; width: 100%; min-width: 1200px; text-align: center;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 10px;">No.</th>
                <th style="padding: 10px;">Divisi</th>
                <th style="padding: 10px;">Kegiatan</th>
                <th style="padding: 10px;">Target Penyelesaian</th>
                <th style="padding: 10px;">PIC</th>
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
                {{-- <td style="padding: 10px; font-size: 12px;">
                    {{(\Carbon\Carbon::parse($item->tanggal_mulai)->format('d-M-Y')) }}
                </td> --}}
                <td style="padding: 10px; font-size: 12px;">
                    @if ($item->tanggal_selesai)
                    {{(\Carbon\Carbon::parse($item->tanggal_selesai)->format('d-M-Y')) }}
                    @else
                    TBA
                    @endif
                </td>
                <td style="padding: 10px; font-size: 12px;">{{ implode(', ', json_decode($item->pic, true))}}</td>
                <td style="padding: 10px; font-size: 12px;">{{ $item->auditor }}</td>
                <td style="padding: 10px; font-size: 12px;">{{ $item->reviewer }}</td>
                <td
                    style="padding: 10px; font-size: 12px; color: {{ $item->status == 'Pending' ? 'red' : 'inherit' }};">
                    {{ $item->status }}
                </td>

                <td style="text-align:left; padding: 10px; font-size: 12px;
           max-width: 200px; white-space: normal; word-wrap: break-word;">
                    {{ $item->keterangan }}
                </td>

                <td style="padding: 10px; font-size: 11px;">
                    <div
                        style="padding: 10px;font-size: 11px;display: flex;justify-content: center;align-items: center;gap:10px;">
                        <!-- Lihat -->
                        <a href="{{ asset('storage/'.$item->file_laporan) }}" target="_blank"
                            style="display: inline-flex; align-items: center; gap: 5px; text-decoration: none; color: inherit;">
                            <img width="18" height="18" src="https://img.icons8.com/ios/50/visible--v1.png"
                                alt="lihat-icon" style="display: block;" />
                            <!-- <span style="font-size: 12px;">Lihat</span> -->
                        </a>
                        @if($level != 2)
                        <!-- Edit -->
                        <a href="javascript:void(0);" onclick="editMemo({{ $item->id }})" title="Edit"
                            style="display: inline-flex; align-items: center; gap: 5px; text-decoration: none; color: inherit;">
                            <img width="18" height="18" src="https://img.icons8.com/ios/50/create-new.png"
                                alt="edit-icon" style="display: block;" />
                        </a>
                        <!-- Hapus -->
                        <form action="{{ route('audit.tlha.destroy', $item->id) }}" method="POST"
                            onsubmit="return confirmDelete()" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Hapus"
                                style="display: inline-flex; align-items: center; background: none; border: none; padding: 0; font-size: 12px; color: inherit; cursor: pointer;">
                                <img width="18" height="15" src="https://img.icons8.com/ios/50/trash--v1.png"
                                    alt="hapus-icon" style="display: block;" />
                            </button>
                            @endif
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
            <u>Edit Tindak Lanjut Hasil Audit</u>
        </h3>


        <form id="editMemoForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 15px;">
                <label for="edit_divisi">Divisi<span style="color: red;">*</span>:</label>
                <select name="divisi" id="edit_divisi" required
                    style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <option value="#">-- Pilih Department --</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->department }}">{{ $dept->department }}</option>
                    @endforeach
                    {{-- <option value="SD1_SSD1">SD1_SSD1</option>
                    <option value="SD2 Payroll">SD2 Payroll</option>
                    <option value="SD2 Non Payroll">SD2 Non Payroll</option>
                    <option value="SD3_SSD3">SD3_SSD3</option>
                    <option value="SD4_SSD4">SD4_SSD4</option>
                    <option value="SD5_SSD5">SD5_SSD5</option>
                    <option value="SD6_SSD6">SD6_SSD6</option>
                    <option value="SD7_SSD7">SD7_SSD7</option>
                    <option value="SD8_SSD8">SD8_SSD8</option>
                    <option value="IT PMO">IT PMO</option>
                    <option value="IT Compliance">IT Compliance</option> --}}
                </select>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="edit_kegiatan">Kegiatan<span style="color: red;">*</span>:</label>
                <input type="text" name="kegiatan" id="edit_kegiatan" required
                    style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            {{-- <div style="margin-bottom: 15px;">
                <label for="edit_tanggal_mulai">Tanggal Mulai<span style="color: red;">*</span>:</label>
                <input type="date" name="tanggal_mulai" id="edit_tanggal_mulai" lang="id" min="2000-01-01"
                    max="2099-12-31" onkeydown="return false" required style="width: 97%; padding: 8px;"
                    style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div> --}}

            <div style="margin-bottom: 15px;">
                <label for="edit_tanggal_selesai">Tanggal Selesai:</label>
                <input type="date" name="tanggal_selesai" id="edit_tanggal_selesai" lang="id" min="2000-01-01"
                    max="2099-12-31" onkeydown="return false" required style="width: 97%; padding: 8px;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Pilih PIC:</label><br>
                <select id="edit_dropdownPIC" style="width:89%; padding:8px;">
                    <option value="#">Pilih PIC</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->department }}">{{ $dept->department }}</option>
                    @endforeach
                </select>

                <button type="button" onclick="tambahEditPIC()" class="btn-tambah-pic"
                    style="padding:8px; color:white; border:none; border-radius:4px; margin-top:10px; font-size:12px;">
                    Tambah
                </button>

                <br><br>

                <label>PIC Terpilih:</label>
                <!-- hidden input untuk dikirim ke server -->
                <input type="hidden" id="edit_hasilPIC" name="pic">

                <!-- daftar PIC -->
                <div id="edit_daftarPIC"
                    style="border:1px solid #ccc; padding:10px; border-radius:5px; background:#f9f9f9; min-height:40px;">
                </div>
            </div>



            <div style="margin-bottom: 15px;">
                <label for="edit_auditor">Auditor <span style="color: red;">*</span>:</label>
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
                <select name="status" id="edit_status" required
                    style="width: 100%; padding: 8px; box-sizing: border-box;">
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
                <label for="edit_file_laporan">Upload Dokumen (Max Size: 10 MB):</label>
                <input type="file" name="file_laporan" id="edit_file_laporan" accept=".pdf,.doc,.docx,.zip"
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
            <u>Tambah Tindak Lanjut Hasil Audit</u>
        </h3>


        <form action="{{ route('audit.tlha.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom: 15px;">
                <label for="edit_divisi">Divisi<span style="color: red;">*</span>:</label>
                <select name="divisi" id="edit_divisi" required
                    style="width: 100%; padding: 8px; box-sizing: border-box;">
                    <option value="#">-- Pilih Divisi --</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->department }}">{{ $dept->department }}</option>
                    @endforeach
                    {{-- <option value="SD1_SSD1">SD1_SSD1</option>
                    <option value="SD2 Payroll">SD2 Payroll</option>
                    <option value="SD2 Non Payroll">SD2 Non Payroll</option>
                    <option value="SD3_SSD3">SD3_SSD3</option>
                    <option value="SD4_SSD4">SD4_SSD4</option>
                    <option value="SD5_SSD5">SD5_SSD5</option>
                    <option value="SD6_SSD6">SD6_SSD6</option>
                    <option value="SD7_SSD7">SD7_SSD7</option>
                    <option value="SD8_SSD8">SD8_SSD8</option>
                    <option value="IT PMO">IT PMO</option>
                    <option value="IT Compliance">IT Compliance</option> --}}
                </select>
            </div>


            <div style="margin-bottom: 15px;">
                <label for="kegiatan">Kegiatan<span style="color: red;">*</span>:</label>
                <input type="text" name="kegiatan" id="kegiatan" required
                    style="width: 100%; padding: 8px; box-sizing: border-box;">
            </div>

            {{--
            <div style="margin-bottom: 15px;">
                <label for="tanggal_mulai">Tanggal Mulai<span style="color: red;">*</span>:</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" required lang="id" min="2000-01-01"
                    max="2099-12-31" onkeydown="return false" style="width:97%; padding:8px;" autocomplete="off">
            </div> --}}
            <div style="margin-bottom: 15px;">
                <label for="tanggal_selesai">Tanggal Selesai:</label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" lang="id" min="2000-01-01"
                    max="2099-12-31" onkeydown="return false" required style="width: 97%; padding: 8px;">
            </div>

            <div style="margin-bottom:15px;">
                <label>Pilih PIC:</label><br>
                <select id="dropdownPIC" style="width:89%; padding:8px;">
                    <option value="#">Pilih PIC</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->department }}">{{ $dept->department }}</option>
                    @endforeach
                </select>

                <button class="btn-tambah-pic" type="button" onclick="tambahPIC()">
                    Tambah
                </button>


                <br><br>

                <label>PIC Terpilih:</label>
                <!-- input hidden untuk dikirim ke server -->
                <input type="hidden" id="hasilPIC" name="pic">

                <!-- tempat menampilkan daftar PIC terpilih -->
                <div id="daftarPIC"
                    style="border:1px solid #ccc; padding:10px; border-radius:5px; background:#f9f9f9; min-height:40px;">
                </div>
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
                <label for="file_laporan">Upload Dokumen (Max Size: 10 MB)<span style="color: red;">*</span>:</label>
                <input type="file" name="file_laporan" id="file_laporan" accept=".pdf,.doc,.docx,.zip"
                    style="width: 100%; padding: 6px;">
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

let editDaftarPIC = [];

function tambahEditPIC() {
    const dropdown = document.getElementById('edit_dropdownPIC');
    const selectedPIC = dropdown.value;
    const daftarDiv = document.getElementById('edit_daftarPIC');
    const hasilInput = document.getElementById('edit_hasilPIC');

    if (!editDaftarPIC.includes(selectedPIC)) {
        editDaftarPIC.push(selectedPIC);
    } else {
        alert(selectedPIC + ' sudah dipilih!');
        return;
    }

    renderEditDaftarPIC(daftarDiv, hasilInput);
}

function hapusEditPIC(pic) {
    const daftarDiv = document.getElementById('edit_daftarPIC');
    const hasilInput = document.getElementById('edit_hasilPIC');

    editDaftarPIC = editDaftarPIC.filter(item => item !== pic);
    renderEditDaftarPIC(daftarDiv, hasilInput);
}

function renderEditDaftarPIC(daftarDiv, hasilInput) {
    daftarDiv.innerHTML = '';

    editDaftarPIC.forEach(pic => {
        const badge = document.createElement('span');
        badge.textContent = pic + ' ';
        badge.style.display = 'inline-block';
        badge.style.background = '#edf2f4';
        badge.style.border = '1px solid #ccc';
        badge.style.borderRadius = '15px';
        badge.style.padding = '5px 10px';
        badge.style.margin = '3px';
        badge.style.fontSize = '13px';

        const btn = document.createElement('button');
        btn.textContent = '×';
        btn.style.marginLeft = '5px';
        btn.style.color = 'red';
        btn.style.border = 'none';
        btn.style.background = 'transparent';
        btn.style.cursor = 'pointer';
        btn.onclick = () => hapusEditPIC(pic);

        badge.appendChild(btn);
        daftarDiv.appendChild(badge);
    });

    hasilInput.value = editDaftarPIC.join(', ');
}


 let daftarPIC = [];

    function tambahPIC() {
        const dropdown = document.getElementById('dropdownPIC');
        const selectedPIC = dropdown.value;
        const daftarDiv = document.getElementById('daftarPIC');
        const hasilInput = document.getElementById('hasilPIC');

        // jangan duplikat
        if (!daftarPIC.includes(selectedPIC)) {
            daftarPIC.push(selectedPIC);
        } else {
            alert(selectedPIC + ' sudah dipilih!');
            return;
        }

        // render ulang daftar PIC
        renderDaftarPIC(daftarDiv, hasilInput);
    }

    function hapusPIC(pic) {
        const daftarDiv = document.getElementById('daftarPIC');
        const hasilInput = document.getElementById('hasilPIC');

        // hapus PIC dari array
        daftarPIC = daftarPIC.filter(item => item !== pic);

        // render ulang tampilan daftar
        renderDaftarPIC(daftarDiv, hasilInput);
    }

    function renderDaftarPIC(daftarDiv, hasilInput) {
        // bersihkan isi tampilan
        daftarDiv.innerHTML = '';

        // tampilkan setiap PIC dalam bentuk badge + tombol hapus
        daftarPIC.forEach(pic => {
            const badge = document.createElement('span');
            badge.textContent = pic + ' ';
            badge.style.display = 'inline-block';
            badge.style.background = '#edf2f4';
            badge.style.border = '1px solid #ccc';
            badge.style.borderRadius = '15px';
            badge.style.padding = '5px 10px';
            badge.style.margin = '3px';
            badge.style.fontSize = '13px';

            const btn = document.createElement('button');
            btn.textContent = '×';
            btn.style.marginLeft = '5px';
            btn.style.color = 'red';
            btn.style.border = 'none';
            btn.style.background = 'transparent';
            btn.style.cursor = 'pointer';
            btn.onclick = () => hapusPIC(pic);

            badge.appendChild(btn);
            daftarDiv.appendChild(badge);
        });

        // simpan ke input hidden agar bisa dikirim ke controller
        hasilInput.value = daftarPIC.join(', ');
    }

function closePopup() {
    document.getElementById('popupForm').style.display = 'none';
}

function closeEdit() {
    document.getElementById('editForm').style.display = 'none';
}

function editMemo(id) {
    fetch(`/admin/tlha-audit/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('edit_divisi').value = data.divisi;
            document.getElementById('edit_kegiatan').value = data.kegiatan;
            // document.getElementById('edit_tanggal_mulai').value = data.tanggal_mulai;
            document.getElementById('edit_tanggal_selesai').value = data.tanggal_selesai;
            document.getElementById('edit_auditor').value = data.auditor;
            document.getElementById('edit_reviewer').value = data.reviewer;
            document.getElementById('edit_status').value = data.status;
            document.getElementById('edit_keterangan').value = data.keterangan;

            // ✅ isi PIC yang sudah ada
            const daftarDiv = document.getElementById('edit_daftarPIC');
            const hasilInput = document.getElementById('edit_hasilPIC');

            try {
                // jika data.pic disimpan sebagai JSON string
                editDaftarPIC = Array.isArray(data.pic)
                    ? data.pic
                    : JSON.parse(data.pic);
            } catch {
                // jika data.pic disimpan sebagai string "IT PMO, SD8_SSD8"
                editDaftarPIC = data.pic
                    ? data.pic.split(',').map(i => i.trim())
                    : [];
            }

            renderEditDaftarPIC(daftarDiv, hasilInput);

            const form = document.getElementById('editMemoForm');
            form.action = `/admin/tlha-audit/${id}`;
            document.getElementById('editForm').style.display = 'flex';
        });
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