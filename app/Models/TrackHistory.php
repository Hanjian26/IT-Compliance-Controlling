<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TrackHistory extends Model
{
    use LogsActivity;

    protected $table = 'track_history';

    protected $fillable = [
        'nik',
        'nama',
        'aktivitas',
        'created_at'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('track_history');
    }
}