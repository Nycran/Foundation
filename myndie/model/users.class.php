<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 
use Myndie\Lib\Strings;
use Myndie\Lib\Session;

class Users extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "users";
        
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
    * Hashes the specific input password, and generates a resultant hash.
    * Note the hash will either be BCRYPT or SHA256, depending on the 
    * PASSWORD_HASH_MODE setting in the contants file.
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
        
        if(PASSWORD_HASH_MODE == "BCRYPT") {
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
        
        $user = $this->getSingleBean(array("email" => $email));
        if(!$user) {
            return false;    
        }
        
        // If we're using PHP's built in password hashing system, use the password_verify to test.
        if(PASSWORD_HASH_MODE == "BCRYPT") {
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
        
        // Setup session data
        Session::set("user_id", $user->id);
        die("HERE");
        Session::set("user_first_name", $user->first_name);
        Session::set("user_last_name", $user->last_name);
        Session::set("user_email", $user->email);
        
        return true;
    }    
}
