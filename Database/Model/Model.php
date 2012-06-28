<?php
namespace FireKit\Database\Model;
/**
 * User: olton
 * Date: 11.07.11
 * Time: 08:50
 */

use FireKit\Base\Super;
use FireKit\Database\Interfaces\IDataProvider;
use FireKit\Database\Factories\DataProviderFactory;
use FireKit\Database\Exceptions\DataProviderException;

abstract class Model extends Super {
    protected $driver;
    protected $page_size = 10;

    public function GetDriver(){
        return $this->driver;
    }

    public function __construct(IDataProvider $driver = null){
        if ($driver) {
            $this->driver = $driver;
        } else {
            //throw new DataProviderException("Provider is not defined", E_USER_ERROR);
            //$this->driver = DataProviderFactory::createProvider("mysql", $GLOBALS['config']['database']);
            $this->driver = $GLOBALS['database']['provider'];
        }
    }

    public function Select($sql){
        return $this->driver->Select($sql);
    }

    public function Update($table, $data, $condition = false){
        return $this->driver->Update($table, $data, $condition);
    }

    public function Insert($table, $data){
        return $this->driver->Insert($table, $data);
    }

    public function Delete($table, $condition = false){
        return $this->driver->Delete($table, $condition);
    }

    public function ExecProc($name, $params){
        return $this->driver->ExecProc($name, $params);
    }

    public function ExecFunc($name, $params){
        return $this->driver->ExecFunc($name, $params);
    }

    public function Rows($handle){
        return $this->driver->Rows($handle);
    }

    public function Columns($table){
        return $this->driver->Columns($table);
    }

    public function ID(){
        return $this->driver->ID();
    }

    public function Fetch($handle = false, $how = 'ARRAY'){
        return $this->driver->Fetch($handle, $how);
    }

    public function FetchArray($handle = false){
        return $this->driver->FetchArray($handle);
    }

    public function FetchObject($handle = false, $class = false){
        return $this->driver->FetchObject($handle, $class);
    }

    public function FetchAll($handle = false){
        return $this->driver->FetchAll($handle);
    }

    public function FetchResult($handle = false, $row = 0, $field = 0){
        return $this->driver->FetchResult($handle, $row, $field);
    }

    public function Transaction($name = ''){
        return $this->driver->Transaction($name);
    }

    public function Commit($name = ''){
        return $this->driver->Commit($name);
    }

    public function Rollback($name){
        return $this->driver->Rollback($name);
    }

    public function CreateObject($object){
        return $this->driver->CreateObject($object);
    }

    public function DropObject($name){
        return $this->driver->DropObject($name);
    }

    public function ReverseObject($name){
        return $this->driver->ReverseObject($name);
    }

    public function UpdateObject($object){
        return $this->driver->UpdateObject($object);
    }

    public function DescribeObject($name){
        return $this->driver->DescribeObject($name);
    }

    public function GetSnapshot(){
        return $this->driver->GetSnapshot();
    }

    public function GetQueries(){
        return $this->driver->GetQueries();
    }

    public function GetStack(){
        return $this->driver->GetStack();
    }

    public function Escape($value){
        return $this->driver->Escape($value);
    }

    public function GetPageSize(){
        return $this->page_size;
    }

    public function SetPageSize($size = 10){
        $this->page_size = $size;
    }

    public function is_value_set($val){
        if (!isset($val) || is_null($val) || strtoupper($val) == 'NULL' || !$val){
            return false;
        } else {
            return true;
        }
    }
}
