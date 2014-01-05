<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 

class Session extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "session";
        
        // Call parent constructor
        parent::__construct($app);
        
        $this->defaultOrderBy = "id ASC";
    }
    
    private function applyFilters($filters, &$where = "", &$values = array()) 
    {

    }
}
