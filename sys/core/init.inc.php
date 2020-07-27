<?php
    declare(strict_types=1);

    include_once '../sys/config/db-cred.inc.php';
    foreach ($CONST as $name => $val){
        define($name, $val);
    }

    $name = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    $db = new PDO($name, DB_USER, DB_PASS);
    $db->query("SET NAMES 'utf8'");

    function __autoload($class){
        $filename = "../sys/class/class." . $class . ".inc.php";
        if (file_exists($filename)){
            include_once $filename;
        }
    }