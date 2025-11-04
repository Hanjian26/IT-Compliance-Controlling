<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MemoPenemuan extends Model
{
    use LogsActivity;

    protected $table = 'memo_penemuan';

    protected $fillable = [
        'tipe', 'nomor', 'tanggal_terbit', 'file_dokumen', 'perihal',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('memo_penemuan')
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Memo penemuan di-{$eventName}");
    }
}
