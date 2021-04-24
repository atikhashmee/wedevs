<?php
 $dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
 $dotenv->safeLoad();

if (!function_exists('env')) {
    function env($key_name) {
       if (isset($_ENV[$key_name])) {
           return $_ENV[$key_name];
       }
       return null;
    }
}