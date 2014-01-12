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
}
