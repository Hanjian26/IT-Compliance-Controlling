<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class MemoAudit extends Model
{
    use LogsActivity;

    protected $table = 'memo_audit';

    protected $fillable = [
        'tipe', 'nomor', 'tanggal_terbit', 'file_dokumen', 'perihal',
    ];

    public $timestamps = false;

    /**
     * ✅ Wajib untuk Spatie v4 ke atas
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('memo_audit')
            ->logOnly(['tipe', 'nomor', 'tanggal_terbit', 'file_dokumen', 'perihal'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Opsional: kustom deskripsi log
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Memo Audit telah di-{$eventName}";
    }
}