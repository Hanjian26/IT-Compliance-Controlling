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


    public $timestamps = false;

    protected $fillable = [
        'tanggal_pengajuan',
        'nik',
        'nama',
        'perihal',
        'status',
<<<<<<< HEAD
        'created_at',
=======
>>>>>>> cc65200a9547c5b0516d493e2bce8307619226f1
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('track_history');
    }
}