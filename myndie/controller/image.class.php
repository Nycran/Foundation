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
    
    public function uploadImage($imageFor, $id, $fileName, $exclusive = false)
    {
        $error = "";
        
        // Get the destination folder to upload the image to.
        $destFolder = $this->model->prepareUploadFolder($imageFor, $id, $error);    
        
        if(!$destFolder) {
            $this->error($error);
        }
        
        // Before doing the upload, if the image is exclusive, see if there's an existing record
        // in the database for this combination of imageFor and id (foreign_id).
        $imageID = "";
        
        if($exclusive) {
            $filters = array();
            $filters["image_for"] = $imageFor;
            $filters["foreign_id"] = $id;
            
            $imageBean = $this->model->getSingleBean($filters);
            if($imageBean) {
                if(!$this->model->deleteImage($imageBean)) {
                    $this->error("Unable to delete previously saved image");    
                }
            }
        }
        
        // Now handle the imageUpload
        $filePath = $this->model->handleImageUpload($destFolder, $fileName, $error);               
        
        if(!$filePath) {
            $this->error($error);
        } 
        
        // Save the file meta data to the database
        $imageID = $this->model->saveToDB($imageFor, $id, $filePath);
        if(!$imageID) {
            $this->error("Unable to save image metadata");
        } 
        
        // Load the image bean
        $imageBean = $this->model->get($imageID);
        if(!$imageBean) {
            $this->error("Unable to load image bean");    
        }
        
        // Now that the file has been uploaded, see if there's a post upload processing function defined
        // We translate an imageFor variable that might be like "sponsor_logo" to handleSponsorLogo (capitalised, underscores removed)
        $postUploadMethod = "handle" . str_replace(" ", "", ucwords(str_replace("_", " ", $imageFor)));
        
        if(method_exists($this, $postUploadMethod)) {
            $this->$postUploadMethod($imageBean, $error);
        }
        
        $this->ok("The image was uploaded successfully");
    }
    
    private function handleSponsorLogo($imageBean, &$error = "")
    {
        // Do any image resizing necessary for the sponsor logo
        die("HANDLE RESIZE");   
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
