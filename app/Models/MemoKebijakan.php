<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MemoKebijakan extends Model
{
    use LogsActivity;

    protected $table = 'memo_kebijakan';

    protected $fillable = [
        'tipe', 'nomor', 'tanggal_terbit', 'file_dokumen', 'perihal',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('memo_kebijakan')
            ->logFillable()
            ->setDescriptionForEvent(function (string $eventName) {
                return "Memo kebijakan di-{$eventName}";
            });
    }
}
