<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;
use Myndie\Lib\Input;
use Myndie\Lib\Utils;  
use Myndie\Lib\Session; 

class Location extends Controller
{
    public function __construct($app)
    {
        $this->app = $app;
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Myndie\Model\Location($this->app);
    }    
    
    /**
    * Handle a location details save request
    * 
    * @param integer $id  The id of the location being updated.
    */
    public function save($id)
    {
        // Invoke the base class save method to do any preparation work
        parent::save($id);
        
        $addNewMode = ($id == 0);     // Will be true if we're adding a new location (i.e. id == 0)
        
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

        // Save the location record (if id = 0 then a new record will be created)
        $id = $this->model->save($id, $data);   

        $this->result["status"] = true;
        $this->result["message"] = $id;
        $this->send();     
    }
 
    /***
    * Returns the form validation rules for adding a new location.
    * @param boolean $addNewMode Set to true if we're adding a new location
    */
    private function getValidationAttribs($addNewMode)
    {
        $attribs = [
            'name' => true,         
            'enabled' => false,
        ];
        
        return $attribs;
    }   
	
	/***
    * This function for statistics in dashboard cms
    */
	public function getStatisticsSchedules()
    {
		$filters = $_POST;
        $orderBy = "name";
        
        $page = Input::post("page");
        if(!is_numeric($page)) {
            $page = 0;
        }
                    
        $locations = $this->model->getList(array(), $orderBy, $page, $this->numBeans);
		$schedules = array();
		
		$where = "";
		$values = array();
		if(array_key_exists("date_from_ge", $filters)) {
            $where .= " date_from >= ? "; 
			$filters["date_from_ge"] = Utils::convertUKDateToISODate($filters["date_from_ge"]);
            $values[] = $filters["date_from_ge"];  
        }

		foreach($locations as $loc)
		{
			$schedule = $loc
				->with( $where . ' ORDER BY date_from ASC, date_to ASC ', $values )
				->sharedSchedule;
			$schedules[] = R::exportAll($schedule);
		}
       
        $this->ok($schedules);   
    }  
}
