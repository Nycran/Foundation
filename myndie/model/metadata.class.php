<?php
namespace Myndie\Model;  

use RedBean_Facade as R; 

class Metadata extends Model
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "metadata";
        
        // Call parent constructor
        parent::__construct($app);
        
        $this->defaultOrderBy = "id ASC";
    }
    
    protected function applyFilters($filters, &$where = "", &$values = array()) 
    {
        // For metadata records, the foreign_type and foreign_id filters MUST be set
        if((!array_key_exists("foreign_type", $filters)) || 
            (!array_key_exists("foreign_id", $filters))) {
            throw new Exception("Myndie/Model/Metadata - Missing foreign_type or foreign_id filters which MUST be set");        
        }
        
        $where .= " foreign_type = ? AND foreign_id = ? "; 
        $values[] = $filters["foreign_type"];  
        $values[] = $filters["foreign_id"];       
    }  
}
