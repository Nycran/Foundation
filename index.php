<?php
    /***
    * Myndie Foundation Framework
    * Version 0.0.1
    * Copyright SIMB.com.au 2014
    * Authors: Andrew Chapman, Others will go here
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
    $settings["debug"] = (MODE == "development") ? true : false;
    $settings["mode"] = MODE;
    
    $app = new \Slim\Slim($settings);    
    
    // Include routes     
    require 'myndie/route/routes.php';
    
    // Run the app
    $app->run();  
