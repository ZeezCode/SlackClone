<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $table = 'channels';
    protected $dates = ['deleted_at'];

    public $primaryKey = 'id';
    public $timestamps = true;

    public function server() {
        return $this->belongsTo('App\Server');
    }
}
