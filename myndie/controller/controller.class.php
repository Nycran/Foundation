<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;
use \Myndie\Lib\Input;

/**
* The Controller abstract class provides the skeleton that all
* controllers should inherit from.  
*/
abstract class Controller
{
    protected $app;                 // An instance of the Slim Framework
    protected $model;               // An instance of the primary model for this handler.
    protected $result;              // An return array with STATUS and MESSAGE nodes
    private $numBeans;              // Stores the total number of beans for calls such as getList.

    public function __construct()
    {
        $this->result = array("status" => false, "message" => "An unspecified error occured");
        $this->numBeans = 0;
    }
    
    /**
    * Gets a singular item for the controller's model.
    * and returns the item in JSON format.
    * 
    * @param integer $id The id of the item to load and output the JSON for
    */
    public function get($id)
    {
        if(!is_numeric($id)) {
            return false;
        }
        
        $bean = $this->model->get($id);
        
        if(!$bean) {
            $this->result["message"] = "Invalid ID";
            $this->send();  
        }
        
        $this->outputBeanAsJson($bean);       
    }    

    /**
    * Gets a list of the beans from the controller's model and then outputs as json.
    * The $_POST array will be used by the model to achieve any filtering necessary.
    */
    public function getList()
    {
        $filters = $_POST;
        $orderBy = "";
        
        $page = Input::post("page");
        if(!is_numeric($page)) {
            $page = 0;
        }
                    
        $beans = $this->model->getList($filters, $orderBy, $page, $this->numBeans);
        $this->outputBeansAsJson($beans);       
    }  
    
    /**
    * Gets a list of the rows from the controller's model and then outputs as json.
    * The $_POST array will be used by the model to achieve any filtering necessary.
    */
    public function getListSQL()
    {
        $beans = $this->model->getListSQL($_POST);
        $this->ok($beans);       
    }    
    
    /**
    * The base save method does not handle any of the save operation,
    * as the functionality required from case to case is to specific.
    * It does however do any generic preparation work.
    * 
    * @param int $id The id of the item to save
    */
    public function save($id)
    {
        $this->handleJSONContentType();
    }  
    
    /**
    * The delete method deletes specified items from the target model
    * The ids must be provided in a HTTP post variable called "ids". 
    * If there are multiple ids, they should be comma separated, e.g. 1,2,3
    */
    public function delete()
    {
        $this->handleJSONContentType();
        
        $ids = Input::post("ids");
        
        if(empty($ids)) {
            $this->result["message"] = "Please specify valid ids to delete";
            $this->send();             
        }
        
        $targets = array();
        if(is_numeric($ids)) {
            $targets[] = $ids;
        } else {
            $targets = explode(",", $ids);
        }
        
        foreach($targets as $target_id) {
            if(!$this->model->delete($target_id)) {
                $this->result["message"] = "Item with an id of $target_id could not be deleted";
                $this->send();                
            }
        }

        $this->ok();          
    }

    
    /**
    * Outputs a collection of beans as a json encoded response.  
    * The result will be a JSON encoded ARRAY of beans.
    * Very useful for loading data via AJAX.
    * 
    * @param RedBeanResult $beans
    * @param boolean $exit Set to true (default) to exit PHP processing after sending result.
    */
    protected function outputBeansAsJson($beans, $exit = true)
    {
        // Convert all beans to an array
        $beanArray = R::exportAll($beans);        
        $this->ok($beanArray);
    }
    
    /**
    * Outputs a single bean as a json encoded response. 
    * The result will be a JSON encoded single bean (NOT an array)
    * 
    * @param RedBeanResult $beans
    * @param boolean $exit Set to true (default) to exit PHP processing after sending result.
    */
    protected function outputBeanAsJson($bean, $exit = true)
    {
        // Convert all beans to an array
        $beanArray = R::exportAll($bean);
        
        // If there are no beans, just output a blank message.
        if(count($beanArray) == 0) {
            $this->ok("");
        }
        
        $this->ok($beanArray[0]);
    }    
    
    /**
    * Outputs the class result array as a json message
    * Used extensively for AJAX communications
    * 
    * @param boolean $exit Set to false if the app should NOT exit after the message is sent.
    */
    protected function send($exit = true)
    {
        $this->app->response->headers->set('Content-Type', 'application/json');
        
        echo json_encode($this->result);
        
        if($exit) {
            exit();
        }        
    }
    
    /**
    * Sends a JSON encoded "OK" result to the client
    */
    protected function ok($message = "")
    {
        $this->result["status"] = true;
        $this->result["message"] = $message;
        
        if($this->numBeans > 0) {
            $this->result["pages"] = floor($this->numBeans / MYNDIE_ITEMS_PER_PAGE);
            if(($this->numBeans % MYNDIE_ITEMS_PER_PAGE) != 0) {
                $this->result["pages"]++;    
            }
        }        
        
        $this->send();
    }
    

    /**
    * Sends a JSON encoded "ERROR" result to the client
    *     
    * @param string $message The error message to send
    */
    protected function error($message)
    {
        $this->result["status"] = false;
        $this->result["message"] = $message;
        $this->send();        
    }
    
    /**
    * The Angular JS framework oftens sends data to the server in JSON format, rather than
    * standard post vars.  This method detects JSON encoding and converts the variables back to post.
    */
    protected function handleJSONContentType()
    {
        // Detect a JSON submission and convert to $_POST
        $headers = $this->app->request->headers;
        $content_type = $headers["Content-Type"];
        if(stristr($content_type, "application/json")) {
            $_POST = (array) json_decode(file_get_contents("php://input"));
        }        
    }
}
