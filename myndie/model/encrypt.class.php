<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 
use Myndie\Lib\Encryption as E;
//use Myndie\Lib\Session;

class Encrypt extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
       // $this->table = "user";
        
        // Call parent constructor
        parent::__construct($app);
        
     }
    
        
    public function encrypt($string, $usekey)
    {
        if(empty($string)) {
            return false;
        }
        
        
        if(!$string) {
            return false;    
        } 
               
        if(!\Myndie\Lib\Session::createSession()) {
            $this->app->error(new \Exception("Myndie/Model/User::login - Failed to create session"));
        }
        
		$param_encoded = new E;
       
        return $param_encoded->encode($string,$usekey);
    }    
    
    public function decrypt($string, $usekey)
    {
        if(empty($string)) {
            return false;
        }
        
        
        if(!$string) {
            return false;    
        } 
               
        if(!\Myndie\Lib\Session::createSession()) {
            $this->app->error(new \Exception("Myndie/Model/User::login - Failed to create session"));
        }
        
		$param_decoded = new E;
       
        return $param_decoded->decode($string,$usekey);
    }
}
