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
    
    public function sendEmail($templateID, $emailData, $recipients, $cc = false, $bcc = false)
    {
        if(!is_numeric($templateID)) {
            $this->app->error(new \Exception("Emailtemplate::send - Invalid template ID"));
        }
        
        if((!is_array($recipients)) || (count($recipients) == 0)) {
            $this->app->error(new \Exception("Emailtemplate::send - No recipients defined"));
        }
        
        // Load the template
        $template = $this->model->get($templateID);
        if(!$template) {
            $this->app->error(new \Exception("Emailtemplate::send - Couldn't load template with an ID of $templateID"));    
        }
        
        // Use TWIG to substitute any variables found in the template with those in the emailData array
        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);
        $emailMessage = $twig->render($template->message, $emailData);
        
        // Use the SWIFT Mailer engine to send the email
        // Define the transport engine
        $swiftTransport = new \Swift_SmtpTransport("localhost", 25);
        
        // Create the mailer object
        $swiftMailer = \Swift_Mailer::newInstance($swiftTransport);
        
        // Create the message payload
        $swiftMessage = \Swift_Message::newInstance($template->subject, $emailMessage);
        $swiftMessage->setFrom($template->from, $template->from_name);
        $swiftMessage->setTo($recipients);
        $swiftMessage->setContentType("text/html");
        
        if(is_array($cc)) {
            $swiftMessage->setCC($cc);
        }
        
        if(is_array($bcc)) {
            $swiftMessage->setCC($bcc);
        }        

        // Send the message
        $result = $swiftMailer->send($swiftMessage);
        if(!$result) {
            $this->error("The email template could not be sent");
        }
        
        $this->OK("Email sent successfully");
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
        
        $this->sendEmail($templateID, $emailData, $recipients, $cc, $bcc);
    }    
}
