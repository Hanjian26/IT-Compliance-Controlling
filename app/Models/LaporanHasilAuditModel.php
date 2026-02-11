<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class LaporanHasilAuditModel extends Model
{
    use LogsActivity;

    protected $table = 'laporan_hasil_audit';

    protected $fillable = [
        'divisi', 'kegiatan', 'tanggal_mulai', 'tanggal_selesai', 'auditor', 'reviewer', 'status', 'keterangan', 'file_lha',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('laporan_hasil_audit')
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Laporan Hasil Audit di-{$eventName}");
    }
}