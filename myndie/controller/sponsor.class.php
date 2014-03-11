<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;
use Myndie\Lib\Input;
use Myndie\Lib\Utils;  
use Myndie\Lib\Session;    

class Sponsor extends Controller
{
    public function __construct($app)
    {
        $this->app = $app;
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Myndie\Model\Sponsor($this->app);
    }    
    
    /**
    * Handle sponsor save request
    * 
    * @param integer $id  The id of the sponsor being updated.
    */
    public function save($id)
    {
        // Invoke the base class save method to do any preparation work
        parent::save($id);
        
        $addNewMode = ($id == 0);     // Will be true if we're adding a new category (i.e. id == 0)
        
        /************ VALIDATION **************/
        $profile = new \DataFilter\Profile();
        
        // Set global validation checks
        //$profile->addPreFilters(['Trim', 'StripHtml']);
        $profile->setAttribs($this->getValidationAttribs($addNewMode)); 
          
        // Perform validation checks
        if (!$profile->check($_POST)) {
            // The form is NOT valid.
            $message = "Validation Error:\n";
            $res = $profile->getLastResult();
            foreach ($res->getAllErrors() as $error) {
                $message .= "Err: $error\n";
            }            
            
            // Send the validation errors back to the browser
            $this->result["message"] = $message;
            $this->send();
        }
        
        // The form was valid.  Get the validated and transformed data from the profile.
        $data = $profile->getLastResult()->getValidData();  

        // Save the category record (if id = 0 then a new record will be created)
        $id = $this->model->save($id, $data);   

        $this->result["status"] = true;
        $this->result["message"] = $id;
        $this->send();     
    }
    
    public function deleteLogo($id)
    {   
        // Load the sponsor bean
        $sponsorBean = $this->model->get($id);
        if(!$sponsorBean) {
            $this->error("Could not load the sponsor");
        }
        
        $imageBeans = $sponsorBean->sharedImage;
        
        foreach($imageBeans as $imageBean) {
            // Remove the image from the file system
            $path = MYNDIE_ABSOLUTE_PATH . $imageBean->path;
            
            // Delete the main image.
            if(file_exists($path)) {
                @unlink($path);
                
                if(file_exists($path)) { 
                    $this->error("Couldn't delete image:  $path");    
                }                 
            } 
            
            // Delete the thumbnail
            $path = $path . "_thumb.jpg";
            
            if(file_exists($path)) {
                @unlink($path);
                
                if(file_exists($path)) { 
                    $this->error("Couldn't delete image:  $path");    
                }                 
            }             
        }
        
        // Remove the shareImages
        $sponsorBean->sharedImage = array();
        R::store($sponsorBean);
        
        $this->ok("Image removed");
    }
 
    /***
    * Returns the form validation rules for adding a new schedule.
    * @param boolean $addNewMode Set to true if we're adding a new category
    */
    private function getScheduleValidationAttribs($addNewMode)
    {
        $attribs = [
            'date_from' => true,         
            'date_to' => true,
            'location_id' => true,
            'text' => false,
            'notes' => false
        ];
        
        return $attribs;
    }
    
    /**
    * Handle schedule save request
    * 
    * @param integer $sponsor_id  The id of the sponsor the schedule belongs to.
    * @param integer $id  The id of the schedule being updated.
    */
    public function saveSchedule($sponsor_id, $id)
    {
        // Invoke the base class save method to do any preparation work
        parent::save($id);
        
        $addNewMode = ($id == 0);     // Will be true if we're adding a new category (i.e. id == 0)
        
        /************ VALIDATION **************/
        $profile = new \DataFilter\Profile();
        
        // Set global validation checks
        //$profile->addPreFilters(['Trim', 'StripHtml']);
        $profile->setAttribs($this->getScheduleValidationAttribs($addNewMode)); 
          
        // Perform validation checks
        if (!$profile->check($_POST)) {
            // The form is NOT valid.
            $message = "Validation Error:\n";
            $res = $profile->getLastResult();
            foreach ($res->getAllErrors() as $error) {
                $message .= "Err: $error\n";
            }            
            
            // Send the validation errors back to the browser
            $this->result["message"] = $message;
            $this->send();
        }
        
        // The form was valid.  Get the validated and transformed data from the profile.
        $data = $profile->getLastResult()->getValidData(); 
        
        $scheduleModel = new \Myndie\Model\Schedule($this->app);        

        // Save the schedule record (if id = 0 then a new record will be created)
        $id = $scheduleModel->save($id, $data);   

        $this->result["status"] = true;
        $this->result["message"] = $id;
        $this->send();     
    }       
}
