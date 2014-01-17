<?php
namespace Myndie\Lib;

use \Myndie\Lib\Input;
use \Myndie\Lib\Session;

/***
* 
*/
class Firewall
{
    // Add any controllers where the ENTIRE controller and all its methods are open to the public here.
    private static $publicController = array("country", "state");  
    
    // Add any method uris where the specific controller method is open to the public here
    // Note, if the method takes additional parameters, e.g. /api/states/list/1, leave out the parameter
    // so just add /api/ then the controller name, then the method name, e.g. /api/states/list
    private static $publicURI = array(
        "/api/user/login"   // Allow user logins publically
    );
     
    public static function run($app)
    {
        // Get the URI currently being invoked
        $req = $app->request;        
        $uri = $resourceUri = $req->getResourceUri(); 
        
        // Permissions are enforced for API routes only.
        // If this is NOT an api route, allow the request throgh
        if(!strstr($uri, "/api/")) {
            return;
        }
        
        $segments = explode("/", $uri);
        $num_segments = count($segments);
        
        if(count($segments) < 3) {
            $app->error(new \Exception("Myndie/Lib/Firewall::run - Invalid Route"));
        }
        
        $controller = $segments[2];       
        $method = ($num_segments > 3) ? $segments[3] : "";
        $uri = "/api/" . $controller . "/" . $method;
        
        // If the current controller is allowed to the public, let the request go through
        if(in_array($controller, self::$publicController)) {
            return;
        }   

        // If the current uri is allowed to the public, let the request go through
        if(in_array($uri, self::$publicURI)) {
            return;
        } 
        
        // If we get to this point then the current URI requires a login.
        // The ID for the session may be passed via a session cookie, OR via HTTP POST
        // First check the cookie
        $sessionID = Input::cookie(MYNDIE_SESSION_COOKIE_NAME);
        if(empty($sessionID)) {
            // The session was NOT in the cookie.
            $sessionID = Input::post("session_id");
        }
        
        if(empty($sessionID)) {
            $app->error(new \Exception("Myndie/Lib/Firewall::run - Access Denied"));
        }
        
        // Check if the session is valid - an exception will be thrown if it is not valid
        Session::sessionValid($sessionID);

        $roles = Session::get("user_roles");
                                           
       // die("OK $roles");      
    }
}