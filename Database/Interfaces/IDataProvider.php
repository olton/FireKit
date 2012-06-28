<?php
namespace FireKit\Database\Interfaces;
/**
 *
 * @author Sergey Pimenov
 * @name Interface for DataProviders
 */
interface IDataProvider {
    public function Connect();
    public function Disconnect();
    public function Reconnect();
    public function SetCharset($charset = false);
    public function SetSchema($schema = false);
    public function GetSnapshot();
    public function GetQueries();
    public function GetStack();
    
    public function Select($sql);
    public function Update($table, $data, $condition = false);
    public function Delete($table, $condition = false);
    public function Insert($table, $data);
    
    public function Rows($handle = false);
    public function Columns($table);
    public function ID();
    
    public function Fetch($handle = false, $mode = 'ARRAY');
    public function FetchArray($handle = false);
    public function FetchObject($handle = false, $class = false);
    public function FetchAll($handle = false);
    public function FetchResult($handle = false, $row = 0, $field = null);

    public function Transaction($name = '');
    public function Commit($name = '');
    public function Rollback($name = '');
    
    public function CreateObject($object);
    public function DropObject($name);
    public function ReverseObject($name);
    public function UpdateObject($object);
    public function DescribeObject($name);
    public function Escape($value);

    public function ExecProc($name, $params);
    public function ExecFunc($name, $params);
}

?>
