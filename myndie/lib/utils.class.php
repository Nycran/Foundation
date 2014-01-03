<?php
namespace Myndie\Lib;

class Utils
{
    public static function removeArrayKey(&$array, $key)
    {
        if(array_key_exists($key, $array)) {
            unset($array[$key]);
        }
    }
}