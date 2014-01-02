<?php
    define("MODE", "development");
    define("BASE_URL", "http://192.168.1.52/foundation/");
    //define("BASE_URL", "http://qa.simb.com.au/foundation/");
    //define("PASSWORD_HASH_MODE", "BCRYPT");
    define("PASSWORD_HASH_MODE", "SHA256");
    
    if(MODE == "development") {
        ini_set("display_errors", "On");
        error_reporting(E_ALL);
    }
