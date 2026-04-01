<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model {
    protected $table = 'kunjungan';
    protected $guarded = ['id'];

    public function pengunjung() {
        return $this->belongsTo(Pengunjung::class, 'pengunjung_id');
    }
    public function prodi() {
        return $this->belongsTo(MasterProdiInstansi::class, 'prodi_id');
    }
    public function keperluan_master() {
        return $this->belongsTo(MasterKeperluan::class, 'keperluan_id');
    }
    public function admin() {
        return $this->belongsTo(MasterUser::class, 'user_id');
    }
    public function survey() {
        return $this->hasOne(Survey::class, 'kunjungan_id');
    }
}