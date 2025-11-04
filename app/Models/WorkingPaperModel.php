<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WorkingPaperModel extends Model
{
    use LogsActivity;

    protected $table = 'working_paper';

    protected $fillable = [
        'divisi', 'kegiatan', 'tanggal_mulai', 'tanggal_selesai', 'auditor', 'reviewer', 'status', 'keterangan', 'file_wp',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('working_paper')
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Working Paper di-{$eventName}");
    }
}
