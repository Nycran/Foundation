<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 

class Role extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "role";
        
        // Call parent constructor
        parent::__construct($app);
        
        $this->defaultOrderBy = "id ASC";
    }
    
    protected function applyFilters($filters, &$where = "", &$values = array()) 
    {

    }
}
