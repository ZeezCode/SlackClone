<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Server extends Model
{
    use SoftDeletes;

    protected $table = 'servers';
    protected $dates = ['deleted_at'];

    public $primaryKey = 'id';
    public $timestamps = true;

    public function owner() {
        return $this->belongsTo('App\User', 'owner_id');
    }

    public function channels() {
        return $this->hasMany('App\Channel');
    }

    public function memberships() {
        return $this->hasMany('App\ServerMembership');
    }
}
