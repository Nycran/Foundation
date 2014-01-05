<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 

class States extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "states";
        
        // Call parent constructor
        parent::__construct($app);
        
        $this->defaultOrderBy = "state ASC";
    }
    
    private function applyFilters($filters, &$where = "", &$values = array()) 
    {
        if(array_key_exists("country_id", $filters)) {
            $where .= " country_id = ? "; 
            $values[] = $filters["country_id"];   
        }
    }
}
