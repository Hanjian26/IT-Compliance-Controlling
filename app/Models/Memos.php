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
        'tipe_memo',
        'scope_memo',
        'nomor',
        'tanggal_terbit',
        'perihal',
        'file_dokumen',
        'user_id',
        'manager_id',
        'status',           // pending | approved | rejected
        'action_type',      // create | update | delete
        'requested_by',
        'approved_by',
        'approved_at',
        'pending_changes',
        'rejection_reason',
    ];

    protected $casts = [
        'pending_changes' => 'array',
        'approved_at'     => 'datetime',
        'tanggal_terbit'  => 'date',
    ];

    /* ================= RELATIONS ================= */

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id', 'nik');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', 'nik');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'nik');
    }

    /* ================= HELPERS ================= */

    public function needsApproval(): bool
    {
        return !is_null($this->manager_id);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

public function getNamaPengajuAttribute()
{
    return $this->requester->nama ?? '-';
}



public function requester()
{
    // 'requested_by' di memos mengacu ke 'nik' di users
    return $this->belongsTo(User::class, 'requested_by', 'nik');
}


    /* ================= ACTIVITY LOG ================= */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Memo')
            ->logOnly([
                'status',
                'action_type',
                'approved_by',
                'approved_at',
                'pending_changes',
                'requested_by',
            ])
            ->setDescriptionForEvent(
                fn(string $event) => "Memo {$event}"
            );
    }
}