<?php
namespace Controller;  // All hanlders should be in the handler namespace
use RedBean_Facade as R;

class Base
{
    protected $requiredModels;      // An array containing required models for this handler
    protected $app;                 // An instance of the Slim Framework
    protected $model;               // An instance of the primary model for this handler.
    protected $result;              // An return array with STATUS and MESSAGE nodes

    public function __construct()
    {
        $this->result = array("status" => false, "message" => "An unspecified error occured");
        
        // Include any required models
        $this->includeDependencies();
    }

    /***
    * Includes any required models, libs etc as defined by the protected class variables.
    */
    protected function includeDependencies()
    {
        foreach($this->requiredModels as $modelClass) {
            if(!$this->loadModelByClassName($modelClass)) {
                die("Controller::Base::includeDependencies Model file $modelClass does not exist");
            }
        }
    }
    
    /***
    * Ensures that the specified model has been included  so it can be used.
    * 
    * @param string $modelClass The name of the model class to load
    */
    protected function loadModelByClassName($modelClass)
    {
        if(!class_exists('\\Model\\' . $modelClass)) {
            $modelFile = "backend/models/" . strtolower($modelClass) . ".class.php";
            
            if(!file_exists($modelFile)) {
                return false;
            }
            
            require_once($modelFile);    
        } 
        
        return true;       
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
    
    protected function send($exit = true)
    {
        echo json_encode($this->result);
        
        if($exit) {
            exit();
        }        
    }
    
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
