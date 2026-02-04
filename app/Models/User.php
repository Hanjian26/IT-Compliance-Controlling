<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /* ================= PRIMARY KEY ================= */

    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'int';

    /* ================= MASS ASSIGN ================= */

    protected $fillable = [
        'nik',
        'nama',
        'email',
        'password',
        'department',
        'manager_id',
        'level',
        'is_manager',
    ];

    /* ================= HIDDEN ================= */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* ================= CAST ================= */

    protected $casts = [
        'password' => 'hashed',
        'is_manager' => 'boolean',
    ];

    /* ================= AUTH ================= */

    public function getAuthIdentifierName()
    {
        return 'nik';
    }

    /* ================= RELATION ================
    = */

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', 'nik');
    }

    public function staffs()
    {
        return $this->hasMany(User::class, 'manager_id', 'nik');
    }

    /* ================= ROLE CHECKER ================= */

    public function isAdmin()
    {
        return $this->level === 1;
    }

    public function isUser()
    {
        return $this->level === 2; // view only
    }

    public function isManager()
    {
        return $this->is_manager === true;
    }
}