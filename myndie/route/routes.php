<?php
    /***
    * Get list of countries
    * ID #1
    */
    $app->get('/api/country/list', function () use ($app) {       
        //require_once("myndie/controllers/countries.class.php");
        $controller = new \Myndie\Controller\Country($app);
        $controller->getList();
    }); 
    
    /***
    * Get list of states for a specific country
    * ID #2
    */    
    $app->get('/api/state/list/:id', function ($country_id) use ($app) {       
        // Inject country ID filter
        $_POST["country_id"] = $country_id;
        $controller = new \Myndie\Controller\State($app);
        $controller->getList();
    })->conditions(array("id" => '\d+')); 
    
    
    /***
    * Saves a user to the database
    * If an ID of 0 is passed, a new user will be created
    * ID #3
    */    
    $app->get('/api/user/save/:id', function ($id) use ($app) {       
        // Inject test data
        $_POST["first_name"] = "Andrew";
        $_POST["last_name"] = "Chapman";
        $_POST["email"] = "andy@simb.com.au";
        $_POST["password"] = "mango77z";
        $_POST["password_repeat"] = "mango77z";

        $controller = new \Myndie\Controller\User($app);
        $controller->save($id);
    })->conditions(array("id" => '\d+'));     
    
    /***
    * Handles a user login request.
    * Both email and password should be passed via HTTP Post
    * ID #4
    */    
    $app->get('/api/user/login', function () use ($app) {       
        // Inject test data
        $_POST["email"] = "andy@simb.com.au";
        $_POST["password"] = "mango77z";

        $controller = new \Myndie\Controller\User($app);
        $controller->login();
    });             
   
