<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 

class Emailtemplate extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "emailtemplate";
        
        // Call parent constructor
        parent::__construct($app);
        
        $this->defaultOrderBy = "id ASC";
    }
        
    protected function applyFilters($filters, &$where = "", &$values = array()) 
    {
        if(array_key_exists("code", $filters)) {
            $where .= " code = ? "; 
            $values[] = $filters["code"];  
        }
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
        $template = $this->get($templateID);
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
            return false;
        }
        
        return true;
    }    
}
