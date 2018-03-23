<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $table = 'messages';
    protected $dates = ['deleted_at'];

    public $primaryKey = 'id';
    public $timestamps = true;

    public function channel() {
        return $this->belongsTo('App\Channel');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
