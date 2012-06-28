<?php
namespace FireKit\Database\Factories;

/**
 * Description of DatabaseFactory
 *
 * @author Sergey Pimenov
 */
class DataProviderFactory {
    public static function createProvider($type, $config = null){
        if (!$config) {
            $config = $GLOBALS['config']['database'][$type];
        }
        $type = strtoupper($type);
        $provider = null;
        switch ($type) {
            case 'MYSQL': {
                $provider = new \FireKit\Database\Provider\MySQL\MySQLDataProvider($config);
                break;
            }
            case 'MYSQLI': {
                $provider = new \FireKit\Database\Provider\MySQLi\MySQLiDataProvider($config);
                break;
            }
            default: {
                throw new \Exception("Database provider not supported.", E_USER_ERROR);
            }
        }
        return $provider;
    }
}

?>
