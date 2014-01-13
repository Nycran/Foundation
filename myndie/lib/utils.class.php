<?php
namespace Myndie\Lib;

class Utils
{
    /**
    * Gracefully removes a key from an array if it exists
    * 
    * @param array $array  The array to check the key for
    * @param string $key The key to remove
    */
    public static function removeArrayKey(&$array, $key)
    {
        if(array_key_exists($key, $array)) {
            unset($array[$key]);
        }
    }
}