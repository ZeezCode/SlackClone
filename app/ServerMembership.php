<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServerMembership extends Model
{
    protected $table = 'server_memberships';

    public $primaryKey = 'id';
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function server() {
        return $this->belongsTo('App\Server');
    }
}
