<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MasterKeperluan extends Model {
    protected $table = 'master_keperluan';
    protected $guarded = ['id'];

    public function kunjungan() {
        return $this->hasMany(Kunjungan::class, 'keperluan_id');
    }
}