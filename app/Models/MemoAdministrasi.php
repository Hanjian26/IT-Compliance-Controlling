<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MemoAdministrasi extends Model
{
    use LogsActivity;

    protected $table = 'memo_administrasi';

    protected $fillable = [
        'tipe', 'nomor', 'tanggal_terbit', 'file_dokumen', 'perihal',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('memo_administrasi')
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Memo administrasi di-{$eventName}");
    }
}
