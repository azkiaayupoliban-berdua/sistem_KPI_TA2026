<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetailSurvey extends Model {
    protected $table = 'detail_survey';
    protected $guarded = ['id'];

    public function survey() {
        return $this->belongsTo(Survey::class, 'survey_id');
    }
}