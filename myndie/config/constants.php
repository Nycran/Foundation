<?php
<<<<<<< HEAD
    define("MYNDIE_MODE", "development");
    define("MYNDIE_BASE_URL", "http://localhost/Foundation/");  // The Base URL for the website.
    //define("MYNDIE_BASE_URL", "http://qa.simb.com.au/foundation/");
    define("MYNDIE_HASH_MODE", "BCRYPT");                      // The algorithm to use for password hasing.  Can be either BRCRYPT or SHA256
    define("MYNDIE_ITEMS_PER_PAGE", 10);                       // Default number of items to show on each page when pagination is being used.
    define("MYNDIE_OUTPUT_MODE", "JSON");                      // Output mode is used to determine the appropriate way to output errors, 404s etc
=======
    define("MYNDIE_LIVE_URL", "http://www.myndie.com/");        // Define the site LIVE URL.  Used to set the app automatically into debug mode.
    define("MYNDIE_HASH_MODE", "BCRYPT");                       // The algorithm to use for password hasing.  Can be either BRCRYPT or SHA256
    define("MYNDIE_ITEMS_PER_PAGE", 10);                        // Default number of items to show on each page when pagination is being used.
    define("MYNDIE_OUTPUT_MODE", "JSON");                       // Output mode is used to determine the appropriate way to output errors, 404s etc
>>>>>>> 56d648395bb583d3529ccc85d643cee56b6569c4
    
    // Session settings
    define("MYNDIE_SESSION_TIMEOUT", 3600);                     // Default number of seconds that a session will be valid for between updates
    define("MYNDIE_SESSION_COOKIE_NAME", "MyndieSession");      // Default session cookie name
    define("MYNDIE_SESSION_COOKIE_EXPIRY", "86400");            // Session cookie expiry (in seconds)
    define("MYNDIE_SESSION_COOKIE_PATH", "/");                  // Session cookie path
    define("MYNDIE_SESSION_CHECK_IP", "true");                  // If set to true, the session ID will be validated against the users IP address.
    define("MYNDIE_SESSION_CHECK_AGENT", "true");               // If set to true, the session ID will be validated against the browsers user agent string.
    
    // Define user roles here for convenience and code readability
    define("MYNDIE_ROLE_ADMIN", 1);
    define("MYNDIE_ROLE_MEMBER", 2);
    define("MYNDIE_DEFAULT_USER_ROLE", MYNDIE_ROLE_MEMBER);  // The ID of the default user role when a new user registers
    
    // SMTP SERVER SETTINGS
    define("MYNDIE_SMTP_HOST", "localhost");
    define("MYNDIE_SMTP_PORT", 25);
    
    // Resolve base url and absolute path 
    $protocol = ((array_key_exists("HTTPS", $_SERVER)) && ($_SERVER["HTTPS"] != "off")) ? "https" : "http";
    $domain = (array_key_exists("SERVER_NAME", $_SERVER)) ? $_SERVER["SERVER_NAME"] : "";
    $scriptName = (array_key_exists("SCRIPT_NAME", $_SERVER)) ? $_SERVER["SCRIPT_NAME"] : "";
    $scriptFileName = (array_key_exists("SCRIPT_FILENAME", $_SERVER)) ? $_SERVER["SCRIPT_FILENAME"] : "";

    if((empty($domain)) || (empty($scriptName))) {
        die("Couldn't resolve server domain or script name");
    }
    
    $folder = str_replace("index.php", "", $scriptName);
    
    define("MYNDIE_BASE_URL", $protocol . "://" . $domain . $folder);
    define("MYNDIE_ABSOLUTE_PATH", str_replace("index.php", "", $scriptFileName));
    define("MYNDIE_MODE", (MYNDIE_BASE_URL == MYNDIE_LIVE_URL) ? "live" : "development");   // Set development mode automatically.
    
    define("MYNDIE_ENCRYPTION_KEY", ""); 
    
    if(MYNDIE_MODE == "development") {
        ini_set("display_errors", "On");
        error_reporting(E_ALL);
    } else {
        ini_set("display_errors", "Off");       
    }
