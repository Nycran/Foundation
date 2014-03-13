<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 
use Myndie\Lib\Strings;

class User extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "user";
        
        // Call parent constructor
        parent::__construct($app);
        
        $this->defaultOrderBy = "id ASC";
    }
    
    protected function applyFilters($filters, &$where = "", &$values = array()) 
    {
        if(array_key_exists("email", $filters)) {
            $where .= " email = ? "; 
            $values[] = $filters["email"];  
        }
    }
    
    /***
    * Loads a single user bean from the database and removes any references to password or salt.
    * Note: we're overriding the base class method - normally we would NOT include this method
    * in the model.
    * 
    * @param integer  $id The ID of the bean being loaded
    * @return \RedBean_OODBBean
    */
    public function get($id)
    {
        $bean = parent::get($id);
        
        if(!$bean) {
            return false;   
        }

        // Manually remove password and salt fields.
        unset($bean->password);
        unset($bean->salt);
        
        return $bean;
    }
    
    /***
    * Loads a list of user beans from the database, taking into account any filters specified in the database
    * and removes any references to password or salt fields.
    * 
    * Note: we're overriding the base class method - normally we would NOT include this method
    * in the model.
    */
    public function getList($filters, $orderBy="", $page = 0, &$totalBeans = 0, $dropSecretFields = true)
    {        
        $beans = parent::getList($filters, $orderBy, $page, $totalBeans);
        $num_users = count($beans);
        
        if(($dropSecretFields) && ($num_users > 0)) {
            foreach($beans as $bean) {
                unset($bean->password);
                unset($bean->salt);                
            }
        }

        return $beans;        
    }
    
    /**
    * Returns the first user bean that matches the specified filters
    * 
    * @param array $filters The array of filters
    * @param boolean $dropSecretFields If set to false, the user password and salt fields will NOT be dropped
    * and will therefore be accessible.
    * 
    * Note: we're overriding the base class method - normally we would NOT include this method
    * in the model.
    * 
    * @returns a RedBean bean
    */
    public function getSingleBean($filters, $dropSecretFields = true)
    {
        $beans = $this->getList($filters, "", 0, $totalBeans, $dropSecretFields);   
        
        if(count($beans) < 1) {
            return false;             
        }
        
        $bean = array_pop($beans);
        return $bean;
    }            
    
    /***
    * Hashes the specific input password, and generates a resultant hash.
    * Note the hash will either be BCRYPT or SHA256, depending on the 
    * MYNDIE_HASH_MODE setting in the contants file.
    * 
    * @param string $input The plain text password to hash
    * @param string $password An output param - the resultant hash
    * @param string $salt An output param - the resultant salt (note: no salt will be generated if BCRYPT is used)
    */
    public function hashPassword($input, &$password, &$salt = "")
    {
        if(empty($input)) {
            return false;
        }
        
        if(MYNDIE_HASH_MODE == "BCRYPT") {
            $password = password_hash($input, PASSWORD_BCRYPT);
            $salt = "";
            return true;
        }
        
        // If no salt value has been passed, create one
        if(empty($salt)) {
            $salt = Strings::createRandomString();
        }
        
        // Hash the password using the salt.
        $password = hash("SHA256", $input . $salt);

        return true;
    }
    
    public function login($email, $password)
    {
        if((empty($email)) || (empty($password))) {
            return false;
        }
        
        $user = $this->getSingleBean(array("email" => $email), false);
        if(!$user) {
            return false;    
        }       
        
        // If we're using PHP's built in password hashing system, use the password_verify to test.
        if(MYNDIE_HASH_MODE == "BCRYPT") {
            if(!password_verify($password, $user->password)) {
                return false;
            }
        } else {
            // Use the SHA256 hashing method.
            // Does the password match
            $this->hashPassword($password, $hashedPassword, $user->salt);
            
            if($user->password != $hashedPassword) {
                return false;
            }
        }
        
        // Create a new session
        if(!\Myndie\Lib\Session::createSession()) {
            $this->app->error(new \Exception("Myndie/Model/User::login - Failed to create session"));
        }
        
        // Get the user's roles
        $roles = $user->sharedRole;
        $numRoles = count($roles);
        
        if($numRoles == 0) {
            $app->error(new \Exception("Myndie/Lib/Firewall::run - No roles assigned to this user"));        
        }
        
        $roleCSV = "";
        
        foreach($roles as $role) {
            if(!empty($roleCSV)) {
                $roleCSV .= ",";
            }
            
            $roleCSV .= $role->id;
        }       

        // Setup session data
        \Myndie\Lib\Session::set("user_id", $user->id);
        \Myndie\Lib\Session::set("user_first_name", $user->first_name);
        \Myndie\Lib\Session::set("user_last_name", $user->last_name);
        \Myndie\Lib\Session::set("user_email", $user->email);
        \Myndie\Lib\Session::set("user_roles", $roleCSV);

        return true;
    }    
}
