<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 
use Myndie\Lib\Strings;
// use Myndie\Lib\Session;

class Article extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "article";
        
        // Call parent constructor
        parent::__construct($app);
        
        $this->defaultOrderBy = "id ASC";
    }
    
    protected function applyFilters($filters, &$where = "", &$values = array()) 
    {
		if(array_key_exists("is_allocated", $filters)) {
            $where .= " is_allocated = ? "; 
            $values[] = $filters["is_allocated"];  
        }
    }           
}
