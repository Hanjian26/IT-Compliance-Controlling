<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AuditBRDB extends Model
{
    use LogsActivity;

    protected $table = 'audit_brdb';

    protected $fillable = [
        'divisi', 'kegiatan', 'tanggal_mulai', 'tanggal_selesai', 'auditor', 'reviewer', 'status', 'keterangan',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('audit_brdb')
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Audit Backup Restore di-{$eventName}");
    }
}