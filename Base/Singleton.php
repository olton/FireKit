<?php
namespace FireKit\Base;
/**
 * User: Сергей Пименов
 * Date: 13.12.11
 * Time: 11:22
 * File: Singleton.php
 */
class Singleton {
    static $instances = Array();

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    static function instance(){
        $class = get_called_class();
         if (!isset(self::$instances[$class])) {
                self::$instances[$class] = new $class();
         }
         return self::$instances[$class];
    }
}
?>