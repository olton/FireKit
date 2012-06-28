<?php
namespace FireKit\Database\Exceptions;

/**
 * Description of DataProviderException
 *
 * @author Sergey Pimenov
 */

define('MSG_DATABASE_PROVIDER_CONFIG_EMPTY', 'Configuration options id empty');
define('MSG_DATABASE_PROVIDER_NO_CONNECT', 'Сan not connect to database server');
define('MSG_DATABASE_PROVIDER_SQL_EMPTY', 'SQL is empty');
define('MSG_DATABASE_PROVIDER_SQL_ERROR', 'Query execution stopped with message: ');
define('MSG_DATABASE_PROVIDER_NO_DATA', 'Data for operation is empty');
define('MSG_DATABASE_PROVIDER_NOT_SUPPORTED', 'Mode not supported');
define('MSG_DATABASE_PROVIDER_OBJECT_ERROR', 'Object not exists or structure error');
define('MSG_DATABASE_PROVIDER_KEY_FIELD_NOT_DEFINED', 'Key field not defined');

use \FireKit\Exceptions\FireKitException;

class DataProviderException extends FireKitException{
    
}

?>
