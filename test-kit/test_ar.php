<?php
include('../bootstrap.php');
$config = array(
    'host' => 'localhost',
    'user' => 'root',
    'password' => 'ghjrcbvf',
    'schema' => 'test',
    'charset' => 'utf8',
    'fetch' => MYSQLI_ASSOC
);

use \FireKit\Database\ActiveRecord\ActiveRecord;
use \FireKit\Factories\DataProviderFactory;

$db = DataProviderFactory::createProvider('MYSQL', $config);
$db->Connect();


class r extends ActiveRecord {
    protected $table = "ttt";
    protected $key = "id";
}

$record = new r($db);
//$record->Add();
$record->id = 1;
$record->value = "test";
$record->Save();


$sql_poll = $record->GetQueries();
echo "Queries poll:\n";
var_dump($sql_poll);
$db->Disconnect();
?>
