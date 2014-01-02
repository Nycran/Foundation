<?php
namespace Controller;  // All hanlders should be in the handler namespace
use RedBean_Facade as R;

class States extends Base
{
    public function __construct($app)
    {
        $this->app = $app;
        $this->requiredModels = array("States");
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Model\States($this->app);
    }
    
    public function get($id)
    {
        $bean = $this->model->get($id);
        
        if(!$bean->id) {
            $this->result["message"] = "Invalid ID";
            $this->send();  
        }
        
        $this->outputBeansAsJson($bean);       
    }    

    public function getList()
    {
        $beans = $this->model->getList($_POST);
        $this->outputBeansAsJson($beans);       
    }
}
