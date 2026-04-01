<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MasterProdiInstansi extends Model {
    protected $table = 'master_prodi_instansi';
    protected $guarded = ['id'];

    public function kunjungan() {
        return $this->hasMany(Kunjungan::class, 'prodi_id');
    }
}