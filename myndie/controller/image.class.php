<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;
use Myndie\Lib\Input;
use Myndie\Lib\Utils;  


class Image extends Controller
{
    public function __construct($app)
    {
        $this->app = $app;
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Myndie\Model\Image($this->app);
    }    
    
    public function resizeImage()
    {
    	
        $this->handleJSONContentType();
        
        $param = $_SERVER['DOCUMENT_ROOT'].'/Foundation/test.jpg';
        print_r($param);
        $resizeType = Input::post("resizeType");
        $width = Input::post("width");
        $height =  Input::post("height");
        
        $this->result["image"] = $param;
        $this->result["resizeType"] = $resizeType;      
       // if(!$this->model->resizeTo($param,$width,$height, $resizeType)) {
        //  $this->result["message"] = "Sorry, your image resize failed.  Please try again.";
        //  $this->send();            
     // }
        $this->result["status"]="OK";
        $this->result["message"] = "";
        $this->result["new_image"] = $this->model->resizeTo( $param,$width,$height, $resizeType);
        
        
        $this->send();
    }
    
    public function cropImage()
    {
    	
        $this->handleJSONContentType();
        
        $param = $_SERVER['DOCUMENT_ROOT'].'/Foundation/test.jpg';
        $width = Input::post("width");
        $height =  Input::post("height"); 
             
        //if(!$this->model->cropImage($param,$width,$height)) {
        //  $this->result["message"] = "Sorry, your crop failed.  Please try again.";
        //  $this->send();            
      // }
        $this->result["status"]="OK";
        $this->result["message"] = "";
        $this->result["param_decoded"] = $this->model->cropimage($param,$width,$height);
        
        
        $this->send();
    }
}
