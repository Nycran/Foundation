<?php
    /**
    * Get list of countries
    */
    $app->get('/admin/', function () use ($app) {     
        $admin = new \Myndie\Controller\Admin($app);
        $admin->render("admin.html");
    }); 
    
    /**************************************** USERS *********************************************
    * User Routes
    ********************************************************************************************/    

    /**
    * Handles a user login request.
    * Both email and password should be passed via HTTP Post
    */    
    $app->post('/api/user/login', function () use ($app) {       
        $controller = new \Myndie\Controller\User($app);
        $controller->login();
    }); 

    /**
    * Handles a user logout request.
    */    
    $app->post('/api/user/logout', function () use ($app) {       
        $controller = new \Myndie\Controller\User($app);
        $controller->logout();
    });  
    
    /**
    * Get a list of users
    */    
    $app->post('/api/user/list', function () use ($app) {       
        $controller = new \Myndie\Controller\User($app);
        $controller->getList();
    });     
    
    /**
    * Get a single user by ID
    */    
    $app->get('/api/user/get/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\User($app);
        $controller->get($id);
    })->conditions(array("id" => '\d+')); 
    
    /**
    * Saves a user to the database
    * If an ID of 0 is passed, a new user will be created
    */    
    $app->post('/api/user/save/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\User($app);
        $controller->save($id);
    })->conditions(array("id" => '\d+'));   
    
    /**
    * Handle password reset request
    */    
    $app->post('/api/user/password_reset/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\User($app);
        $controller->passwordReset($id);
    })->conditions(array("id" => '\d+')); 
    
    /**
    * Delete users
    */    
    $app->post('/api/user/delete', function () use ($app) {       
        $controller = new \Myndie\Controller\User($app);
        $controller->delete();
    }); 
    
    /**
    * Handle save locations request
    */    
    $app->post('/api/user/save_locations/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\User($app);
        $controller->saveLocations($id);
    })->conditions(array("id" => '\d+'));     
    
    /**************************************** LOCATIONS*****************************************
    * Location Routes
    ********************************************************************************************/                  
    
    /**
    * Get a list of locations
    */    
    $app->post('/api/location/list', function () use ($app) {       
        $controller = new \Myndie\Controller\Location($app);
        $controller->getList();
    });     
    
    /**
    * Get a single user by ID
    */    
    $app->get('/api/location/get/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\Location($app);
        $controller->get($id);
    })->conditions(array("id" => '\d+')); 
    
    /**
    * Saves a locations to the database
    * If an ID of 0 is passed, a new location will be created
    */    
    $app->post('/api/location/save/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\Location($app);
        $controller->save($id);
    })->conditions(array("id" => '\d+'));   
    
    /**
    * Delete locations
    */    
    $app->post('/api/location/delete', function () use ($app) {       
        $controller = new \Myndie\Controller\Location($app);
        $controller->delete();
    });     
    
    /**************************************** CATEGORIES****************************************
    * Category Routes
    ********************************************************************************************/                  
    
    /**
    * Get a list of categories
    */    
    $app->post('/api/category/list', function () use ($app) {       
        $controller = new \Myndie\Controller\Category($app);
        $controller->getList();
    });     
    
    /**
    * Get a single user by ID
    */    
    $app->get('/api/category/get/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\Category($app);
        $controller->get($id);
    })->conditions(array("id" => '\d+')); 
    
    /**
    * Saves a category to the database
    * If an ID of 0 is passed, a new category will be created
    */    
    $app->post('/api/category/save/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\Category($app);
        $controller->save($id);
    })->conditions(array("id" => '\d+'));       
    
    
    /**
    * Delete categories
    */    
    $app->post('/api/category/delete', function () use ($app) {       
        $controller = new \Myndie\Controller\Category($app);
        $controller->delete();
    });  
    
	/**************************************** ARTICLES****************************************
    * Articles Routes
    ********************************************************************************************/                  
    
    /**
    * Get a list of categories
    */    
    $app->post('/api/article/list', function () use ($app) {       
        $controller = new \Myndie\Controller\Article($app);
        $controller->getList();
    });     
    
    /**
    * Get a single user by ID
    */    
    $app->get('/api/article/get/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\Article($app);
        $controller->get($id);
    })->conditions(array("id" => '\d+')); 
    
    /**
    * Saves a article to the database
    * If an ID of 0 is passed, a new article will be created
    */    
    $app->post('/api/article/save/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\Article($app);
        $controller->save($id);
    })->conditions(array("id" => '\d+'));       
    
    
    /**
    * Delete articles
    */    
    $app->post('/api/article/delete', function () use ($app) {       
        $controller = new \Myndie\Controller\Article($app);
        $controller->delete();
    });  
    
    /**************************************** SPONSORS ****************************************
    * Sponsor Routes
    ******************************************************************************************/                  
    
    /**
    * Get a list of Sponsors
    */    
    $app->post('/api/sponsor/list', function () use ($app) {       
        $controller = new \Myndie\Controller\Sponsor($app);
        $controller->getList();
    });     
    
    /**
    * Get a single Sponsor by ID
    */    
    $app->get('/api/sponsor/get/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\Sponsor($app);
        $controller->get($id);
    })->conditions(array("id" => '\d+')); 
    
    /**
    * Saves a Sponsor to the database
    * If an ID of 0 is passed, a new category will be created
    */    
    $app->post('/api/sponsor/save/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\Sponsor($app);
        $controller->save($id);
    })->conditions(array("id" => '\d+'));       
    
   
    $app->post('/api/sponsor/delete', function () use ($app) {       
        $controller = new \Myndie\Controller\Sponsor($app);
        $controller->delete();
    }); 
    
    $app->post('/api/sponsor/delete_logo/:id', function ($id) use ($app) {       
        $controller = new \Myndie\Controller\Sponsor($app);
        $controller->deleteLogo($id);
    })->conditions(array("id" => '\d+'));       
    
    /**************************************** Images ****************************************
    * Image Routes
    ******************************************************************************************/                  
    $app->post('/api/images/upload/:imageFor/:id/:fileName', function ($imageFor, $id, $fileName) use ($app) {       
        $controller = new \Myndie\Controller\Image($app);
        $controller->uploadImage($imageFor, $id, $fileName, true);
    })->conditions(array("id" => '\d+')); 
    
    /**************************************** ROLES *********************************************
    * Role Routes
    ********************************************************************************************/                      
    
    /**
    * Get a list of roles
    */    
    $app->get('/api/role/list', function () use ($app) {       
        $controller = new \Myndie\Controller\Role($app);
        $controller->getListSQL();
    });      