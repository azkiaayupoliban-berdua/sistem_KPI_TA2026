<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pengunjung extends Model {
    protected $table = 'pengunjung';
    protected $guarded = ['id'];

    public function kunjungan() {
        return $this->hasMany(Kunjungan::class, 'pengunjung_id');
    }
}