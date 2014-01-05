<?php
namespace Myndie\Lib;   

use RedBean_Facade as R;  
use Myndie\Lib\Strings;

class Session
{
    private static $sessionID;
    private static $objSession;
    
    public static function set($key, $value)
    {
        global $app;
        
        if(empty(self::$objSession)) {
            self::$objSession = new \Myndie\Model\Session($app);
        }
        
        // If no session ID has been defined yet,
        // Create the session and store the ID.
        if(empty(self::$sessionID)) {
            self::createSession();
        }
        
        if(self::hasExpired()) {    
            throw new Exception("Myndie/Lib/Session - Session has expired");
        }
        
        $_SESSION[$key] = $value;
        $_SESSION['LAST_ACTIVITY'] = time();
        
        return true;
    }
    
    public static function get($key)
    {
        if(empty(self::$sessionID)) {
            return false;
        }  
        
        if(self::hasExpired()) {
            throw new Exception("Myndie/Lib/Session - Session has expired");
        }
        
        if(!array_key_exists($key, $_SESSION)) {
            return "";
        }             
        
        return $_SESSION[$key]; 
    }
    
    /***
    * Creates a new session in the database and stores the ID 
    * for that session for later use.
    * 
    */
    private static function createSession()
    {
        self::$sessionID = Strings::createRandomString(15);
        
        $sessionData = array();
        $sessionData["session_id"] = self::$sessionID;
        $sessionData["created"] = date("Y-m-d H:i:s");
        $sessionData["timeout"] = date("Y-m-d H:i:s", time() + SESSION_TIMEOUT);
        $sessionData["ip_address"] = INPUT::ipAddress();    
        $sessionData["user_agent"] = INPUT::userAgent();
        $sessionData["data"] = serialize(array());
        
        $id = self::$objSession->save("", $sessionData);
        
        setcookie(SESSION_COOKIE_NAME, self::$sessionID, time() + SESSION_COOKIE_EXPIRY);
    }
    
    public static function hasExpired()
    {
        // Check for a session timeout.
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 60)) {
            // last request was more than 30 minutes ago - destroy session and session data
            session_unset();  
            session_destroy();
            
            self::$sessionID = "";
            
            return true;
        }  
        
        return false;      
    }
    
    public static function getSessionID()
    {
        return self::$sessionID;
    }
}