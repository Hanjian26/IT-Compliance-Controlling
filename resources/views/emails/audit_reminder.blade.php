<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pengingat Audit Pending</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>
        @if($jenis === 'H-10')
        Pengingat Audit Pending (H-10)
        @else
        Pengingat Audit Pending — Sudah Jatuh Tempo
        @endif
    </h2>

    <p>Halo, ini merupakan reminder otomatis</p>

    @if($jenis === 'H-10')
    <p>Audit berikut akan <strong>selesai 10 hari lagi</strong>, tetapi statusnya masih <strong>Pending</strong>:</p>
    @else
    <p>Audit berikut <strong>sudah jatuh tempo hari ini</strong> dan masih berstatus <strong>Pending</strong>:</p>
    @endif

    <ul>
        <li><strong>Divisi:</strong> {{ $audit->divisi }}</li>
        <li><strong>Kegiatan:</strong> {{ $audit->kegiatan }}</li>
        <li><strong>Tanggal Mulai:</strong> {{ \Carbon\Carbon::parse($audit->tanggal_mulai)->format('d/m/Y') }}</li>
        <li><strong>Tanggal Selesai:</strong> {{ \Carbon\Carbon::parse($audit->tanggal_selesai)->format('d/m/Y') }}</li>
        <li><strong>Status:</strong> {{ $audit->status }}</li>
    </ul>

    <p>Mohon segera ditindaklanjuti atau ubah status audit untuk menghindari keterlambatan laporan.</p>

    <p>Terima kasih,<br><strong>Divisi IT Compliance</strong></p>
</body>

</html>