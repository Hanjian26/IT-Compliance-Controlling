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
        <p>Halo,</p>
        <p>Audit berikut akan <strong>selesai 10 hari lagi</strong>, tetapi statusnya masih <strong>Pending</strong>:
        </p>
        @elseif(Str::contains($jenis, 'H+7'))
        <p>Halo,</p>
        <p>Audit berikut <strong>sudah lewat 7 hari</strong> dari tanggal selesai dan masih berstatus
            <strong>Pending</strong>:
        </p>
        @elseif(Str::contains($jenis, 'H+14'))
        <p>Halo,</p>
        <p>Audit berikut <strong>sudah lewat 14 hari</strong> dari tanggal selesai dan masih berstatus
            <strong>Pending</strong>:
        </p>
        @else
        <p>Halo,</p>
        <p>Berikut adalah pengingat untuk audit yang masih berstatus <strong>Pending</strong>:</p>
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
            Mohon segera <strong>ditindaklanjuti</strong> atau <strong>ubah status audit</strong> agar laporan dapat
            terselesaikan tepat waktu.
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