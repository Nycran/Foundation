<?php
namespace Myndie\Lib;

class Strings
{
    /***
    * Generates a random string of $result_length length
    * 
    * @param integer $result_length The length of the random string to generate
    * @returns a random alphanumeric string of the specified length.
    */
    public static function createRandomString($result_length = 10)
    {
        srand();
        $candidates = "ABCDEFJHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_#%^&*()!@";
        $num_valid_candidates = strlen($candidates) - 1;
        
        $result = "";
        for($x = 0; $x < $result_length; $x++) {
            $offset = rand(0, $num_valid_candidates);
            $result .= substr($candidates, $offset, 1);
        }
        
        return $result;
    }
    
    /***
    * Compares a CSV string with another CSV string or an array.  
    * If any of the values in csv1 are found in csv2, this returns true
    * 
    * @param string $csv A CSV string, e.g. 1,2,3
    * @param string $target Either a CSV string, e.g. 3,4,5 OR an array to search
    * 
    * @returns If any of the values in csv1 are present in the target, true is returned.  Otherwise false is returned.
    */
    public static function matchInCSV($csv, $target)
    {
        // If the passed CSV is empty, return false;
        if(empty($csv)) {
            return false;
        }
        
        // Explode the CSVS to arrays
        $a1 = explode(",", $csv);
        
        if(!is_array($target)) {
            $a2 = explode(",", $target);
        } else {
            $a2 = $target;
        }
        
        // Compare the arrrays.  The intersect method returns the values 
        // from array1 that are also present in array2
        $intersection = array_intersect($a1, $a2);
        
        // If there are no intersecting values, return false.
        if(count($intersection) == 0) {
            return false;
        }
        
        // There were matching values, return true.
        return true;
    }
}