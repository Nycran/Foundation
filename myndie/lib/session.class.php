<?php
namespace Myndie\Lib;   

use RedBean_Facade as R;  
use Myndie\Lib\Strings;
use Myndie\Lib\Input;

class Session
{
    private static $sessionID = "";         // The sessionID (a random string of 15 characters)
    private static $objSession = false;     // Will be set to a Session Model object
    private static $sessionBean = false;    // Set to the actual session bean object once the session has been validated.
    
    /**
    * Updates the users session
    * 
    * @param mixed $key
    * @param mixed $value
    */
    public static function set($key, $value)
    {
        self::sessionValid();   // Ensure the user's session is valid.
        
        // Session data is stored in the bean as a serialized array. 
        // To manipulate it therefore we need to unserialize.
        $dataArray = unserialize(self::$sessionBean->data);
        if(!is_array($dataArray)) {
            $dataArray = array();
        }
                     
        // Set the session value
        $dataArray[$key] = $value;
        
        // Update the session bean
        self::$sessionBean->timeout = time() + MYNDIE_SESSION_TIMEOUT;
        self::$sessionBean->data = serialize($dataArray);
        R::store(self::$sessionBean);
    }
    
    /**
    * Gets a value from the users session
    * 
    * @param string $key The key of the value to retrieve.
    */
    public static function get($key)
    {
        self::sessionValid();   // Ensure the user's session is valid.
        
        $dataArray = unserialize(self::$sessionBean->data);
        if(!is_array($dataArray)) {
            $dataArray = array();
        }
        
        if(!array_key_exists($key, $dataArray)) {
            return "";   
        }
        
        return $dataArray[$key];
    }
    
    /**
    * Creates a new session in the database and stores the ID 
    * for that session for later use.  The session cookie is also 
    * written to the client
    */
    public static function createSession()
    {
        self::checkCreateSessionModel();
        
        // Create a random ID for the session, ensuring no other session 
        // exists with this same ID.
        $found = false;
        while(!$found) {
            self::$sessionID = Strings::createRandomString(15);
            
            $bean = self::$objSession->getSingleBean(array("session_id" => self::$sessionID));
            if(!$bean) {
                $found = true;
            }
        }
        
        // Create a new session in the database.
        $sessionData = array();
        $sessionData["session_id"] = self::$sessionID;
        $sessionData["timeout"] = time() + MYNDIE_SESSION_TIMEOUT;
        $sessionData["ip_address"] = INPUT::ipAddress();    
        $sessionData["user_agent"] = INPUT::userAgent();
        $sessionData["data"] = serialize(array());
        $id = self::$objSession->save("", $sessionData);
        
        // Get the bean back from the database.
        self::$sessionBean = self::$objSession->get($id);
        if(!self::$sessionBean) {
            return false;
        }

        // Set the session cookie
        $expiry = (MYNDIE_SESSION_COOKIE_EXPIRY > 0) ? time() + MYNDIE_SESSION_COOKIE_EXPIRY : 0;
        setcookie(MYNDIE_SESSION_COOKIE_NAME, self::$sessionID, $expiry, MYNDIE_SESSION_COOKIE_PATH);
        
        return true;
    }
    
    /**
    * Tests if the users session is valid.  The session ID to check may be passed
    * explicitly, but if not, it is retrieved from the browser cookies.
    * 
    * @param string $sessionID The session ID to check.
    * @return True if the session is valid, false if not.
    */
    public static function sessionValid($sessionID = "", $throwException = true)
    {
        global $app;
        
        if(self::$sessionBean) {
            return true;
        }
        
        // If no session ID was provided, check for the myndie session cookie
        if(empty($sessionID)) {
            $sessionID = Input::cookie(MYNDIE_SESSION_COOKIE_NAME);
        }
        
        // If we still have no session ID, the session is NOT valid
        if(empty($sessionID)) {
            if($throwException) {
                $app->error(new \Exception("Myndie/Lib/Session::sessionValid - Invalid Session - Error Code 1"));   
            } else {
                return false;
            }
        }
        
        // Attempt to load the session from the database
        self::checkCreateSessionModel();
        $bean = self::$objSession->getSingleBean(array("session_id" => $sessionID));
        if(!$bean) {
            // There is no session in the database with this ID.
            if($throwException) {
                $app->error(new \Exception("Myndie/Lib/Session::sessionValid - Invalid Session - Error Code 2")); 
            } else {
                return false;
            }
        }
        
        // We have a valid session record. 
         
        // If the session config has IP address checking turned on, ensure the 
        // IP address that created the session is the same IP address as the one checking now.
        if(MYNDIE_SESSION_CHECK_IP) {
            if($bean->ip_address != Input::ipAddress()) {
                if($throwException) {
                    $app->error(new \Exception("Myndie/Lib/Session::sessionValid - Invalid Session - Error Code 4"));   
                } else {
                    return false;
                }
            }
        }
        
        // If the session config has browser UserAgent checking turned on, ensure the 
        // UserAgent that created the session is the same UserAgent as the one checking now.        
        if(MYNDIE_SESSION_CHECK_AGENT) {
            if($bean->user_agent != Input::userAgent()) {
                if($throwException) {
                    $app->error(new \Exception("Myndie/Lib/Session::sessionValid - Invalid Session - Error Code 4"));
                } else {
                    return false;
                }
            }
        }
        
        // Test Last Activity
        if($bean->timeout < time()) {
            if($throwException) {
                $app->error(new \Exception("Myndie/Lib/Session::sessionValid - Invalid Session - Error Code 5"));
            } else {
                return false;
            }
        }
        
        // The session is valid. 
        self::$sessionID = $sessionID;
        self::$sessionBean = $bean; 
        
        return true;    
    }

    
    public static function getSessionID()
    {
        return self::$sessionID;
    }
    
    private static function checkCreateSessionModel()
    {
        if(empty(self::$objSession)) {
            global $app;
            self::$objSession = new \Myndie\Model\Session($app);
        }        
    }
    
    public static function checkRole($role, $sessionID = "")
    {
        if(!self::sessionValid($sessionID, false)) {
            return false;
        }
        
        $roles = Session::get("user_roles");
        if(empty($roles)) {
            return false;
        }

        return Strings::matchInCSV($role, $roles);
    }
}