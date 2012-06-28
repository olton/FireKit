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

class ModelTable extends Model {
    protected $table;
    protected $key;
    protected $columns = array();

    protected $storage = array();

    const HAS_ONE = 0;
    const HAS_MANY = 1;

    const CROSS_JOIN = 'CROSS JOIN';
    const INNER_JOIN = 'INNER JOIN';
    const LEFT_JOIN = 'LEFT JOIN';
    const RIGHT_JOIN = 'RIGHT JOIN';


    /*
     * relationName =>
     * array(
     *   'table'=>'table_name',
     *   'key' => 'field_name',
     *   'prefix' => 'field_prefix',
     *   'join' => self::JOIN_x
     * )
     *
     * */
    protected $relations = array();
    protected $useRelations = "ALL";
    protected $useCols = "ALL";

    protected $counter = 0;

    protected function SetCounter($value){
        $this->counter = $value;
    }

    protected function GetCounter(){
        return $this->counter;
    }

    public function GetTotal(){
        return $this->GetCounter();
    }

    public function GetObjectName(){
        return $this->table['name'];
    }

    public function GetObject(){
        return $this->table;
    }

    public function Columns($table = false){
        if ((!$table || $table == $this->table['name']) && !empty($this->columns)) {
            //var_dump(".\r\n");
            return $this->columns;
        }

        $columns = array();

        if (!$table && !isset($this->table['structure'])){
            throw new DataProviderException(MSG_DATABASE_PROVIDER_OBJECT_ERROR . " 1:". $this->table['name'], E_USER_ERROR);
        }

        if ($table){
            $columns = parent::Columns($table);
            return $columns;
        } else {
            if (!isset($this->table['structure'])){
                throw new DataProviderException(MSG_DATABASE_PROVIDER_OBJECT_ERROR . " 2:". $this->table['name'], E_USER_ERROR);
            }
            foreach($this->table['structure'] as $key=>$value){
                $columns[] = $key;
            }
            $this->columns = $columns;
            return $this->columns;
        }
        //return $columns;
    }

    public function CreateObject($object = false){
        $object = $object ?:$this->table;
        return parent::CreateObject($object);
    }

    public function DropObject($name = false){
        $name = $name ?: $this->table['name'];
        return parent::DropObject($name);
    }

    public function ReverseObject($name = false){
        $name = $name ?: $this->table['name'];
        return parent::ReverseObject($name);
    }

    public function UpdateObject($object = false){
        $object = $object ?: $this->table;
        return parent::UpdateObject($object);
    }

    public function DescribeObject($object = false){
        $object = $object ?: $this->table;
        return parent::DescribeObject($object);
    }

    public function __construct(IDataProvider $driver = null){
        parent::__construct($driver);

        if ($this->ObjectExist() == false) {
            if (!isset($this->table['auto_create']) || $this->table['auto_create'] == 1) {
                $this->CreateObject();
            }
        } else {
            if (defined('DB_DEVELOP_STATE') || isset($this->table['check_structure'])){
                $this->UpdateObject();
            }
        }

        $this->columns = $this->Columns($this->table['name']);

        if (!isset($this->key) || !$this->key) {
            if (isset($this->table['structure']) && !empty($this->table['structure'])){
                $keys = array_keys($this->table['structure']);
                $this->key = $keys[0];
            } else {
                if ($this->ObjectExist()) {
                    //$columns = $this->Columns($this->table['name']);
                    $this->key = $this->columns[0];
                } else {
                    throw new DataProviderException(MSG_DATABASE_PROVIDER_KEY_FIELD_NOT_DEFINED, E_USER_ERROR);
                }
            }
        }
    }

    public function Register(){
        return $this->CreateObject();
    }

    public function Unregister(){
        return $this->DropObject();
    }

    public function Clear($cascade = array()){
        return $this->Delete(false, $cascade);
    }

    public function Flush(){
        $columns = $this->Columns($this->table['name']);
        foreach($columns as $column) {
            $this->$column = null;
        }
    }

    public function ObjectExist($table = false){
        $table = $table ?: $this->table['name'];
        $sql = "desc {$table}";
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

    public function With(){
        $args_count = func_num_args();
        if ($args_count == 0) {
            $this->useRelations = "ALL";
        } else {
            $this->useRelations = array();
            for($i=0;$i<$args_count;$i++){
                $this->useRelations[] = func_get_arg($i);
            }
        }
        return $this;
    }

    public function JoinWith(){
        $args_count = func_num_args();
        if ($args_count == 0) {
            $this->useRelations = "ALL";
        } else {
            $this->useRelations = array();
            for($i=0;$i<$args_count;$i++){
                $this->useRelations[] = func_get_arg($i);
            }
        }
        return $this;
    }

    public function Cols(){
        $args = func_get_args();
        $args_count = func_num_args();
        if ($args_count == 0 || func_get_arg(0) === false) {
            $this->useCols = "ALL";
        } else {
            $this->useCols = array();
            if (!in_array("*", $args)) $this->useCols[] = $this->table['name'].".*";
            for($i=0;$i<$args_count;$i++){
                $col = func_get_arg($i);
                if (strstr($col, ".") === false) $col = $this->table['name'].".".$col;
                $this->useCols[] = $col;
            }

        }
        return $this;
    }

    protected function GetCols(){
        return $this->useCols == "ALL" ? "*" : join(", ", $this->useCols);
    }

    protected function GetJoin(){
        $join_rules = array();
        if (!empty($this->relations)) {
            $table_index = 1;
            foreach($this->relations as $key=>$value){
                if ($this->useRelations != "ALL" && !in_array($key, $this->useRelations)) continue;
                if (!isset($value['join'])) $value['join'] = self::INNER_JOIN;
                if (!isset($value['key'])) $value['key'] = $this->key;
                $_key = $this->key;
                $join_rules[] = "{$value['join']} {$value['table']} on {$value['table']}.{$value['key']} = {$this->table['name']}.{$value['fkey']}";
                $table_index += 1;
            }
        }
        return join(" ", $join_rules);
    }

    /*
     * Функция предназначяена для получения кол-ва записей
     * По умолчанию считается кол-во записей в текущей таблице, но можно передать селект
     */
    public function Count($sql = false){
        if (!$sql) {
            $sql = "select count(*) from {$this->table['name']}";
        }
        //var_dump($sql);
        $h = $this->Select($sql);
        return $this->FetchResult($h, 0, 0);
    }

    /*
     * Функция для внутреннего использования и предназначена для формирования (заполнения) ключевых полей
     */
    private function _composeKeyVal($key_values){
        if (is_array($key_values)) {
            foreach($key_values as &$val){
                $val = $this->Escape($val);
            }
        } else {
            $key_values = $this->Escape($key_values);
        }
        $key_val = "";
        if (!is_array($this->key)){
            if (is_array($key_values)) {
                $key_val = "{$this->table['name']}.{$this->key} in (".(explode(", ", $key_values)).")";
            } else {
                $key_val = "{$this->table['name']}.{$this->key} = {$key_values}";
            }
        } elseif (is_array($this->key) && !is_array($key_values)){
            $key_effective = $this->key[0];
            $key_val = "{$this->table['name']}.{$key_effective} = {$key_values}";
        } elseif (is_array($this->key) && is_array($key_values)){
            $key_effective = array();
            for($i=0;$i<count($this->key);$i++){
                $key_effective[] = $this->key[$i]." = ".$key_values[$i];
            }
            $key_val = "(".(join(" and ", $key_effective)).")";
        } else {
            $key_val = "true";
        }
        return $key_val;
    }

    /*
     * Функция предназаняена для выборки записи по ключу
     * В случае успешного выполнения выбранные данные переносятся в свойства класса
     */
    public function One($key_values, $create = false){
        //TODO Добавить автоматическое создание записи по ключу, если ничего не найдено и указан флаг $create
        $this->SetCounter(0);
        $row = array();
        $this->Flush();
        $cols = $this->GetCols();
        $join = $this->GetJoin();

        $key_val = $this->_composeKeyVal($key_values);

        $sql = "select $cols from {$this->table['name']} $join where $key_val limit 1";

        //var_dump($sql);


        $h = $this->Select($sql);
        if ($this->Rows($h) == 0) {
            $row = false;
            //$key = $this->key;
            //$this->$key = $key_value;
        } else {
            $row = $this->FetchArray($h);
            $columns = $this->Columns($this->table['name']);
            foreach($columns as $column){
                $this->$column = $row[$column];
            }
            $this->SetCounter(1);
        }
        return $row;
    }

    /*
     * Функция предназначена для выборки всех данных из таблицы в определенном порядке.
     * Усли не указан порядок сортировки сортировка осуществляется по первому полю запроса
     */
    public function All($order = false){
        $order = "order by " . ($order ? : "1");
        $cols = $this->GetCols();
        $join = $this->GetJoin();
        $sql = "select $cols from {$this->table['name']} $join $order";
        $h = $this->Select($sql);
        $this->SetCounter($this->Count());
        $this->storage = $this->FetchAll($h);
        return $this->storage;
    }

    /*
     * Функция предназначена для внутреннего использования и занимается вормированием значения limit для постраничных запросов
     */
    protected function _page($page){
        $start = ((!$page) ? 0 : $page)*$this->page_size-$this->page_size;
        $start = ($start<0) ? 0 : $start;
        $limit = $start > 0 ? "limit $start, {$this->page_size}" : "limit {$this->page_size}";
        return $limit;
    }

    /*
     * Функция предназначена для постраничной выборки данных из таблицы
     * можно задать дополнительное условие, порядок сортировки, и номер страницы
     * кол-во записей на странице регулируется методом SetPageSize
     */
    public function Many($condition = false, $order = false, $page = 1){
        $condition = "where " . ($condition ?  : "true");
        $order = "order by " . ($order ? : "1");
        $limit = $this->_page($page);
        $cols = $this->GetCols();
        $join = $this->GetJoin();
        $sql = "select $cols from {$this->table['name']} $join $condition $order $limit";

        $this->SetCounter($this->Count("select count(*) from {$this->table['name']} $condition"));

        $h = $this->Select($sql);
        if ($this->Rows($h) == 0) {
            return false;
        }
        $this->storage = $this->FetchAll($h);
        return $this->storage;
    }

    /*
     * Функция ищет данные по условию, может быть задан порядок сортировки и кол-во возвращаемых данных
     * Функция возвращает массив или false
     */
    public function Find($condition = false, $order = false, $limit = false){
        $condition = "where " . ($condition ?  : "true");
        $order = "order by " . ($order ? : "1");
        $limit = $limit ? "limit {$limit}" : "";
        $cols = $this->GetCols();
        $join = $this->GetJoin();

        $this->SetCounter($this->Count("select count(*) from {$this->table['name']} $condition"));
        $sql = "select $cols from {$this->table['name']} $join $condition $order $limit";
        //var_dump($sql);
        $h = $this->Select($sql);
        if ($this->Rows($h) == 0) {
            return false;
        }
        $this->storage = $this->FetchAll($h);
        return $this->storage;
    }

    /*
     * Функция используется совместно с Find, Many, All
     * Функция извлекает в свойства класса значения полей и смещает указатель на следующую запись локального хранилища
     * Функция однонаправленная
     */
    public function Next(){
        if (empty($this->storage)) return false;
        $current = array_shift($this->storage);
        $columns = $this->Columns($this->table['name']);
        foreach($columns as $col){
            $this->$col = $current[$col];
        }
        return true;
    }

    public function FindFirst($condition = false, $order = false){
        $condition = "where " . ($condition ?  : "true");
        $order = "order by " . ($order ? : "1");
        $limit = "limit 1";
        $cols = $this->GetCols();
        $join = $this->GetJoin();

        $this->SetCounter($this->Count("select count(*) from {$this->table['name']} $condition"));

        $h = $this->Select("select $cols from {$this->table['name']} $join $condition $order $limit");

        if ($this->Rows($h) == 0) {
            return false;
        }
        return $this->FetchArray($h);
    }

    public function FindOne($condition = false, $order = false){
        $condition = "where " . ($condition ?  : "true");
        $order = "order by " . ($order ? : "1");
        $limit = "limit 1";
        $cols = $this->GetCols();
        $join = $this->GetJoin();

        $this->SetCounter($this->Count("select count(*) from {$this->table['name']} $condition"));

        $h = $this->Select("select $cols from {$this->table['name']} $join $condition $order $limit");

        if ($this->Rows($h) == 0) {
            return false;
        }
        $row = $this->FetchArray($h);
        $columns = $this->Columns($this->table['name']);
        foreach($columns as $column){
            $this->$column = $row[$column];
        }
        return $row;
    }

    public function Random($condition = false, $count = 1){
        $condition = "where " . ($condition ?  : "true");
        $order = "order by rand()";
        $limit = "limit $count";
        $cols = $this->GetCols();
        $join = $this->GetJoin();

        $this->SetCounter($this->Count("select count(*) from {$this->table['name']} $condition"));

        $h = $this->Select("select $cols from {$this->table['name']} $join $condition $order $limit");

        if ($this->Rows($h) == 0) {
            return false;
        }
        return $this->FetchAll($h);
    }

    public function CheckKey($values = false){
        if (!$values){
            if (is_array($this->key)) {
                $values = array();
                foreach($this->key as $key){
                    $values[] = $this->$key;
                }
            } else {
                $key = $this->key;
                $values = $this->$key;
            }
        }

        $key_val = $this->_composeKeyVal($values);

        //var_dump($key_val);

        // TODO Добавить проверку композитного ключа
        $sql = "select count(*) from {$this->table['name']} where $key_val";
        //var_dump($sql);
        $h = $this->Select($sql);
        if ($this->FetchResult($h, 0, 0) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function Check($key, $value){
        $sql = "select count(*) from {$this->table['name']} where $key = " . $this->Escape($value);
        $h = $this->Select($sql);
        if ($this->FetchResult($h, 0, 0) == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function Save(){
        //$key = $this->key;
        //var_dump($key . " = " . $this->$key);
        $new = !$this->CheckKey();
        $columns = $this->Columns($this->table['name']);
        $data = array();
        foreach($columns as $column) {
            $data[$column] = $this->$column;
        }
        //var_dump($data);
        if ($new) {
            $h = $this->Insert($this->table['name'], $data);
            $new_id = $this->ID();
            if ($new_id) {
                if (!is_array($this->key)){
                    $this->{$this->key} = $new_id;
                } else {
                    //TODO Добавить запись ID если композитный-ключ и сгенерировн новый ID
                    $this->{$this->key[0]} = $new_id;
                }
            }
        } else {
            if (is_array($this->key)) {
                $key_val_a = array();
                foreach($this->key as $key){
                    $key_val_a[] = "$key = '$data[$key]'";
                }
                $key_val = implode(" and ", $key_val_a);
            } else {
                $key_val = "{$this->key} = '{$data[$this->key]}'";
            }
            $h = $this->Update($this->table['name'], $data, $key_val);
        }
        //$this->One();
        return $this->Rows($h);
    }

    /*
     * $cascade задает массив пар таблица-поле для каскадного удаления
     *
     *
     * */
    public function Delete($table = false, $condition = false, $cascade = array()){
        $table = $table ?: $this->table['name'];
        if (!empty($cascade)) {
            $to_delete = array();
            $condition = $condition ?: "1=1";
            $sql = "select {$this->key} from $table where $condition";
            $h = $this->Select($sql);
            if ($this->Rows($h)>0){
                while($row = $this->FetchArray($h)){
                    //$to_delete[] = $row[$this->key];
                    foreach($cascade as $tab=>$key){
                        $sql = "delete from {$tab} where {$key}={$row[$this->key]}";
                        $this->Select($sql);
                    }
                }
            }
        }
        return parent::Delete($table, $condition);
    }

    public function Store($data = array()){
        if (empty($data)) return false;
        $columns = $this->Columns($this->table['name']);

        // TODO: Добавить данные которых нет во входном массиве и они есть в таблице
        //var_dump($data);
        foreach($columns as $key){
            $this->$key = isset($data[$key]) ? $data[$key] : "NULL";
        }
        try {
            $this->Save();
            return true;
        } catch (\Exception $e){
            //var_dump($e->getMessage());
            return false;
        }
    }

    public function CountRows($condition = false){
        $condition = "where " . ($condition ?  : "true");
        $h = $this->Select("select count(*) from {$this->table['name']} $condition");
        return $this->FetchResult($h, 0, 0);
    }
}

// TODO "Конфликт имен столбцов в реляционных связях"