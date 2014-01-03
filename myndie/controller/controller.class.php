<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;

class Controller
{
    protected $app;                 // An instance of the Slim Framework
    protected $model;               // An instance of the primary model for this handler.
    protected $result;              // An return array with STATUS and MESSAGE nodes

    public function __construct()
    {
        $this->result = array("status" => false, "message" => "An unspecified error occured");
    }
    
    /***
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
        
        if(!$bean->id) {
            $this->result["message"] = "Invalid ID";
            $this->send();  
        }
        
        $this->outputBeansAsJson($bean);       
    }    

    /***
    * Gets a list of the items from the controller's model.
    * The $_POST array will be used by the model to achieve any filtering necessary.
    */
    public function getList()
    {
        $beans = $this->model->getList($_POST);
        $this->outputBeansAsJson($beans);       
    }  
    
    public function save($id)
    {
        $this->handleJSONContentType();
    }  

    
    /**
    * Outputs a collection of beans as a json encoded response.  Very useful for loading
    * data via AJAX.
    * 
    * @param RedBeanResult $beans
    * @param boolean $exit Set to true (default) to exit PHP processing after sending result.
    */
    protected function outputBeansAsJson($beans, $exit = true)
    {
        $this->app->response->headers->set('Content-Type', 'application/json');
        echo json_encode(R::exportAll($beans));
        
        if($exit) {
            exit();
        }
    }
    
    /***
    * Outputs the class result array as a json message
    * Used extensively for AJAX communications
    * 
    * @param boolean $exit Set to false if the app should NOT exit after the message is sent.
    */
    protected function send($exit = true)
    {
        echo json_encode($this->result);
        
        if($exit) {
            exit();
        }        
    }
    
    /***
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
