<?php
namespace Myndie\Model;    

use RedBean_Facade as R;

class Model
{
    protected $app;             // An instance of the Slim Framework
    protected $table;           // The name of the database table this model is working on.
    protected $defaultOrderBy;  // The default orderBy when returning a list
    protected $itemsPerPage;    // The default number of items per page

    /***
    * The class constructor.  Sets default items per page for 
    * pagination and initialises other variables.
    * 
    * @param mixed $app
    * @return Model
    */
    function __construct($app)
    {
        $this->itemsPerPage = ITEMS_PER_PAGE;   // Defaults items per page to the value defined in the constants file.
        $this->defaultOrderBy = ""; // Default order by must be set on a model by model basis.
    }
    
    /***
    * Loads a single bean from the database for the current table
    * 
    * @param integer  $id The ID of the bean being loaded
    * @return \RedBean_OODBBean
    */
    public function get($id)
    {
        $bean = R::load($this->table, $id);
        return $bean;
    }  
    
    /***
    * Loads a list of beans from the database, taking into account any filters specified in the database.
    * 
    * @param array $filters An associative array of any filters to apply
    * @param string $orderBy An order by statement.  If not provided, the models default order by will be used.
    * @param int $page If you want to load a limited set of the data for pagination, specify the current page number.  Pass 0 for no pagination.
    * @param int $totalBeans An output param, if you're using pagination this will populate with the total number of beans.
    * @return \RedBean_OODBBean[]
    */
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
    
    /***
    * Saves a record/bean to the database.  If the id value is blank or 0, a new bean will be created.
    * Otherwise the bean will be updated.
    * 
    * @param \RedBean_OODB $id  The id of the bean
    * @param array $data  An associative array of the data to update/insert.
    * @return The id of the record after saving/inserting.
    */
    public function save($id, $data)
    {
        // Manually inject the database table type
        $data["type"] = $this->table; 
        
        // If an ID was provided, we are updating an existing record.
        if((is_numeric($id)) && ($id > 0)) {
            $data["id"] = $id;
        }
        
        // Create a transaction to ensure the record AND its metadata are both updated/created
        // before ccommitting.
        R::begin();
        
        try {
            // Use the RB Cooker to convert the data array to a bean
            $bean = R::graph($data);
            
            // Save the bean
            $id = R::store($bean);
            
            // Don't create a metadata record for metadata records :-)
            if($this->table == "metadata") {
                R::commit();
                
                return $id;
            }  
            
            // See if an existing metadata bean exists for this record
            $objMetadata = new \Myndie\Model\Metadata($this->app);
            $metadata = $objMetadata->getSingleBean(array("foreign_type" => $this->table, "foreign_id" => $id));
            
            // If no existing metadata record exists, create a new one.
            if(!$metadata) {
                // Create a new metadata record
                $metadata = R::dispense("metadata");            
                $metadata->foreign_id = $id;
                $metadata->foreign_type = $this->table;
                $metadata->created_dtm = date("Y-m-d H:i:s");
            }
            
            // TODO 2 -o achapman -c flow: Set created by and modified by if the user is logged in.

            // Update last modified details
            $metadata->modified_dtm = date("Y-m-d H:i:s");
            $meta_id = R::store($metadata);
            
            R::commit();

            return $id; 
        }
        catch(Exception $e) {
            R::rollback();
            return false;
        }         
    }
    
    /***
    * Deletes a record/bean from the database
    * 
    * @param integer $id The id of the bean to delete.
    * @return true on successful delete, false on failure.
    */
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
    
    /***
    * Constructs the Order By and LIMIT/OFFSET statement based on variables 
    * set within the model.
    * 
    * @param string $orderBy The column to order the results by
    * @param int $page The current page number for pagination.  If the page is 0, no LIMIT or OFFSET will be applied.
    */
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
    
    /***
    * Returns the first bean that matches the specified filters
    * 
    * @param array $filters The array of filters
    * @returns a RedBean bean
    */
    public function getSingleBean($filters)
    {
        $beans = $this->getList($filters);
        if(count($beans) < 1) {
            return false;             
        }
        
        $bean = array_pop($beans);
        return $bean;
    }
}