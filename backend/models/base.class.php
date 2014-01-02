<?php
namespace Model;    
use RedBean_Facade as R;

class Base
{
    protected $app; // An instance of the Slim Framework
    protected $table;
    protected $itemsPerPage;

    function __construct($app)
    {
        $this->itemsPerPage = 10;
    }
    
    public function get($id)
    {
        $bean = R::load($this->table, $id);
        return $bean;
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
}
