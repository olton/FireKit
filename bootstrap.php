<?php namespace FireKit;
define('FIREKIT', 1);
define('FIREKIT_ROOT', dirname(__DIR__)."/");
define('FIREKIT_NULL_VALUE', 'NULL');

function firekit_autoload($name){
    $name = str_replace("\\", "/", $name);
    //var_dump($name);
    if (file_exists(FIREKIT_ROOT . "$name.php")) {
        include_once (FIREKIT_ROOT. "$name.php");
    }
}

spl_autoload_register('\FireKit\firekit_autoload');
?>
