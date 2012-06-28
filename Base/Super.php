<?php
namespace FireKit\Base;

/**
 * Description of Super
 *
 * @author Sergey Pimenov
 * @name Super Class
 * @license MIT
 */
abstract class Super {
    protected $items;
    
    public function __set($name, $value) {
        $this->items[$name] = $value;
    }
    
    public function __get($name) {
        return $this->items[$name]?:null;
    }
}

?>
