<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 

class Countries extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "countries";
        
        // Call parent constructor
        parent::__construct($app);        
        
        // Override defaults
        $this->defaultOrderBy = "country ASC";
    }
    
    protected function applyFilters($filters, &$where = "", &$values = array()) 
    {
 
    }
}
