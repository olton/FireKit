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
        'name' => 'ttt',
        'structure' => array(
            "id" => array("type"=>"bigint", "options"=>array( "not null", "auto_increment"), "key"=>"primary", "comment"=>"rowid" ),
            "crdate" => array("type"=>"timestamp", "default"=>"CURRENT_TIMESTAMP", "options"=>array("not null")),
            "value" => array("type"=>"varchar", "size"=>"100", "comment"=>"тестовое поле, текстовое")
        ),
        'indexes'=>array(
            "UK"=>array(
                array("name"=>"ttt_name_ui", "fields"=>array("crdate"))
            )
        ),
        "options"=>array(
            "engine"=>"MyISAM",
            "charset"=>"utf8",
            "collate"=>"utf8_general_ci"
        )
    );
    protected $relations = array(
        "users" => array("table"=>"users", "key"=>"id2"),
        "users_temp" => array("table"=>"users_temp")
    );
}

$m = new t($db);
$m->Cols("users.login")->With("users")->Many(false, false, 1);
echo $m->GetTotal()."\n\n\n";
//$m->With("users")->One(1);
//echo $m->DescribeObject()."\n";
//$m->Delete(false, array("users"=>"id2"));

$sql_poll = $m->GetQueries();
echo "Queries poll:\n";
var_dump($sql_poll);
$db->Disconnect();
?>
