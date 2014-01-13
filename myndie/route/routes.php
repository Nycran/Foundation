<?php
    /***
    * Get list of countries
    */
    $app->get('/api/country/list', function () use ($app) {       
        //require_once("myndie/controllers/countries.class.php");
        $controller = new \Myndie\Controller\Country($app);
        $controller->getList();
    }); 
    
    /***
    * Get list of states for a specific country
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
    */    
    $app->get('/api/user/save/:id', function ($id) use ($app) {       
        // Inject test data
        $_POST["first_name"] = "Andrew";
        $_POST["last_name"] = "Chapman";
        $_POST["email"] = "tester1@simb.com.au";
        $_POST["password"] = "mango77z";
        $_POST["password_repeat"] = "mango77z";
        $_POST["roles"] = "1,2";    // Test adding multiple roles

        $controller = new \Myndie\Controller\User($app);
        $controller->save($id);
    })->conditions(array("id" => '\d+'));
    
    /***
    * Saves a user to the database
    * If an ID of 0 is passed, a new user will be created
    */    
    $app->get('/api/user/save/:id', function ($id) use ($app) {       
        // Inject test data
        $_POST["first_name"] = "Andrew";
        $_POST["last_name"] = "Chapman";
        $_POST["email"] = "tester1@simb.com.au";
        $_POST["password"] = "mango77z";
        $_POST["password_repeat"] = "mango77z";
        $_POST["roles"] = "1,2";    // Test adding multiple roles

        $controller = new \Myndie\Controller\User($app);
        $controller->save($id);
    })->conditions(array("id" => '\d+'));          
    
    /***
    * Handles a user login request.
    * Both email and password should be passed via HTTP Post
    */    
    $app->post('/api/user/login', function () use ($app) {       
        $controller = new \Myndie\Controller\User($app);
        $controller->login();
    }); 
    
    /***
    * Handles user registration requests
    */    
    $app->post('/api/user/register', function () use ($app) {       
        $controller = new \Myndie\Controller\User($app);
        $controller->register();
    }); 
    
    /***
    * Gets the details of a single email template
    */    
    $app->get('/api/emailtemplate/get/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\Emailtemplate($app);
        $controller->get($id);
    })->conditions(array("id" => '\d+'));   
    
    /***
    * Gets a list of email templates
    */    
    $app->get('/api/emailtemplate/list', function () use ($app) {       
        $controller = new \Myndie\Controller\Emailtemplate($app);
        $controller->getList();
    });
    
    /***
    * Deletes either an individual emailtemplate or the list of specified templates
    */     
    $app->post('/api/emailtemplate/delete', function () use ($app) {       
        // Inject test data 
        $controller = new \Myndie\Controller\Emailtemplate($app);
        $controller->delete();
    }); 
    
    $app->get('/api/emailtemplate/sendtest', function () use ($app) {       
        // Inject test data 
        $controller = new \Myndie\Controller\Emailtemplate($app);
        $controller->sendtest();
    });        
   
