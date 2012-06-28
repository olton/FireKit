<?php
namespace FireKit\Database\Model;

/**
 * User: olton
 * Date: 10.07.11
 * Time: 20:03
 */

use FireKit\Database\Model\Model;
use FireKit\Database\Interfaces\IDataProvider;
use FireKit\Database\Exceptions\DataProviderException;
 
class ModelView extends Model {
    protected $view;
    protected $key = null;
    protected $columns;
    protected $counter = 0;
    protected $storage = array();

    public function __construct(IDataProvider $driver = null){
        parent::__construct($driver);
        if (!$this->ObjectExist()) {
            throw new DataProviderException(MSG_DATABASE_PROVIDER_OBJECT_ERROR, E_USER_ERROR);
        }
        $this->columns = parent::Columns($this->view);
        if (!$this->key) {
            $this->key = $this->columns[0];
        }
    }

    public function GetKeyField(){
        return $this->key;
    }

    protected function SetCounter($value){
        $this->counter = $value;
    }

    protected function GetCounter(){
        return $this->counter;
    }

    public function GetTotal(){
        return $this->GetCounter();
    }

    public function ObjectExist($view = false){
        $view = $view ?: $this->view;
        $sql = "desc {$view}";
        try {
            $h = $this->Select($sql);
            if ($this->Rows($h))
                return true;
            else
                return false;
        } catch (\FireKit\Exceptions\FireKitException $e) {
            return false;
        }
    }

    public function Count($sql = false){
        if (!$sql) {
            $sql = "select count(*) from {$this->view}";
        }
        //var_dump($sql);
        $h = $this->Select($sql);
        return $this->FetchResult($h, 0, 0);
    }

    public function One($key_value){
        $row = array();
        $sql = "select * from {$this->view} where {$this->view}.{$this->key} = $key_value";
        //var_dump($sql);
        $h = $this->Select($sql);
        if ($this->Rows($h) == 0) {
            $row = false;
        } else {
            $row = $this->FetchArray($h);
            $columns = $this->columns;
            foreach($columns as $column){
                $this->$column = $row[$column];
            }
            $this->SetCounter(1);
        }
        return $row;
    }

    public function All($order = false){
        $order = "order by " . ($order ? : "1");
        $sql = "select * from {$this->view} $order";
        $h = $this->Select($sql);
        $this->SetCounter($this->Count());
        $this->storage = $this->FetchAll($h);
        return $this->storage;
    }

    protected function _page($page){
        $start = ((!$page) ? 0 : $page)*$this->page_size-$this->page_size;
        $start = ($start<0) ? 0 : $start;
        $limit = $start > 0 ? "limit $start, {$this->page_size}" : "limit {$this->page_size}";
        return $limit;
    }

    public function Many($condition = false, $order = false, $page = 1){
        $condition = "where " . ($condition ?  : "true");
        $order = "order by " . ($order ? : "1");
        $limit = $this->_page($page);
        $sql = "select * from {$this->view} $condition $order $limit";
//var_dump($sql);
        $this->SetCounter($this->Count("select count(*) from {$this->view} $condition"));

        $h = $this->Select($sql);
        if ($this->Rows($h) == 0) {
            return false;
        }
        $this->storage = $this->FetchAll($h);
        return $this->storage;
    }

    public function Find($condition = false, $order = false, $limit = false){
        $condition = "where " . ($condition ?  : "true");
        $order = "order by " . ($order ? : "1");
        $limit = $limit ? "limit {$limit}" : "";

        $sql = "select * from {$this->view} $condition $order $limit";
        //var_dump($sql);

        $this->SetCounter($this->Count("select count(*) from {$this->view} $condition"));

        $h = $this->Select($sql);
        if ($this->Rows($h) == 0) {
            return false;
        }
        $this->storage = $this->FetchAll($h);
        return $this->storage;
    }

    public function FindFirst($condition = false, $order = false){
        $condition = "where " . ($condition ?  : "true");
        $order = "order by " . ($order ? : "1");
        $limit = "limit 1";

        $h = $this->Select("select * from {$this->view} $condition $order $limit");

        $this->SetCounter($this->Count("select count(*) from {$this->view} $condition"));

        if ($this->Rows($h) == 0) {
            return false;
        }
        return $this->FetchArray($h);
    }

    public function FindOne($condition = false, $order = false){
        $condition = "where " . ($condition ?  : "true");
        $order = "order by " . ($order ? : "1");
        $limit = "limit 1";

        $h = $this->Select("select * from {$this->view} $condition $order $limit");

        $this->SetCounter($this->Count("select count(*) from {$this->view} $condition"));

        if ($this->Rows($h) == 0) {
            return false;
        }
        $row = $this->FetchArray($h);
        $columns = $this->Columns($this->view);
        foreach($columns as $column){
            $this->$column = $row[$column];
        }
        return $row;
    }

    /*
     * Функция используется совместно с Find, Many, All
     * Функция извлекает в свойства класса значения полей и смещает указатель на следующую запись локального хранилища
     * Функция однонаправленная
     */
    public function Next(){
        if (empty($this->storage)) return false;
        $current = array_shift($this->storage);
        $columns = $this->Columns($this->view);
        foreach($columns as $col){
            $this->$col = $current[$col];
        }
        return true;
    }

    public function Random($condition = false, $count = 1){
        $condition = "where " . ($condition ?  : "true");
        $order = "order by rand()";
        $limit = "limit $count";

        $h = $this->Select("select * from {$this->view} $condition $order $limit");
        $this->SetCounter($this->Count("select count(*) from {$this->view} $condition"));

        if ($this->Rows($h) == 0) {
            return false;
        }
        $this->storage = $this->FetchArray($h);
        return $this->storage;
    }

    /*
     * Наличие этого метода под вопросом
    public function CheckKey($value){
        $sql = "select count(*) from {$this->view} where {$this->key} = '$value'";
        $h = $this->Select($sql);
        if ($this->FetchResult($h) == 0) {
            return false;
        } else {
            return true;
        }
    }
    */

    public function Check($key, $value){
        $sql = "select count(*) from {$this->view} where $key = " . $this->Escape($value);
        $h = $this->Select($sql);
        if ($this->FetchResult($h) == 0) {
            return false;
        } else {
            return true;
        }
    }
    
}
