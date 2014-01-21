<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;
use Myndie\Lib\Input;
use Myndie\Lib\Utils;  


class Encrypt extends Controller
{
    public function __construct($app)
    {
        $this->app = $app;
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Myndie\Model\Encrypt($this->app);
    }    
    
    public function encryption()
    {
    	
        $this->handleJSONContentType();
        
        $param = Input::post("param");
        $usekey = Input::post("usekey");
        $this->result["param"] = $param;
        $this->result["usekey"] = $usekey;      
        if(!$this->model->encrypt($param,$usekey)) {
          $this->result["message"] = "Sorry, your encrypt failed.  Please try again.";
          $this->send();            
       }
        $this->result["status"]="OK";
        $this->result["message"] = "";
        $this->result["param_encoded"] = $this->model->encrypt($param,$usekey);
        
        
        $this->send();
    }
    
    public function decryption()
    {
    	
        $this->handleJSONContentType();
        
        $param = Input::post("param");
        $usekey = Input::post("usekey");
        $this->result["param"] = $param;
        $this->result["usekey"] = $usekey; 
             
        if(!$this->model->decrypt($param,$usekey)) {
          $this->result["message"] = "Sorry, your decrypt failed.  Please try again.";
          $this->send();            
       }
        $this->result["status"]="OK";
        $this->result["message"] = "";
        $this->result["param_decoded"] = $this->model->decrypt($param,$usekey);
        
        
        $this->send();
    }
}
