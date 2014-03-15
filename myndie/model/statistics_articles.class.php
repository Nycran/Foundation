<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 
use Myndie\Lib\Strings;

class Statistics_articles extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "statistics_articles";
        
        // Call parent constructor
        parent::__construct($app);
        
        $this->defaultOrderBy = "id ASC";
    }
    
    protected function applyFilters($filters, &$where = "", &$values = array()) 
    {

    }           
}
