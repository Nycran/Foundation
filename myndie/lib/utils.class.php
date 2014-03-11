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
    
    /**
    * Converts a date in UK format to ISO format
    * 
    * @param string $date The date in UK date format
    * @param string $default The default value that will be returned if the date conversion fails.
    */
    public static function convertUKDateToISODate($date, $default = null)
    {
        if(empty($date)) {
            return $default;
        }
        
        $elements = explode("/", $date);
        if(count($elements) != 3) {
            return $default;
        }
        
        $result = $elements[2] . "-" . $elements[1] . "-". $elements[0];
        
        return $result;
    }
}