<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 
use Myndie\Lib\Strings;
use Myndie\Lib\Utils;  

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
		if(array_key_exists("is_not_allocated", $filters)) {
            $where .= " is_not_allocated = ? "; 
            $values[] = $filters["is_not_allocated"];  
        }
		
		if(array_key_exists("published_date", $filters)) {
            $where .= "AND published_date = ? "; 
			
			// Convert dates to ISO       
			$filters["published_date"] = Utils::convertUKDateToISODate($filters["published_date"]);
            $values[] = $filters["published_date"];  
        }
		
		if(array_key_exists("location", $filters)) {
            $where .= "AND location = ? "; 
            $values[] = $filters["location"];  
        }
    }           
}
