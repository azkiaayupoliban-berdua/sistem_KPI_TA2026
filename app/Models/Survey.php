<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model {
    protected $table = 'survey';
    protected $guarded = ['id'];

    public function kunjungan() {
        return $this->belongsTo(Kunjungan::class, 'kunjungan_id');
    }
    public function detail() {
        return $this->hasOne(DetailSurvey::class, 'survey_id');
    }
}