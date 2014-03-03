<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 
use Myndie\Lib\Strings;
use Myndie\Lib\Session;

class Sponsor extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "sponsor";
        
        // Call parent constructor
        parent::__construct($app);
        
        $this->defaultOrderBy = "id ASC";
    }
    
    protected function applyFilters($filters, &$where = "", &$values = array()) 
    {

    }           
}
