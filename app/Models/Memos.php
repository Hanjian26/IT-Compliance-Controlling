<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Memos extends Model
{
    use LogsActivity;

    protected $table = 'memos';

    protected $fillable = [
        'tipe_memo', 'scope_memo', 'nomor', 'tanggal_terbit', 'file_dokumen', 'perihal',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Memo')
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Memo-{$eventName}");
    }
}
