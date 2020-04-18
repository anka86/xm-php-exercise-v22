<?php

// PSR-4
spl_autoload_register(function ($classname) {    
    $file = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . str_replace('\\', '/', $classname) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }     
});
