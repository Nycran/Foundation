<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;
use Myndie\Lib\Input;
use Myndie\Lib\Utils;  
use Myndie\Lib\Session; 

class Schedule extends Controller
{
    public function __construct($app)
    {
        $this->app = $app;
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Myndie\Model\Schedule($this->app);
    }    
    
    /**
    * Handle a schedule item save request
    * 
    * @param integer $id  The id of the item being updated.
    */
    public function save($id)
    {
        // Invoke the base class save method to do any preparation work
        parent::save($id);
        
        $addNewMode = ($id == 0);     // Will be true if we're adding a new item (i.e. id == 0)
        
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
        
        // Convert dates to ISO       
        $data["date_from"] = Utils::convertUKDateToISODate($data["date_from"]);
        $data["date_to"] = Utils::convertUKDateToISODate($data["date_to"]); 
        
        // Get the sponsor and location ids
        $sponsor_id = $data["sponsor_id"];            
        $location_id = $data["location_id"];
        
        // Remove them from the data array before saving, as we don't want them in the schedule table
        unset($data["sponsor_id"]);
        unset($data["location_id"]);

        // Save the category record (if id = 0 then a new record will be created)
        $id = $this->model->save($id, $data);  
        
        // Load the schedule item as a bean
        $scheduleBean = $this->model->get($id);
        if(!$scheduleBean) {
            $this->error("Unable to load schedule item");
        }    
        
        // Load the location bean
        $locationModel = new \Myndie\Model\Location($this->app);
        $locationBean = $locationModel->get($location_id);
        if(!$locationBean) {
            $this->error("Unable to load location item");
        }        
        
        // Load the sponsor bean
        $sponsorModel = new \Myndie\Model\Sponsor($this->app);
        $sponsorBean = $sponsorModel->get($sponsor_id);
        if(!$sponsorBean) {
            $this->error("Unable to load sponsor item");
        }         
        
        // Add the schedule item to the location and sponsor
        $locationBean->sharedSchedule[] = $scheduleBean;
        $sponsorBean->sharedSchedule[] = $scheduleBean;
        
        R::store($locationBean);
        R::store($sponsorBean);

        $this->result["status"] = true;
        $this->result["message"] = $id;
        $this->send();     
    }
 
    /***
    * Returns the form validation rules for adding a new category.
    * @param boolean $addNewMode Set to true if we're adding a new category
    */
    private function getValidationAttribs($addNewMode)
    {
        $attribs = [
            'sponsor_id' => true,
            'date_from' => true,         
            'date_to' => true,
            'location_id' => true,
            'text' => false,
            'notes' => false
        ];
        
        return $attribs;
    }   
}
