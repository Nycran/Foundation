<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 
use Myndie\Lib\Strings;
use Myndie\Lib\Session;

class Schedule extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "schedule";
        
        // Call parent constructor
        parent::__construct($app);
        
        $this->defaultOrderBy = "id DESC";
    }
    
    protected function applyFilters($filters, &$where = "", &$values = array()) 
    {

    }           
}
