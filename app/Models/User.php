<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Primary key settings
     */
    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'nik',
        'nama',
        'email',
        'password',
        'department',
        'level',
        'supervisor_id',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast attributes
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Authentication identifier (login pakai NIK)
     */
    public function getAuthIdentifierName()
    {
        return 'nik';
    }

    /**
     * 🔼 Atasan (Supervisor / Manager)
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id', 'nik');
    }

    /**
     * 🔽 Bawahan (Staff)
     */
    public function staffs()
    {
        return $this->hasMany(User::class, 'supervisor_id', 'nik');
    }

    /**
     * 🔐 Helper role checker (opsional tapi rapi)
     */
    public function isAdmin()
    {
        return $this->level == 1;
    }

    public function isManager()
    {
        return $this->level == 2;
    }

    public function isStaff()
    {
        return $this->level == 3;
    }
}