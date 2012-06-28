<?php
/**
 * User: Сергей Пименов
 * Date: 11.01.12
 * Time: 13:00
 * File: model.table.test.php
 */


define("UT_ROOT", dirname(dirname(dirname(dirname(__FILE__)))) . "/");
include(UT_ROOT . "bootstrap.php");


$GLOBALS['config']['database']['mysqli'] = array(
    'host' => 'localhost',
    'user' => 'root',
    'password' => 'ghjrcbvf',
    'schema' => 'test',
    'charset' => 'utf8',
    'fetch' => MYSQL_ASSOC
);
$GLOBALS['database']['provider'] = \FireKit\Database\Factories\DataProviderFactory::createProvider("mysqli");

class myTable extends \FireKit\Database\Model\ModelTable{
    protected $table = array(
        "name" => "domains"
    );
}

$table_m = new myTable();

// Test Find and Next

$table_m->Find("domain_user = 3");
while($table_m->Next()){
    echo $table_m->domain_name . "\r\n";
}

?>