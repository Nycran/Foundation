<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;
use Myndie\Lib\Input;
use Myndie\Lib\Utils;  
use Myndie\Lib\Session; 

class Users extends Controller
{
    public function __construct($app)
    {
        $this->app = $app;
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Myndie\Model\Users($this->app);
    }    
    
    public function save($id)
    {
        // Invoke the base class save method to do any preparation work
        parent::save($id);
        
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
            $email = INPUT::post("email");
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
            Utils::removeArrayKey($data, "password");            
            Utils::removeArrayKey($data, "password_repeat");
            Utils::removeArrayKey($data, "salt");
        }
        
        // Save the client record (if id = 0 then a new record will be created)
        $id = $this->model->save($id, $data);

        $this->result["status"] = true;
        $this->result["message"] = $id;
        $this->send();     
    }
    
    public function login()
    {
        $email = Input::post("email");
        $password = Input::post("password");
        
        if(!$this->model->login($email, $password)) {
            $this->result["message"] = "Sorry, your login failed.  Please try again.";
            $this->send();            
        }
        
        $this->result["status"] = "OK";
        $this->result["message"] = "";
        $this->result["session_id"] = Session::getSessionID();
        $this->result["user_id"] = Session::get("user_id");
        $this->result["user_first_name"] = Session::get("user_first_name");
        $this->result["user_last_name"] = Session::get("user_last_name");
        $this->result["user_email"] = Session::get("user_email");
        
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
                        return ($in == INPUT::post("password_repeat"));
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