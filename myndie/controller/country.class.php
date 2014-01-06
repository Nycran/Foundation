<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;

class Country extends Controller
{
    public function __construct($app)
    {
        $this->app = $app;
        
        // Call parent constructor to initialise and enforce permissions
        parent::__construct();
        
        $this->model = new \Myndie\Model\Country($this->app);
    }
}
