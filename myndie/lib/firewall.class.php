<?php
/***
* firewall.class.php
* @version 1.0
* @license MIT
* @author Andrew Chapman
*/

namespace Myndie\Lib;

use \Myndie\Lib\Input;
use \Myndie\Lib\Session;
use \Myndie\Lib\Strings;

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
        "/api/user/login",      // Allow user logins publically
        "/api/user/register",    // Allow user registrations publically
        "/api/emailtemplate/sendtest"
    );
    
    // If your entire controllers should be restircted to specific user roles, add them here
    // e.g. "state" => array(MYNDIE_ROLE_ADMIN, MYNDIE_ROLE_MEMBER)    
    private static $restrictedControllers = array(
        "emailtemplate" => array(MYNDIE_ROLE_ADMIN)    // Only admin should have access to the email template controller
    );    

    // Add any URIS here that should be restricted to particular login roles.
    // e.g.  "/api/state/list" => array(MYNDIE_ROLE_ADMIN, MYNDIE_ROLE_MEMBER) 
    // You can specify as many roles as you want with comma separated values.
    // Note, if a URI is NOT in this array, as long as the user is logged in, they will be able to access it.
    private static $restrictedURIs = array(
        "/api/state/list" => array(MYNDIE_ROLE_ADMIN, MYNDIE_ROLE_MEMBER)    
    );
     
    /***
    * Runs the firewall rules.
    * Is invoked in index.php
    * 
    * @param object $app An instance of the Slim app object
    */
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
        
        // Get the roles that the user is associated with
        $roles = Session::get("user_roles");        
        
        // Is the current controller restricted?
        if(array_key_exists($controller, self::$restrictedControllers)) {
            // This entire controller is restircted to specific user roles
            // Is this users role allowed to view this controller?
            if(!Strings::matchInCSV($roles, self::$restrictedControllers[$controller])) {
                $app->error(new \Exception("Myndie/Lib/Firewall::run - Access Denied"));    
            }
        }        
        
        // Is the current URI restricted?
        if(!array_key_exists($uri, self::$restrictedURIs)) {
            // The URI is not restricted, let the request pass
            return;
        }
        
        // Is this users role allowed to view this URI?
        if(Strings::matchInCSV($roles, self::$restrictedURIs[$uri])) {
            return true;
        }
                                           
        $app->error(new \Exception("Myndie/Lib/Firewall::run - Access Denied"));
    }
}