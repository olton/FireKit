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
            "value" => array("type"=>"varchar", "size"=>"100", "comment"=>"тестовое поле, текстовое"),
            "ttt" => array("type"=>"bigint")
        ),
        'indexes'=>array(
            "PK"=>array(
                "fields"=>array("id")
            ),
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
}

$m = new t($db);

echo $m->UpdateObject($m->GetObject())."\n";

$db->Disconnect();
?>
