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
    
    /**
    * Sends an email using the specified database template, merging values from the 
    * emailData array into the template, adding any attachments.
    * 
    * @param int $templateID The ID of the template stored in the database templates table
    * @param array $emailData An array of key/value pairs to merge into the template
    * @param array $recipients An array of the recipients to send the email to
    * @param array $cc An array of the recipients to CC the email to - use false if CC is not required.
    * @param array $bcc An array of the recipients to BCC the email to - use false if BCC is not required.
    * @param array $attachments An array of the attachments to attach to the email.  Use false if no attachments.
    */
    public function sendEmailFromTemplate($templateID, $emailData, $recipients, $cc = false, $bcc = false, $attachments = false)
    {
        // Template ID must be a valid number
        if(!is_numeric($templateID)) {
            $this->app->error(new \Exception("Emailtemplate::send - Invalid template ID"));
        }
        
        // Recipients MUST be defined.
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
        $swiftTransport = new \Swift_SmtpTransport(MYNDIE_SMTP_HOST, MYNDIE_SMTP_PORT);
        
        // Create the mailer object
        $swiftMailer = \Swift_Mailer::newInstance($swiftTransport);
        
        // Create the message payload
        $swiftMessage = \Swift_Message::newInstance($template->subject, $emailMessage);
        $swiftMessage->setFrom($template->from, $template->from_name);
        $swiftMessage->setTo($recipients);
        $swiftMessage->setContentType("text/html");
        
        // Add attachments
        if(is_array($attachments)) {
            foreach($attachments as $attachment) {
                if(file_exists($attachment)) {
                    $swiftMessage->attach(\Swift_Attachment::fromPath($attachment));
                }
            }
        }
        
        // If carbon copy recipients are defined, send this email to them also.
        if(is_array($cc)) {
            $swiftMessage->setCC($cc);
        }
        
        // If blind carbon copy recipients are defined, send this email to them also.
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
