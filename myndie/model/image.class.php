<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 
use Myndie\Lib\Imagemodify as I;
//use Myndie\Lib\Session;

class Image extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
       // $this->table = "user";
        
        // Call parent constructor
        parent::__construct($app);
        
     }
    
        
    public function resizeTo($image,$width,$height, $resizeType)
    {
       
               
        if(!\Myndie\Lib\Session::createSession()) {
            $this->app->error(new \Exception("Myndie/Model/User::login - Failed to create session"));
        }
        
		$new_image = new I($image);
       
        $new_image->resizeTo($width,$height, $resizeType);
        return $new_image->saveImage('testnew.jpg', "100", true);
        
    }    
    
     public function cropimage($image,$width,$height)
    {
       
               
        if(!\Myndie\Lib\Session::createSession()) {
            $this->app->error(new \Exception("Myndie/Model/User::login - Failed to create session"));
        }
        
		$new_image = new I($image);
       
        $new_image->cropimage($width,$height);
        return $new_image->saveImage('testnew.jpg', "100", true);
        
    }    
}
