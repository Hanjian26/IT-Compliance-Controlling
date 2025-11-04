<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BeritaAcara extends Model
{
    use LogsActivity;

    protected $table = 'berita_acara';

    protected $fillable = [
        'id', 'tipe', 'nomor_berita', 'dari', 'kepada', 'tanggal_terbit', 'file_dokumen', 'perihal', 'created_by', 'updated_by',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('berita_acara')
            ->logFillable()
            ->setDescriptionForEvent(fn(string $eventName) =>
                "Berita Acara -{$eventName}");
    }
}
