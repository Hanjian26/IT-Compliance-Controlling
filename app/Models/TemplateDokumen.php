<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TemplateDokumen extends Model
{
    use LogsActivity;

    protected $table = 'template_dokumen';

    protected $fillable = [
        'nama_file','tanggal_terbit', 'file_dokumen', 'perihal',
    ];

    public $timestamps = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('template_dokumen')
            ->logFillable()
            ->setDescriptionForEvent(function (string $eventName) {
                return "Template Dokumen di-{$eventName}";
            });
    }
}
