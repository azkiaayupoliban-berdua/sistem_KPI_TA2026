<?php

namespace App\Models;

// WAJIB: Gunakan Authenticatable, bukan Model biasa agar bisa Login
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterUser extends Authenticatable {
    use HasFactory, Notifiable;

    protected $table = 'master_user';

    // Karena Anda memindahkan data, pastikan guarded atau fillable sesuai
    protected $guarded = ['id'];

    // Sembunyikan password agar tidak ikut terbawa saat query data user
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role() {
        return $this->belongsTo(MasterRole::class, 'role_id');
    }

    public function kunjungan() {
        return $this->hasMany(Kunjungan::class, 'user_id');
    }
}
