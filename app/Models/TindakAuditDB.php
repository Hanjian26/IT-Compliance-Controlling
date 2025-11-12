<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TindakAuditDB extends Model
{
    use LogsActivity;

    protected $table = 'tlha_audit';

    protected $fillable = [
        'divisi', 'kegiatan', 'tanggal_mulai', 'tanggal_selesai', 'pic', 'auditor', 'reviewer', 'status', 'keterangan', 'file_laporans',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('tlha_audit')
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "TLHA di-{$eventName}");
    }
}