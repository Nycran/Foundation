<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;
use Myndie\Lib\Input;
use Myndie\Lib\Utils;  
use Myndie\Lib\Session; 

class Article extends Controller
{
    public function __construct($app)
    {
        $this->app = $app;
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Myndie\Model\Article($this->app);
    }    
    
    /**
    * Handle a article details save request
    * 
    * @param integer $id  The id of the article being updated.
    */
    public function save($id)
    {
        // Invoke the base class save method to do any preparation work
        parent::save($id);
        
        $addNewMode = ($id == 0);     // Will be true if we're adding a new article (i.e. id == 0)
        
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

        // Save the article record (if id = 0 then a new record will be created)
        $id = $this->model->save($id, $data);   

        $this->result["status"] = true;
        $this->result["message"] = $id;
        $this->send();     
    }
 
    /***
    * Returns the form validation rules for adding a new article.
    * @param boolean $addNewMode Set to true if we're adding a new article
    */
    private function getValidationAttribs($addNewMode)
    {
        $attribs = [
            'title' => true,    
            'author' => true,   
            'published_date' => false,   
            'position_no' => false,   
            'is_not_allocated' => true,   
            'content' => true,
            'source_url' => true,
            'notes' => true
        ];
        
        return $attribs;
    }   
	
	
	/**
    * Gets a list of the beans from the controller's model and then outputs as json.
    * The $_POST array will be used by the model to achieve any filtering necessary.
    */
    public function getListAsignment()
    {
        $filters = $_POST;
        $orderBy = "position_no";
        
        $page = Input::post("page");
        if(!is_numeric($page)) {
            $page = 0;
        }
                    
        $beans = $this->model->getList($filters, $orderBy, $page, $this->numBeans);
        $this->outputBeansAsJson($beans);       
    }  
}
