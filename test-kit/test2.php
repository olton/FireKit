<?php
include('../bootstrap.php');
$config = array(
    'host' => 'localhost',
    'user' => 'root',
    'password' => 'ghjrcbvf',
    'schema' => 'test',
    'charset' => 'utf8',
    'fetch' => MYSQL_ASSOC
);
$db = FireKit\Factories\DataProviderFactory::createProvider('MYSQL', $config);
$db->Connect();

class t extends FireKit\Database\Model\ModelTable {
    protected $table = array(
        'name' => 'ttt'
    );
}

$m = new t($db);

$re = $m->ReverseObject();
$re['name'] = "yyy";
echo $m->CreateObject($re);

$sql_poll = $m->GetQueries();
echo "Queries poll:\n";
var_dump($sql_poll);
$db->Disconnect();
?>
