<?php
namespace FireKit\Database\Provider\MySQLi;

use \FireKit\Base\Super;
use \FireKit\Database\Interfaces\IDataProvider;
use \FireKit\Database\Exceptions\DataProviderException;

$GLOBALS['FIREKIT_MySQL_DATATYPES'] = array(
    'BIT', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'INTEGER', 'BIGINT', 'REAL', 'DOUBLE',
    'FLOAT', 'DECIMAL', 'NUMERIC', 'DATE', 'TIME', 'TIMESTAMP', 'DATETIME', 'YEAR', 'CHAR',
    'VARCHAR', 'BINARY', 'VARBINARY', 'TINYBLOB', 'BLOB', 'MEDIUMBLOB', 'LONGBLOB', 'TINYTEXT',
    'TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'ENUM', 'SET', 'BOOL'
);
/**
 * Description of MySQLDataProvider
 *
 * @author Sergey Pimenov
 */
class MySQLiDataProvider extends Super implements IDataProvider {
    private $queries;
    private $stack;
    private $conn;
    private $query;
    private $snapshot;

    private $host;
    private $user;
    private $password;
    private $schema;
    private $charset;
    private $fetch;
    private $config;
    
    public function __construct($config){
        if (empty ($config)) {
            throw new DataProviderException(MSG_DATABASE_PROVIDER_CONFIG_EMPTY, E_USER_ERROR);
        }
        $this->config = $config;
        $this->host = isset($config['host'])?$config['host']:'localhost';
        $this->user = isset($config['user'])?$config['user']:'root';
        $this->password = isset($config['password'])?$config['password']:'';
        $this->charset = isset($config['charset'])?$config['charset']:'utf8';
        $this->schema = isset($config['schema'])?$config['schema']:'test';
        $this->fetch = isset($config['fetch'])?$config['fetch']:MYSQLI_ASSOC;
        
        $this->Connect();
        $this->SetCharset($this->charset);
        $this->SetSchema($this->schema);
    }
    
    public function Connect(){
        $this->conn = mysqli_connect($this->host, $this->user, $this->password); //TODO add $this->schema
        if ($this->conn === false) {
            throw new DataProviderException(MSG_DATABASE_PROVIDER_NO_CONNECT, E_USER_ERROR);
        }
        
        return $this;
    }

    public function Reconnect(){
        $this->Disconnect();
        $this->Connect();
    }

    public function Disconnect(){
        if ($this->conn){
            mysqli_close($this->conn);
        }
    }
    
    public function SetCharset($charset = false){
        $charset = $charset?:$this->charset;
        if ($this->conn)
            mysqli_set_charset($this->conn, $charset);
    }
    
    public function SetSchema($schema = false){
        $schema = $schema?:$this->schema;
        if ($this->conn)
            mysqli_select_db($this->conn, $schema);
    }

    public function SetFetchMode($mode = MYSQLI_ASSOC){
        $this->fetch = $mode;
    }

    public function Escape($value){
        return $this->_escape($value);
    }

    protected function _escape($value){
        //if ($this->_no_quotes === true) return $value;
        // если magic_quotes_gpc включена - используем stripslashes
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        // Если переменная - число, то экранировать её не нужно
        // если нет - то окружем её кавычками, и экранируем
        if (!is_numeric($value) && (strtoupper($value) !== 'NULL') ) {
            $value = "'" . mysqli_real_escape_string($this->conn, $value) . "'";
        }
        return $value;
    }
    
    private function _execSQL($sql){
        $this->queries[] = $sql;

        //var_dump($sql);
        if (empty ($sql)) {
            throw new DataProviderException(MSG_DATABASE_PROVIDER_SQL_EMPTY, E_USER_ERROR);
        }
        $sql = trim($sql);
        $this->snapshot = $sql;
        $this->query = mysqli_query($this->conn, $sql);

        if ($this->query === false) {
            throw new DataProviderException(MSG_DATABASE_PROVIDER_SQL_ERROR . " [".(mysqli_errno($this->conn))."] " . " $sql " . (mysqli_error($this->conn)), E_USER_ERROR);
        }
        
        $sql_type = strtoupper(substr($sql, 0, strpos($sql, " ")));
        //var_dump($sql_type);
        $rows = ($sql_type == "SELECT") ? mysqli_num_rows($this->query) : mysqli_affected_rows($this->conn);

        $key = md5($sql);

        $this->stack[$key]['rows'] = $rows;
        $this->stack[$key]['sql'] = $sql;
        $this->stack[$key]['type'] = $sql_type;
        return $this->query;
    }
    
    public function Select($sql){
        return $this->_execSQL($sql);
    }
    
    public function Update($table, $data, $condition = false){
        if (empty ($data)) {
            throw new DataProviderException(MSG_DATABASE_PROVIDER_NO_DATA, E_USER_ERROR);
        }
        $arr = array();
        $sql = "update $table set";
        foreach($data as $key=>$value){
            $arr[] = "`$key` = " . $this->_escape($value);
        }
        $sql .= " " . join(", ", $arr);
        if ($condition) {
            $sql .= " where $condition";
        }
        return $this->_execSQL($sql);
    }
    
    public function Delete($table, $condition = false){
        $sql = "delete from $table";
        if ($condition) {
            $sql .= " where $condition";
        }
        return $this->_execSQL($sql);
    }
    
    public function Insert($table, $data){
        if (empty($data)) {
            throw new DataProviderException(MSG_DATABASE_PROVIDER_NO_DATA, E_USER_ERROR);
        }
        $test_key = true;
        $f = array();
        $v = array();
        $sql = "insert into $table";
        foreach($data as $key=>$value){
            if (is_numeric($key)) $test_key = false;
            $f[] = "`$key`";
            $v[] = $this->_escape($value);
        }
        if ($test_key) {
            $sql .= "(".join(", ", $f).")";
        }
        $sql .= " values(".join(", ", $v).")";
        return $this->_execSQL($sql);
    }
    
    public function Rows($handle = false){
        $handler = $handle?:$this->query;
        return ($handler === true) ? mysqli_affected_rows($this->conn) : mysqli_num_rows($handler);
    }

    public function Columns($table){
        $columns = array();
        $current_fetch_mode = $this->fetch;
        $this->SetFetchMode(MYSQLI_ASSOC);
        $sql = "DESC $table";
        $h = $this->Select($sql);
        while($row = $this->FetchArray($h)){
            $columns[] = $row['Field'];
        }
        $this->SetFetchMode($current_fetch_mode);
        return $columns;
    }
    
    public function ID(){
        return $this->conn ? mysqli_insert_id($this->conn) : false;
    }
    
    public function FetchArray($handle = false){
        $handle = $handle?:$this->query;
        return mysqli_fetch_array($handle, $this->fetch);
    }
    
    public function FetchObject($handle = false, $class = false){
        $handle = $handle?:$this->query;
        return mysqli_fetch_object($handle, $class);
    }
    
    public function FetchAll($handle = false){
//        var_dump($this->snapshot, $handle);
        $result = array();
        while($row = $this->FetchArray($handle)){
            $result[] = $row;
        }
        return $result;
    }

    public function FetchResult($handle = false, $row = 0, $field = 0){
        $handle = $handle?:$this->query;
        $current_fetch_mode = $this->fetch;
        $this->SetFetchMode(MYSQLI_NUM);
        $result = $this->FetchAll($handle);
        $this->SetFetchMode($current_fetch_mode);
        $val = isset($result[$row][$field]) ? $result[$row][$field] : 0;
        return $val;
    }
    
    public function Fetch($handle = false, $how = 'ARRAY'){
        $handle = $handle?:$this->query;
        $result = false;
        switch($how){
            case 'ARRAY': {
                $result = $this->FetchArray($handle);
                break;
            }
            case 'OBJECT': {
                $result = $this->FetchObject($handle);
                break;
            }
            case 'ALL': {
                $result = $this->FetchAll($handle);
                break;
            }
            default: {
                throw new DataProviderException(MSG_DATABASE_PROVIDER_NOT_SUPPORTED, E_USER_ERROR);
            }
        }
        return $result;
    }

    public function Transaction($name = ''){
        return $this->Select('START TRANSACTION $name');
    }

    public function Commit($name = ''){
        return $this->Select('COMMIT $name');
    }

    public function Rollback($name = ''){
        return $this->Select('ROLLBACK $name');
    }

    public function ExecProcNoConflict($name, $params){
        $temp_conn = mysqli_connect($this->host, $this->user, $this->password, $this->schema);
        if (!$temp_conn) throw new DataProviderException(MSG_DATABASE_PROVIDER_NO_CONNECT, E_USER_ERROR);
        mysqli_query($temp_conn, "set names {$this->charset}");

        if (!empty($params)) {
            $_p = join(",", $params);
        } else {
            $_p = "";
        }
        $query_call = "call $name($_p);";

        $h = mysqli_query($temp_conn, $query_call);
        $res = array();
        $key = 0;
        while ($row = mysqli_fetch_assoc($h)){
            $res[$key] = $row;
            $key+=1;
        }
        mysqli_close($temp_conn);

        $this->queries[] = $query_call;

        return $res;
    }

    public function ExecFuncNoConflict($name, $params){
        $temp_conn = mysqli_connect($this->host, $this->user, $this->password, $this->schema);
        if (!$temp_conn) throw new DataProviderException(MSG_DATABASE_PROVIDER_NO_CONNECT, E_USER_ERROR);
        mysqli_query($temp_conn, "set names {$this->charset}");

        if (!empty($params)) {
        }

        $query = "select $name(".($params ? join(",", $params) : "").") as result";
        $this->queries[] = $query;
        $h = mysqli_query($temp_conn, $query);
        $result = mysqli_fetch_assoc($h);
        mysqli_close($temp_conn);
        return $result['result'];
    }

    public function ExecProc($name, $params){
        if (!empty($params)) {
            foreach($params as &$par){
                $par = trim($par);
                if ($par[0] == "@") {
                    $_outs[] = $par;
                } else {
                    $par = $this->Escape( $par );
                }
            }
            $_p = join(",", $params);
        } else {
            $_p = "";
        }
        $query_call = "call $name($_p);";
        //echo $query_call;
        $h = $this->Select($query_call);
        if ($h) {
            if (count($_outs) == 0) return true;
            $sql = "select " . join(", ", $_outs);
            $h = $this->Select($sql);
            return $this->FetchArray($h);
        } else {
            return false;
        }
        /*
        $res = $this->FetchAll($h);
        $this->queries[] = $query_call;
        return $res;
        */
    }

    public function ExecFunc($name, $params){
        if (is_array($params)) {
            foreach($params as &$par){
                $par = trim($par);
                $par = $this->Escape( $par );
            }
            $_p = join(",", $params);
        } else {
            $_p = "";
        }
        $query_call = "select $name($_p) as result";
        $this->queries[] = $query_call;
        $h = $this->Select($query_call);
        $result = $this->FetchArray($h);
        return $result['result'];
    }

    /*
     * DDL Fuctions
     * */

    private function _compose_field($name, $struct){
        $f = array();
        $f[] = "`$name`";

        if (isset($struct['type']) && in_array(strtoupper($struct['type']), $GLOBALS['FIREKIT_MySQL_DATATYPES'])) {
            $size = (isset($struct['size'])) ? "({$struct['size']})" : "";
            $f[] = strtoupper($struct['type']).$size;
        } else {
            $f[] = "BIGINT";
        }

        if (isset($struct['values'])) {
            $f[] = " (".join(",", $struct['values']).")";
        } else {
            if (in_array(strtoupper($struct['type']), array('ENUM', 'SET'))){
                $f[] = " (0,1)";
            }
        }

        if (isset($struct['default'])) {
            $f[] = "DEFAULT " . ($struct['default'] == 'CURRENT_TIMESTAMP'?$struct['default']:$this->_escape($struct['default']));
        }

        if (isset($struct['options'])) {
            if (is_array($struct['options'])) {
                $f[] = strtoupper(join(" ", $struct['options']));
            } else {
                $f[] = strtoupper($struct['options']);
            }
        }

        if (isset($struct['comment'])) {
            $f[] = "COMMENT " . $this->_escape($struct['comment']);
        }

        if (isset($struct['column_format'])) {
            $f[] = "COLUMN FORMAT " . strtoupper($struct['column_format']);
        }

        if (isset($struct['storage'])) {
            $f[] = "STORAGE " . strtoupper($struct['storage']);
        }

        if (isset($struct['key'])) {
            if (strtolower($struct['key']) == "primary") {
                $f[] = "PRIMARY KEY";
            } else {
                $f[] = "UNIQUE KEY";
            }
        }

        return join(" ", $f);
    }

    private function _compose_index($index, $struct){
        $i = array();
        $type = strtolower($index);

        switch ($type) {
            case 'pk':
            case 'primary key': {
                $i[] = "PRIMARY KEY (".join(",", $struct['fields']).")";
                break;
            }
            case 'uk':
            case 'unique key':{
                foreach($struct as $is){
                    $i[] = "UNIQUE KEY " . $is['name'] . " (".join(",", $is['fields']).")";
                }
                break;
            }
            case 'fk':
            case 'foreign key':{
                foreach($struct as $is){
                    $i[] = "FOREIGN KEY " . $is['name'] . " (".join(",", $is['fields']).")";
                }
                break;
            }
            case 'rk':
            case 'key':
            case 'regular key':{
                foreach($struct as $is){
                    $i[] = "KEY " . $is['name'] . " (".join(",", $is['fields']).")";
                }
                break;
            }
            case 'tk':
            case 'fulltext key':{
                foreach($struct as $is){
                    $i[] = "FULLTEXT KEY " . $is['name'] . " (".join(",", $is['fields']).")";
                }
                break;
            }
            default:{}
        }

        //return join(", ", $i);
        return $i;
    }

    public function GetCreateDDL($object){
        if (!isset($object['name'])) throw new DataProviderException(MSG_DATABASE_PROVIDER_OBJECT_ERROR, E_USER_ERROR);

        if (isset($object['sql'])) {
            return $this->Select($object['sql']);
        }

        $temp = (isset($object['type']) && strtolower($object['type'])) == 'temp' ? "TEMPORARY " : "";

        if (isset($object['like'])) {
            $sql = "CREATE {$temp}TABLE IF NOT EXISTS {$object['name']} LIKE {$object['like']}";
            return $this->Select($sql);
        }

        if (isset($object['select'])) {
            $sql = "CREATE {$temp}TABLE IF NOT EXISTS {$object['name']} {$object['select']}";
            return $this->Select($sql);
        }

        if (!isset($object['structure']) || empty($object['structure'])) throw new DataProviderException(MSG_DATABASE_PROVIDER_OBJECT_ERROR, E_USER_ERROR);

        $fields = array();
        foreach($object['structure'] as $field=>$struct){
            $fields[] = $this->_compose_field($field, $struct);
        }

        $indexes = array();
        if (isset($object['indexes']) && !empty($object['indexes']))
            foreach($object['indexes'] as $index=>$struct){
                $indexes[] = join(", ", $this->_compose_index($index, $struct));
        }


        $structure = array_merge($fields, $indexes);

        $sql = "CREATE {$temp}TABLE IF NOT EXISTS {$object['name']} (\n";
        $sql .= join(",\n", $structure);
        $sql .= "\n)";

        if (isset($object['options'])) {
            $op = $object['options'];
            if (isset($op['engine'])) {
                $sql .= " ENGINE=".$op['engine'];
            }

            if (isset($op['charset'])) {
                $sql .= " DEFAULT CHARSET=".$op['charset'];
            }

            if (isset($op['collate'])) {
                $sql .= " COLLATE=".$op['collate'];
            }

            if (isset($op['auto_increment']) && is_numeric($op['auto_increment'])){
                $sql .= " AUTO_INCREMENT=".$op['auto_increment'];
            }

            if (isset($op['avg_row_length']) && is_numeric($op['avg_row_length'])){
                $sql .= " AVG_ROW_LENGTH=".$op['avg_row_length'];
            }

            if (isset($op['checksum']) && in_array($op['check_sum'], array(0,1), true)){
                $sql .= " CHECKSUM=".$op['checksum'];
            }

            if (isset($op['comment'])){
                $sql .= " COMMENT=".$this->_escape($op['comment']);
            }

            if (isset($op['connection'])){
                $sql .= " CONNECTION=".$this->_escape($op['connection']);
            }

            if (isset($op['data_directory'])){
                $sql .= " DATA DIRECTORY=".$this->_escape($op['data_directory']);
            }

            if (isset($op['delay_key_write']) && in_array($op['delay_key_write'], array(0,1), true)){
                $sql .= " DELAY_KEY_WRITE=".$op['delay_key_write'];
            }

            if (isset($op['index_directory'])){
                $sql .= " INDEX DIRECTORY=".$this->_escape($op['index_directory']);
            }

            if (isset($op['insert_method']) && in_array($op['insert_method'], array('NO', 'FIRST', 'LAST', 'no', 'first', 'last'), true)){
                $sql .= " INSERT_METHOD=".strtoupper($op['insert_method']);
            }

            if (isset($op['key_block_size']) && is_numeric($op['key_block_size'])){
                $sql .= " KEY_BLOCK_SIZE=".$op['key_block_size'];
            }

            if (isset($op['max_rows']) && is_numeric($op['max_rows'])){
                $sql .= " MAX_ROWS=".$op['max_rows'];
            }

            if (isset($op['min_rows'])){
                $sql .= " MIN_ROWS=".$op['min_rows'];
            }

            if (isset($op['pack_keys']) && in_array($op['pack_keys'], array(0,1,'DEFAULT','default'), true)){
                $sql .= " PACK_KEYS=".strtoupper($op['pack_keys']);
            }

            if (isset($op['password'])){
                $sql .= " PASSWORD=".$this->_escape($op['password']);
            }

            if (isset($op['row_format']) && in_array($op['row_format'], array("DEFAULT","DYNAMIC","FIXED","COMPRESSED","REDUNDANT","COMPACT","default","dynamic","fixed","compressed","redundant","compact"), true)){
                $sql .= " ROW_FORMAT=".strtoupper($op['row_format']);
            }

            if (isset($op['tablespace'])) {
                $sql .= " TABLESPACE " . $op['tablespace']['name'];
                if (isset($op['tablespace']['storage'])){
                    $sql .= " STORAGE " . $op['tablespace']['storage'];
                }
            }

            if (isset($op['union'])) {
                if (is_array($op['union'])) {
                    $sql .= " UNION (".join(",",$op['union']).")";
                } else {
                    $sql .= " UNION ({$op['union']})";
                }
            }
        }
        //var_dump($sql);
        //return $this->Select($sql);
        return $sql;
    }

    public function CreateObject($object){
        $sql = $this->GetCreateDDL($object);
        //var_dump($sql);
        return $this->Select($sql);
    }

    public function DropObject($name){
        $sql = "drop table {$name}";
        return $this->Select($sql);
    }

    private function _decompose_field($field_data){
        $field = array();

        $field_type_a = explode(" ", $field_data['Type']);

        $field_type = false;
        $field_size = false;
        $field_default = false;
        $field_values = false;
        $field_options = false;
        $field_comment = false;
        $field_key = false;
        $match = array();

        preg_match("|\w+|", $field_type_a[0], $match);
        if (isset($match[0]))
            $field_type = strtolower($match[0]);

        if (in_array($field_type, array("enum", "set"))) {
            preg_match("|\((.+)\)|", $field_type_a[0], $match);
            $field_values = explode(",", str_replace(array(")","("), "", $match[0]));
            //var_dump($field_values);
        } else {
            preg_match("|[\d\.]+|", $field_type_a[0], $match);
            if (isset($match[0])) $field_size = $match[0];
        }

        /*
        if ($field_data['Key'] == "PRI") $field_key = "primary";
        if ($field_data['Key'] == "UNI") $field_key = "unique";
        */
        if ($field_data['Null'] == "NO") $field_options[] = "not null";
        if ($field_data['Extra'] == "auto_increment") $field_options[] = "auto_increment";
        if ($field_data['Comment']) $field_comment = $field_data['Comment'];
        if ($field_data['Default']) $field_default = $field_data['Default'];
        if (in_array("unsigned", $field_type_a)) $field_options[] = "unsigned";
        if (in_array("zerofill", $field_type_a)) $field_options[] = "zerofill";
        if (in_array("national", $field_type_a)) $field_options[] = "national";

        $field["type"] = $field_type;
        if ($field_size) $field['size'] = $field_size;
        if ($field_values) $field['values'] = $field_values;
        if ($field_options) $field['options'] = $field_options;
        if ($field_default) $field['default'] = $field_default;
        if ($field_key) $field['key'] = $field_key;
        if ($field_comment) $field['comment'] = $field_comment;

        return $field;
    }

    private function _decompose_index($index_data, &$index_array){
        $key_primary = $index_data['Key_name'] == 'PRIMARY';
        $key_name = $index_data['Key_name'];
        $key_fulltext = $index_data['Index_type'] == 'FULLTEXT';
        $key_unique = $index_data['Non_unique'] == 0;

        if ($key_primary) {
            $index_array['pk']['fields'][] = $index_data['Column_name'];
        } else {
            if ($key_unique) {
                $index_array['uk'][$key_name]['name'] = $key_name;
                $index_array['uk'][$key_name]['fields'][$key_name] = $index_data['Column_name'];
            }
            if (!$key_unique && !$key_fulltext) {
                $index_array['rk'][$key_name]['name'] = $key_name;
                $index_array['rk'][$key_name]['fields'][] = $index_data['Column_name'];
            }
            if ($key_fulltext) {
                $index_array['tk'][$key_name]['name'] = $key_name;
                $index_array['tk'][$key_name]['fields'][] = $index_data['Column_name'];
            }
        }

        return true;
    }

    public function ReverseObject($name){
        $object = array();

        $object['name'] = $name;

        $h = $this->Select("show full columns from $name");
        while ($col = $this->FetchArray($h)) {
            $field = $this->_decompose_field($col);
            $object['structure'][$col['Field']] = $field;
        }


        $h = $this->Select("show indexes from $name");
        if ($this->Rows($h)) {
            $object['indexes'] = array();
            while($ind = $this->FetchArray($h)){
                $this->_decompose_index($ind, $object['indexes']);
            }
        }

        $sql = "show table status from {$this->schema} where Name = '$name'";
        $h = $this->Select($sql);
        $tab = $this->FetchArray($h);

        $object['options']['engine'] = $tab['Engine'];
        $object['options']['auto_increment'] = $tab['Auto_increment'];
        $object['options']['avg_row_length'] = $tab['Avg_row_length'];
        $object['options']['charset'] = substr($tab['Collation'], 0, strpos($tab['Collation'], "_"));
        $object['options']['checksum'] = $tab['Checksum'];
        $object['options']['collate'] = $tab['Collation'];
        $object['options']['comment'] = $tab['Comment'];
        $object['options']['row_format'] = $tab['Row_format'];

        return $object;
    }

    public function GetUpdateDDL($object){
        $current = $this->ReverseObject($object['name']);
        $new = $object;
        $changes = array();

        $sql = "ALTER TABLE {$object['name']}\n";
        /* Сравнение полей */
        /* Добавляем или изменяем поля */
        foreach($new['structure'] as $field=>$struct){
            if (!array_key_exists($field, $current['structure'])) {
                $changes[] = "ADD COLUMN " . $this->_compose_field($field, $struct) . "\n";
                continue;
            }
            $cur_struct = $current['structure'][$field];
            if (isset($struct['type']) && $struct['type'] != $cur_struct['type']) {
                $changes[] = "MODIFY COLUMN " . $this->_compose_field($field, $struct) . "\n";
                continue;
            }
        }
        /* Удаляем поля */
        foreach($current['structure'] as $field=>$struct){
            if (!array_key_exists($field, $new['structure'])) {
                $changes[] = "DROP COLUMN " . $field . "\n";
                continue;
            }
        }

        /* Обновление индексов */
        /* Удаление текущих индексов */
        if ($current['indexes']) foreach($current['indexes'] as $type=>$struct){
            if ($type == 'pk') {
                $changes[] = "DROP PRIMARY KEY \n";
            } else {
                foreach($struct as $key=>$str){
                    $changes[] = "DROP INDEX " . $str['name'] . "\n";
                }
            }
        }

        /* Создание новых индексов */
        if ($new['indexes']) foreach($new['indexes'] as $type=>$struct){
            $ind = $this->_compose_index($type, $struct);
            foreach($ind as $idx){
                $changes[] = "ADD " . $idx;
            }

        }

        return $sql . join(", ", $changes);
    }

    public function UpdateObject($object){
        $sql = $this->GetUpdateDDL($object);
        return $this->Select($sql);
    }

    public function DescribeObject($name){
        $sql = "SHOW CREATE TABLE {$name}";
        $h = $this->Select($sql);
        if ($this->Rows($h) == 0) return false;
        $row = $this->FetchArray($h);
        return $row['Create Table'];
    }

    public function GetSnapshot(){
        return $this->snapshot;
    }
    
    public function GetQueries(){
        return $this->queries;
    }
    
    public function GetStack(){
        return $this->stack;
    }
}

?>
