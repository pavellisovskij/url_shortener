<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    protected $table = 'urls';

    /**
     *
     */
    public static function getIP() {
        $ip = array(
            '86.57.167.254',
            '37.212.40.80',
            '86.57.245.44',
            '37.212.34.203',
            '151.249.181.221'
        );
        $i = rand(0, 4);
        return $ip[$i];
    }
}
