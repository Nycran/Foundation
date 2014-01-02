<?php
namespace Model;  
use RedBean_Facade as R; 

class Countries extends Base
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->table = "countries";
        
        // Call parent constructor
        parent::__construct($app);
    }
    
    public function getList($filters, $orderBy = "country ASC", $page = 0, &$totalBeans = 0)
    {        
        $values = array();
        $this->applyFilters($filters, $where, $values);
        
        if($page > 0) {
            // Count all the beans                      
            $beans = R::findAll($this->table, $where, $values);
            $totalBeans = count($beans);
        }
        
        $suffix = $this->applyOrderAndLimit($orderBy, $page);
        
        // Get the result taking into account limit and offset           
        $beans = R::findAll($this->table, $where . $suffix, $values);
        return $beans;        
    }
    
    private function applyFilters($filters, &$where = "", &$values = array()) 
    {
 
    }
}
