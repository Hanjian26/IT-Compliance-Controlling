@extends('layouts.app')
@section('title', 'IT Compliance & Controlling')
@section('content')

<h2 style="text-decoration: underline; margin-bottom: 10px; margin-left:60px">History Data</h2>
<form method="GET" action="{{ route('admin.track-history.index') }}"
  style="margin-bottom: 10px; margin-left:60px; display: flex; justify-content: flex-start; gap: 10px;">

  <select name="year" style="padding:8px; border:1px solid #ccc; border-radius:4px; font-size:13px;">

    <option value="">-- Tahun --</option>

    @for ($y = date('Y'); $y >= 2020; $y--)
    <option value="{{ $y }}" {{ request('year')==$y ? 'selected' : '' }}>
      {{ $y }}
    </option>
    @endfor

  </select>

  <!-- Pencarian teks -->
  <input type="text" name="search" placeholder="Keyword" value="{{ request('search') }}"
    style="width: 200px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;">

  <!-- Filter tanggal -->
  {{-- <input type="date" name="start_date" value="{{ request('start_date') }} min=" 2000-01-01" max="2099-12-31"
    onkeydown="return false" style=" padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;">
  <span style="margin-top: 8px;">-</span>
  <input type="date" name="end_date" value="{{ request('end_date') }} min=" 2000-01-01" max="2099-12-31"
    onkeydown="return false" style=" padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;"> --}}

  <!-- Tombol submit -->
  <button type="submit"
    style="background-color: #2196F3; color: white; padding: 8px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
    Filter
  </button>

  @if(request()->hasAny(['search','start_date','end_date']))
  <a href="{{ route('admin.track-history.index') }}"
    style="background-color: #9e9e9e; color: white; padding: 8px 14px; text-decoration: none; border-radius: 4px; font-size: 13px;">
    Reset
  </a>
  @endif
</form>

<table style="border-collapse: collapse; width: 100%; text-align: center; margin-left:12px">
  <thead>
    <tr style="background-color: #f2f2f2;">
      <th style="padding: 10px;">No.</th>
      <th style="padding: 10px;">Tanggal</th>
      <th style="padding: 10px;">NIK</th>
      <th style="padding: 10px;">Nama</th>
      <th style="padding: 10px;">Perihal</th>
      <th style="padding: 10px;">Status</th>

    </tr>
  </thead>
  <tbody>
    @foreach ($data as $key => $item)
    <tr>
      <td style="padding: 10px; font-size: 12px;">{{ $data->firstItem() + $key }}</td>
      <td style="padding: 10px; font-size: 12px;">
        {{ \Carbon\Carbon::parse($item->created_at)->format('d-M-Y H:i:s') }}</td>

      </td>
      <td style="padding: 10px; font-size: 12px;">{{ $item->nik }}</td>
      <td style="padding: 10px; font-size: 12px;">{{ $item->nama }}</td>
      <td style="padding: 10px; font-size: 12px;">{{ $item->perihal }}</td>
      <td style="padding: 10px; font-size: 12px;">{{ $item->status }}</td>
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
          style="text-decoration: none; color: #333; padding: 4px 8px; border: 1px solid #ccc; border-radius: 4px;">
          {{ $page }}
        </a>
      </li>
      @endforeach
    </ul>
  </div>
</div>

<script>
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