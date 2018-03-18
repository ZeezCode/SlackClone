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
}
