@extends('layouts.app')
@section('title', 'IT Compliance & Controlling')
@section('content')

<h2 style="text-decoration: underline; margin-bottom: 10px; margin-left:60px">History Data</h2>

<form method="GET" action="{{ route('admin.track.history') }}"
  style="margin-bottom: 10px; margin-left:15px; display: flex; justify-content: flex-start; gap: 10px;">
  <input type="text" name="search" placeholder="Cari data..." value="{{ request('search') }}"
    style="margin-left:45px;width: 250px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;">
  <button type="submit"
    style="background-color: #2196F3; color: white; padding: 8px 14px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
    Cari
  </button>
  @if(request('search'))
  <a href="{{ route('admin.track.history') }}"
    style="background-color: #9e9e9e; color: white; padding: 8px 14px; text-decoration: none; border-radius: 4px; font-size: 13px;">Reset</a>
  @endif
</form>

<table style="border-collapse: collapse; width: 100%; text-align: center; margin-left:12px">
  <thead>
    <tr style="background-color: #f2f2f2;">
      <th style="padding: 10px;">No.</th>
      <th style="padding: 10px;">Tanggal Pengajuan</th>
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
        {{ \Carbon\Carbon::parse($item->created_at)->format('d-M-Y H:i:s') }}
      </td>
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

@endsection