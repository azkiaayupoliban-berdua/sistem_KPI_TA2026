<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MasterPertanyaan extends Model {
    protected $table = 'master_pertanyaan';
    protected $guarded = ['id'];

    public function aspek() {
        return $this->belongsTo(MasterAspekSurvey::class, 'aspek_id');
    }
}