<?php
    /***
    * SIMB Foundation framework
    * Version 0.0.1
    * Copyright SIMB.com.au 2014
    * Authors: Andrew Chapman, Others will go here
    */

    // Setup backend constants
    require 'backend/config/constants.php';
        
    // Composer Autoload
    require 'vendor/autoload.php';
    
    // Setup RedBean ORM
    require 'backend/config/db.php';
    
    // Require base classes
    require 'backend/controllers/base.class.php';
    require 'backend/models/base.class.php';
    
    // Require any global libraries
    require 'backend/libraries/strings.class.php';
    require 'backend/libraries/input.class.php';
    require 'backend/libraries/utils.class.php';
    
    // Setup SLIM PHP   
    $settings = array();
    $settings["debug"] = (MODE == "development") ? true : false;
    $settings["mode"] = MODE;
    
    $app = new \Slim\Slim($settings);    
    
    // Include routes     
    require 'backend/routes/routes.php';
    
    // Run the app
    $app->run();
