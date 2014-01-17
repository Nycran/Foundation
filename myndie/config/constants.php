<?php
    define("MYNDIE_MODE", "development");
    define("MYNDIE_BASE_URL", "http://localhost/Foundation/");  // The Base URL for the website.
    //define("MYNDIE_BASE_URL", "http://qa.simb.com.au/foundation/");
    define("MYNDIE_HASH_MODE", "BCRYPT");                      // The algorithm to use for password hasing.  Can be either BRCRYPT or SHA256
    define("MYNDIE_ITEMS_PER_PAGE", 10);                       // Default number of items to show on each page when pagination is being used.
    define("MYNDIE_OUTPUT_MODE", "JSON");                      // Output mode is used to determine the appropriate way to output errors, 404s etc
    
    define("MYNDIE_SESSION_TIMEOUT", 3600);                    // Default number of seconds that a session will be valid for between updates
    define("MYNDIE_SESSION_COOKIE_NAME", "MyndieSession");     // Default session cookie name
    define("MYNDIE_SESSION_COOKIE_EXPIRY", "86400");           // Session cookie expiry (in seconds)
    define("MYNDIE_SESSION_COOKIE_PATH", "/");                 // Session cookie path
    define("MYNDIE_SESSION_CHECK_IP", "true");                 // If set to true, the session ID will be validated against the users IP address.
    define("MYNDIE_SESSION_CHECK_AGENT", "true");              // If set to true, the session ID will be validated against the browsers user agent string.
    
    define("MYNDIE_DEFAULT_USER_ROLE_ID", 2);                  // The ID of the default user role when a new user registers
    
    define("MYNDIE_ENCRYPTION_KEY", ""); 
    
    if(MYNDIE_MODE == "development") {
        ini_set("display_errors", "On");
        error_reporting(E_ALL);
    }
