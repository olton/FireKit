<?php
namespace FireKit\Controller;
/**
 * Created by JetBrains PhpStorm.
 * User: Sergey Pimenov
 * Date: 08.08.11
 * Time: 14:16
 * To change this template use File | Settings | File Commands.
 */

use \FireKit\Base\Super;

abstract class Controller extends Super{
    protected $name;
    protected $desc;

    public function GetControllerName(){
        return $this->name;
    }

    public function GetControllerDescription(){
        return $this->desc;
    }
}
