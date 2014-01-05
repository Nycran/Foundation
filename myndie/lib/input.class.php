<?php
namespace Myndie\Lib;

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
    
    /***
    * Returns the users IP address
    */
    public static function ipAddress()
    {
        $ipaddress = '';
        
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } else if(getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if(getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } else if(getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } else if(getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } else if(getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;        
    }
    
    public static function userAgent()
    {
        $userAgent = "";
        if(getenv('HTTP_USER_AGENT')) {
            $userAgent = getenv('HTTP_USER_AGENT');    
        }
        
        return $userAgent;
    }      
}