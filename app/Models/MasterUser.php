<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MasterUser extends Model {
    protected $table = 'master_user';
    protected $guarded = ['id'];

    public function role() {
        return $this->belongsTo(MasterRole::class, 'role_id');
    }
    public function kunjungan() {
        return $this->hasMany(Kunjungan::class, 'user_id');
    }
}