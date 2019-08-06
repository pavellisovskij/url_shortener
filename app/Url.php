<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Url extends Model
{
    const ALPHABET  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const BASE      = 62;

    protected $table = 'urls';

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

    public static function getShortLinkById($id) {
        $short_url = "";
        while ($id != 0) {
            $remainder = $id % self::BASE;
            $id = intval($id / self::BASE);
            $short_url = $short_url . self::ALPHABET[$remainder];
        }
        $short_url = strrev($short_url);
        return $short_url;
    }

    public static function getIdByShortLink($short_url) {
        $short_url_length = strlen($short_url);
        $pos = $id = 0;
        while ($short_url_length > 0) {
            $id = $id + strpos(self::ALPHABET, $short_url[$pos]) * pow(self::BASE, $short_url_length - 1);
            $pos++;
            $short_url_length--;
        }
        return $id;
    }

    public static function cutLink($link) {
        if (Str::is('https://www.*', $link) == true) {
            $url = substr($link, 12);
        } elseif (Str::is('http://www.*', $link) == true) {
            $url = substr($link, 11);
        } elseif (Str::is('www.*', $link) == true) {
            $url = substr($link, 4);
        } elseif (Str::is('http://*', $link) == true) {
            $url = substr($link, 7);
        } elseif (Str::is('https://*', $link) == true) {
            $url = substr($link, 8);
        } else {
            $url = $link;
        }
        return $url;
    }
}
