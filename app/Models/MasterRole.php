<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MasterRole extends Model {
    protected $table = 'master_role';
    protected $guarded = ['id'];

    public function users() {
        return $this->hasMany(MasterUser::class, 'role_id');
    }
}