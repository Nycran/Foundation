<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 
use Myndie\Lib\Strings;

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
    public function hashPassword($input, &$password, &$salt)
    {
        if(empty($input)) {
            return false;
        }
        
        if(PASSWORD_HASH_MODE == "BCRYPT") {
            $password = password_hash($input, PASSWORD_BCRYPT);
            $salt = "";
            return true;
        }
        
        $salt = Strings::createRandomString();
        $password = hash("SHA256", $input . $salt);

        return true;
    }
    
    public function login($email, $password)
    {
        if((empty($email)) || (empty($password))) {
            return false;
        }
        
        $users = $this->getList(array("email" => $email));
        if(count($users) != 1) {
            return false;             
        }
        
        print_r($users);
        
        $user = $users[0];
        
        die("HERE");        
        
    }    
}
