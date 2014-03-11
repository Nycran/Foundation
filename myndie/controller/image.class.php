<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;
use Myndie\Lib\Input;
use Myndie\Lib\Utils; 
use Myndie\Lib\ImageModify;

class Image extends Controller
{
    public function __construct($app)
    {
        $this->app = $app;
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Myndie\Model\Image($this->app);
    }    
    
    /**
    * Handles when an image is uploaded.
    * Will upload the image, create an image database  table entry for it
    * and then call a custom method if defined.
    * 
    * @param string $imageFor What the image is for, e.g. "sponsor_logo"
    * @param int $id The id of the thing the image is being uploaded for (e.g. the sponsor id)
    * @param string $fileName The file name of the file being uploaded
    * @param boolean $exclusive If set to true, it is enforced that only 1 image for the combination of id and imageFor can be uploaded.
    * e.g. If a sponsor should only have 1 logo, set to true.
    */
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
        
        $this->result["image_path"] = $imageBean->path; 
        $this->result["image_height"] = $imageBean->height;
        $this->result["image_width"] = $imageBean->image_width;
        
        $this->ok("The image was uploaded successfully");
    }  
    
    private function handleSponsorLogo($imageBean, &$error = "")
    {
        // If the image path doesn't exist, do nothing
        $imagePath = MYNDIE_ABSOLUTE_PATH . $imageBean->path;
        if(!file_exists($imagePath)) {
            return false;
        }
        
        // Create a thumbnail image
        ImageModify::resizeToMaxWidthAndSave($imagePath, $imagePath . "_thumb.jpg", 150);
        
        // Load the sponsor object
        $sponsorModel = new \Myndie\Model\Sponsor($this->app);
        $sponsor = $sponsorModel->get($imageBean->foreign_id);
        
        if(!$sponsor) {
            $this->error("Unable to load sponsor");     
        }
        
        // Associate the image with the sponsor
        $sponsor->sharedImage[] = $imageBean;
        R::store($sponsor);         
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
