<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 

class Country extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "country";
        
        // Call parent constructor
        parent::__construct($app);        
        
        // Override defaults
        $this->defaultOrderBy = "country ASC";
    }
    
    protected function applyFilters($filters, &$where = "", &$values = array()) 
    {
 
    }
}
