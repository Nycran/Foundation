<?php
namespace Myndie\Model;    

use RedBean_Facade as R;

class Model
{
    protected $app;             // An instance of the Slim Framework
    protected $table;           // The name of the database table this model is working on.
    protected $defaultOrderBy;  // The default orderBy when returning a list
    protected $itemsPerPage;    // The default number of items per page

    function __construct($app)
    {
        $this->itemsPerPage = 10;
        $this->defaultOrderBy = "";
    }
    
    public function get($id)
    {
        $bean = R::load($this->table, $id);
        return $bean;
    }  
    
    public function getList($filters, $orderBy="", $page = 0, &$totalBeans = 0)
    {        
        $values = array();
        $this->applyFilters($filters, $where, $values);
        
        if($page > 0) {
            // Count all the beans                      
            $beans = R::findAll($this->table, $where, $values);
            $totalBeans = count($beans);
        }
        
        // Ensure a sensible ordering statement has been set
        if((empty($orderBy)) && (empty($this->defaultOrderBy))) {
            throw new \Exception("OrderBy not set and no default order set either");
        } else if((empty($orderBy)) && (!empty($this->defaultOrderBy))) {
            $orderBy = $this->defaultOrderBy;    
        }
        
        $suffix = $this->applyOrderAndLimit($orderBy, $page);
        
        // Get the result taking into account limit and offset           
        $beans = R::findAll($this->table, $where . $suffix, $values);
        
        return $beans;        
    }      
    
    public function save($id, $data)
    {
        // Manually inject the database table type
        $data["type"] = $this->table; 
        
        // If an ID was provided, we are updating an existing record.
        if((is_numeric($id)) && ($id > 0)) {
            $data["id"] = $id;
        }
        
        // Use the RB Cooker to convert the data array to a bean
        $bean = R::graph($data);
        
        // Save the bean
        $id = R::store($bean);  
        
        return $id;          
    }
    
    public function delete($id)
    {
        // Make sure the bean exists before attempting to delete
        $bean = $this->get($id);
        if(!$bean->id) {
            return false;
        }   
        
        // Delete the bean
        R::trash( $bean );
        
        return true; 
    }
    
    protected function applyOrderAndLimit($orderBy, $page)
    {
        $suffix = "ORDER BY " . $orderBy . " ";
        
        if($page > 0) {
            $offset = ($page - 1) * $this->itemsPerPage;
            $suffix .= "LIMIT %d OFFSET %d";
            $suffix = sprintf($suffix, $this->itemsPerPage, $offset);
        }
        
        return $suffix;        
    }
    
    public function setItemsPerPage($itemsPerPage)
    {
        if(!is_numeric($itemsPerPage)) {
            throw new \Exception("Invalid itemsPerPage value $itemsPerPage");
        }
        
        $this->itemsPerPage = $itemsPerPage;
    }
    
    public function setDefaultOrderBy($orderBy)
    {
        $this->defaultOrderBy = $orderBy;
    }
}
