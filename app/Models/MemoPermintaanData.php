<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MemoPermintaanData extends Model
{
    use LogsActivity;

    protected $table = 'memo_permintaan_data';

    protected $fillable = [
        'tipe', 'nomor', 'tanggal_terbit', 'file_dokumen', 'perihal',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('memo_permintaan_data')
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Memo permintaan data di-{$eventName}");
    }
}
