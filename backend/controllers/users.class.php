<?php
namespace Controller;  // All hanlders should be in the handler namespace
use RedBean_Facade as R;

class Users extends Base
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->requiredModels = array("Users");
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Model\Users($this->app);
    }
    
    public function get($id)
    {
        $bean = $this->model->get($id);
        
        if(!$bean->id) {
            $this->result["message"] = "Invalid ID";
            $this->send();  
        }
        
        $this->outputBeansAsJson($bean);       
    }    

    public function getList()
    {
        $beans = $this->model->getList($_POST);
        $this->outputBeansAsJson($beans);       
    }     
    
    public function save($id)
    {
        $this->handleJSONContentType();
        
        $add_new_mode = ($id == 0);     // Will be true if we're adding a new user (i.e. id == 0)
        
        /************ VALIDATION **************/
        $profile = new \DataFilter\Profile();
        
        // Set global validation checks
        $profile->addPreFilters(['Trim', 'StripHtml']);
        $profile->setAttribs($this->getValidationAttribs($add_new_mode)); // Set the password required if id == 0 (i.e. we're adding a new user)
          
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
        
        if($add_new_mode) {
            // If we're adding a new user, ensure this email address does NOT already exist
            $email = \INPUT::post("email");
            $users = $this->model->getList(array("email" => $email));
            if(count($users) > 0) {
                $this->result["message"] = "Sorry, an account with this email address already exists";
                $this->send();                
            }
            
            // Hash the password
            $this->model->hashPassword($data["password"], $hashed_password, $salt);
            $data["password"] = $hashed_password;
            $data["salt"] = $salt;
        } else {
            // If we're updating an existing user, the password and salt values may NOT be changed by using this method.
            \Utils::removeArrayKey($data, "password");            
            \Utils::removeArrayKey($data, "password_repeat");
            \Utils::removeArrayKey($data, "salt");
        }
        
        // TODO 2 -o achapman -c flow: Start a DB transaction, and after the saving, update/insert the attributes record
        
        // Save the client record (if id = 0 then a new record will be created)
        $id = $this->model->save($id, $data);

        $this->result["status"] = true;
        $this->result["message"] = $id;
        $this->send();     
    }
    
    /***
    * Returns the form validation rules for adding a new user.
    * @param boolean $add_new_mode Set to true if we're adding a new user
    */
    private function getValidationAttribs($add_new_mode)
    {
        $attribs = [
            'first_name' => true,
            'last_name' => true,
            'email' => [
                'required' => true, 
                'matchAny' => false, 
                'default' => null, 
                'missing' => 'This email field is missing', 
                'noFilter' => false,
                'rules' => [
                    'Valid Email' => [
                        'constraint' => "Email",
                        'error' => 'The provided email address is invalid',
                        'skipEmpty' => false,
                        'sufficient' => false
                    ]
                ]               
            ],
            'password' => [
                'required' => $add_new_mode, 
                'matchAny' => false, 
                'default' => null, 
                'missing' => 'This password field is missing', 
                'noFilter' => false,
                'rules' => [
                    'Minimum Length' => [
                        'constraint' => "LenMin:7",
                        'error' => 'Your password must be at last 7 characters long',
                        'skipEmpty' => false,
                        'sufficient' => false
                    ],
                    'Password Match' => [
                        'constraint' => function($in) {
                        return ($in == \INPUT::post("password_repeat"));
                        },
                        'error' => 'Your passwords do not match.  Please reenter your passwords and try again.',
                        'skipEmpty' => false,
                        'sufficient' => false
                    ]                                  
                ]
            ],            
        ];
        
        return $attribs;
    }
}
