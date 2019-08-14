<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    protected $table = 'statistics';

    public $timestamps = false;

    public function url() {
        $this->belongsTo('App\Url');
    }
}
