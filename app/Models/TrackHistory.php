<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TrackHistory extends Model
{
    use LogsActivity;

    protected $table = 'track_history';
    public $timestamps = false; // pakai created_at manual

    protected $fillable = [
        'tanggal_pengajuan',
        'nik',
        'nama',
        'perihal',
        'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('track_history');
    }
}