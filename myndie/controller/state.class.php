<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;

class State extends Controller
{
    public function __construct($app)
    {
        $this->app = $app;
        
        // Call parent constructor
        parent::__construct();
        
        $this->model = new \Myndie\Model\State($this->app);
    }
}
