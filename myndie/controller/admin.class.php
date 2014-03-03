<?php
namespace Myndie\Controller; 

use RedBean_Facade as R;
use Myndie\Lib\Input;
use Myndie\Lib\Session;

class Admin
{
    private $app;
    private $templatePath;
    private $cachePath;

    public function __construct($app) {
        $this->app = $app;
        $this->templatePath = MYNDIE_ABSOLUTE_PATH . "frontend/admin/templates/";
        $this->cachePath = MYNDIE_ABSOLUTE_PATH . "cache";
        $this->cachePath = false; // Disable caching
    }
  
    public function render($template) {

        $loader = new \Twig_Loader_Filesystem($this->templatePath);
        $twig = new \Twig_Environment($loader, array(
            'cache' => $this->cachePath,
        ));        
        
        $data = array();
        $data["BASE_URL"] = MYNDIE_BASE_URL;
        $data["TEMPLATE_PATH"] = $this->templatePath;
        
        // If the user is not logged in, show the login screen.
        if(!Session::sessionValid("", false)) {
            $template = "login.html";
        }

        $template = $twig->loadTemplate($template);
        $template->display($data); 
    }    
}
