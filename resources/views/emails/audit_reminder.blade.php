<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pengingat Audit Pending</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color:#f8f9fa; padding:20px; color:#333;">
    <div
        style="max-width:600px; margin:0 auto; background:#fff; border-radius:10px; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color:#0d6efd; text-align:center;">
            📋 Pengingat Audit Pending
        </h2>

        @if(Str::contains($jenis, 'H-10'))
        <p>Dear Bpk/Ibu,</p>
        <p>Tindak Laporan Hasil Audit (TLHA) berikut akan <strong>jatuh tempo dalam 10 hari lagi</strong>
        </p>
        @elseif(Str::contains($jenis, 'H+7'))
        <p>Dear Bpk/Ibu,</p>
        <p>Tindak Laporan Hasil Audit (TLHA) berikut <strong>sudah jatuh tempo 7 hari</strong> dari tanggal selesai yang
            sudah
            disepakati
        </p>
        @elseif(Str::contains($jenis, 'H+14'))
        <p>Dear Bpk/Ibu,</p>
        <p>Tindak Laporan Hasil Audit (TLHA) berikut <strong>sudah lewat 14 hari</strong> dari tanggal selesai yang
            sudah
            disepakati
        </p>
        @else
        <p>Dear Bpk/Ibu,</p>
        <p>Berikut adalah reminder untuk Tindak Laporan Hasil Audit (TLHA) berikut yang masih berstatus <strong>Jatuh
                Tempo</strong>:</p>
        @endif

        <table style="width:100%; border-collapse:collapse; margin-top:15px;">
            <tr>
                <td style="padding:6px 8px; border-bottom:1px solid #ddd;"><strong>Divisi</strong></td>
                <td style="padding:6px 8px; border-bottom:1px solid #ddd;">{{ $audit->divisi ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding:6px 8px; border-bottom:1px solid #ddd;"><strong>Kegiatan</strong></td>
                <td style="padding:6px 8px; border-bottom:1px solid #ddd;">{{ $audit->kegiatan ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding:6px 8px; border-bottom:1px solid #ddd;"><strong>Tanggal Mulai</strong></td>
                <td style="padding:6px 8px; border-bottom:1px solid #ddd;">{{
                    \Carbon\Carbon::parse($audit->tanggal_mulai)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td style="padding:6px 8px; border-bottom:1px solid #ddd;"><strong>Tanggal Selesai</strong></td>
                <td style="padding:6px 8px; border-bottom:1px solid #ddd;">{{
                    \Carbon\Carbon::parse($audit->tanggal_selesai)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td style="padding:6px 8px;"><strong>Status</strong></td>
                <td style="padding:6px 8px;">{{ $audit->status ?? '-' }}</td>
            </tr>
        </table>

        <p style="margin-top:20px;">
            Mohon agar segera <strong>ditindaklanjuti</strong> sesuai dengan jadwal yang telah disepakati.
        </p>

        <p>Terima kasih,<br><strong>Divisi IT Compliance</strong></p>

        <hr style="margin-top:30px; border:none; border-top:1px solid #ddd;">
        <p style="font-size:12px; color:#777; text-align:center;">
            Email ini dikirim secara otomatis oleh sistem audit internal.<br>
            Mohon tidak membalas email ini secara langsung.
        </p>
    </div>
</body>

</html>