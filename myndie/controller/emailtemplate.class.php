<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;
use Myndie\Lib\Input;

class Emailtemplate extends Controller
{
    public function __construct($app)
    {
        $this->app = $app;
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Myndie\Model\Emailtemplate($this->app);
    }
    
    /**
    * Saves an email template to the database.  Note, if creating a new email template, the "code" field
    * must be unique.
    * 
    * @param int $id If a value of 0 is passed, a new template will be created.  Otherwise the specified
    * template will be updated.
    */
    public function save($id)
    {
        // Invoke the base class save method to do any preparation work
        parent::save($id);
        
        $addNewMode = ($id == 0);     // Will be true if we're adding a new user (i.e. id == 0)
        
        /************ VALIDATION **************/
        $profile = new \DataFilter\Profile();
        
        // Set global validation checks
        $profile->addPreFilters(['Trim']);
        $profile->setAttribs($this->getValidationAttribs()); 
          
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
        
        if($addNewMode) {
            // If we're adding a new user, ensure no other template with this code already exists
            $code = INPUT::post("code");
            $templates = $this->model->getList(array("code" => $code));
            if(count($templates) > 0) {
                $this->result["message"] = "Sorry, an emailtemplate with this code already exists";
                $this->send();                
            }
  
        }         

        // Save the client record (if id = 0 then a new record will be created)
        $id = $this->model->save($id, $data);

        $this->result["status"] = true;
        $this->result["message"] = $id;
        $this->send();     
    }    
    
    /**
    * Returns the form validation rules for adding a new emailtemplate.
    */
    private function getValidationAttribs()
    {
        $attribs = [
            'code' => true,
            'name' => true,
            'subject' => true,
            'from' => true,
            'is_html' => [
                'required' => true, 
                'matchAny' => false, 
                'default' => null, 
                'missing' => 'The is_html field is missing', 
                'noFilter' => false,
                'rules' => [
                    'Valid Integer' => [
                        'constraint' => "Int",
                        'error' => 'The is_html field must be a valid number',
                        'skipEmpty' => false,
                        'sufficient' => false
                    ]
                ]               
            ],
            'message' => true      
        ];
        
        return $attribs;
    }
    
    public function sendtest()
    {
        // Test sending an email
        $templateID = 1;
        
        $emailData = array();
        $emailData["first_name"] = "Andrew";
        $emailData["last_name"] = "Chapman";
        
        $recipients = array("andy@simb.com.au");
        $cc = array("tester1@simb.com.au");
        $bcc = array("tester2@simb.com.au");
        
        if(!$this->model->sendEmail($templateID, $emailData, $recipients, $cc, $bcc)) {
            $this->error("Email sending failed");
        }
        
        $this->OK("Email sent successfully");
    }    
}
