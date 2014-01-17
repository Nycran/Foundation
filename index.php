<?php
    /**
    * Myndie Foundation Framework
    * @version 1.0
    * @author SIMB Pty Ltd http://www.simb.com.au
    * @license http://opensource.org/licenses/MIT MIT
    * Please use the PSR-2 Coding Standard
    * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
    */
    
    // Define Myndie autoloader
    spl_autoload_register(function ($class) {
        // Only autoload Myndie classes
        if(!strstr($class, "Myndie")) {
            return;
        }
        
        // Convert "\" namespace charters to "/" for paths, and convert to lower case
        $path = str_replace('\\', '/', strtolower($class)) . '.class.php';
        require $path;
    });     

    // Setup backend constants
    require 'myndie/config/constants.php';
        
    // Composer Autoload
    require 'vendor/autoload.php';   
    
    // Setup RedBean ORM
    require 'myndie/config/db.php';

    // Setup SLIM PHP   
    $settings = array();
    $settings["debug"] = (MYNDIE_MODE == "development") ? true : false;
    $settings["mode"] = MYNDIE_MODE;
    
    $app = new \Slim\Slim($settings);    
    
    // Include routes     
    require 'myndie/route/routes.php';
    
    // Setup the error handler
    $app->error(function (Exception $e) use ($app) {
        if(MYNDIE_OUTPUT_MODE == "JSON") {
            $return = array();
            $return["status"] = false;
            $return["message"] = $e->getMessage();
            
            echo json_encode($return);
            exit();
        } else {
            die("NEED TO HANDLE NON JSON FORMAT ERRORS");
            $app->render('error.php');
        }
    });    
    
    
    // Enforce firewall permissions as a Slim "before.dispatch" hook.  This fires before any controllers are fired.
    $app->hook('slim.before.dispatch', function () use ($app) {
        \Myndie\Lib\Firewall::run($app);
    });    
    
    // Run the app
    $app->run();  
