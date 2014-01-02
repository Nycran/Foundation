<?php
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
}