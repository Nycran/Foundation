<?php
class Input
{
    /***
    * Reads a value from the $_POST array if present
    * Returns a blank/empty value if the value is not present
    * 
    * @param string $name The name of the post variable to check for.
    */
    public static function post($name)
    {
        if(!array_key_exists($name, $_POST)) {
            return "";            
        }
        
        return $_POST[$name];
    }
    
    /***
    * Reads a value from the $_GET array if present
    * Returns a blank/empty value if the value is not present
    * 
    * @param string $name The name of the GET variable to check for.
    */
    public static function get($name)
    {
        if(!array_key_exists($name, $_GET)) {
            return "";            
        }
        
        return $_GET[$name];
    }  
    
    /***
    * Reads a value from the $_COOKIE array if present
    * Returns a blank/empty value if the value is not present
    * 
    * @param string $name The name of the COOKIE variable to check for.
    */
    public static function cookie($name)
    {
        if(!array_key_exists($name, $_COOKIE)) {
            return "";            
        }
        
        return $_COOKIE[$name];
    }      
}