<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MemoToAll extends Model
{
    use LogsActivity;

    protected $table = 'memo_to_all';

    protected $fillable = [
        'tipe', 'nomor', 'tanggal_terbit', 'file_dokumen', 'perihal',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('memo_to_all')
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Memo To All IT di-{$eventName}");
    }
}