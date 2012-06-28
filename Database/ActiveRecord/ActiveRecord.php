<?php
namespace FireKit\Database\ActiveRecord;
/**
 * User: olton
 * Date: 07.08.11
 * Time: 12:13
 */

use \FireKit\Database\Interfaces\IDataProvider;
use \FireKit\Database\Model\Model;
use \FireKit\Database\Exceptions\DataProviderException;

class ActiveRecord extends Model{
    const NULL_VALUE = "NULL";

    protected $provider;
    protected $table;
    protected $records = array();
    protected $current;
    protected $cols = array();
    protected $default = array();
    protected $key;
    protected $auto_create_row = false;

    public function __construct(IDataProvider $provider){
        parent::__construct($provider);
        $this->cols = $this->Columns($this->table);
        if (empty($this->cols)) throw new DataProviderException(MSG_DATABASE_PROVIDER_OBJECT_ERROR, E_USER_ERROR);
        foreach($this->cols as $col){
            $this->$col = isset($this->default[$col]) ? $this->default[$col] : self::NULL_VALUE;
        }
        if ($this->auto_create_row) $this->Add();
    }

    public function SetKeyField($field_name){
        $this->key = $field_name;
    }

    public function CheckKey($value){
        $sql = "select count(*) from {$this->table} where {$this->key} = '$value'";
        $h = $this->Select($sql);
        if ($this->FetchResult($h) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function Add($values = array()){
        foreach($this->cols as $col){
            $this->$col = isset($this->default[$col]) ? $this->default[$col] : self::NULL_VALUE;
            if (isset($values[$col])) {
                $this->$col = $values[$col];
            }
        }
        $this->Save();
    }

    public function Save(){
        if (!isset($this->key)) $this->SetKeyField($this->cols[0]);
        $data = array();
        foreach($this->cols as $col){
            $data[$col] = $this->$col;
        }
        $is_new = $this->CheckKey($this->{$this->key}) === false;
        if ($is_new) {
            $h = $this->Insert($this->table, $data);
            $this->{$this->key} = $this->ID();
        } else {
            $h = $this->Update($this->table, $data, "{$this->key} = '{$data[$this->key]}'");
        }
        return $this->Rows($h) > 0;
    }

    public function FindByKey($val){
        $sql = "SELECT * FROM {$this->table} WHERE {$this->key} = " . $this->Escape($val);
        $h = $this->Select($sql);
        if ($this->Rows($h) == 0) return false;
        $row = $this->FetchArray($h);
        foreach($this->cols as $col){
            $this->$col = $row[$col];
        }
        return true;
    }

    public function Find($condition){
        $sql = "SELECT * FROM {$this->table} WHERE $condition limit 1";
        $h = $this->Select($sql);
        if ($this->Rows($h) == 0) return false;
        $row = $this->FetchArray($h);
        foreach($this->cols as $col){
            $this->$col = $row[$col];
        }
        return true;
    }

    public function Delete($key_value = false){
        $key_value = $key_value ?: $this->{$this->key};
        $h = parent::Delete($this->table, "{$this->key} = " . $this->Escape($key_value));
        return $this->Rows($h) > 0;
    }

    public function Current(){
        $result = array();
        foreach($this->cols as $col) {
            $result[$col] = $this->$col;
        }
        return $result;
    }
}
