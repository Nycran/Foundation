<?php
    define("MODE", "development");
    define("BASE_URL", "http://192.168.1.52/foundation/");
    //define("BASE_URL", "http://qa.simb.com.au/foundation/");
    define("PASSWORD_HASH_MODE", "BCRYPT");
    //define("PASSWORD_HASH_MODE", "SHA256");
    define("ITEMS_PER_PAGE", 10);                       // Default number of items to show on each page when pagination is being used.
    
    define("SESSION_TIMEOUT", 3600);                    // Default number of seconds that a session will be valid for between updates
    define("SESSION_COOKIE_NAME", "MyndieSession");     // Default session cookie name
    define("SESSION_COOKIE_EXPIRY", "86400");           // Session cookie expiry (in seconds)
    define("SESSION_COOKIE_PATH", "/");                 // Session cookie path
    define("SESSION_COOKIE_DOMAIN", "*");                 // Session cookie path
    
    if(MODE == "development") {
        ini_set("display_errors", "On");
        error_reporting(E_ALL);
    }
