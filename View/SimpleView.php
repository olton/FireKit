<?php
namespace FireKit\View;

/**
 * User: olton
 * Date: 05.08.11
 * Time: 19:29
 */

//use \FireKit\Base\Super;
//use \FireKit\Helpers\CDN;

class SimpleView extends \FireKit\Base\Super{
    protected $path;

    public function __construct($path = ''){
        $this->path = $path;
    }

    public function Render($template, $params = array()){
        extract($params);
        ob_start();
        include($template);
        return ob_get_clean();
     }

    public function Display($template, $params = array(), $echo = false){
        $template = $this->path . $template;
        if (!file_exists($template)) throw new \FireKit\Exceptions\FireKitException("Template not found" . ". File: $template", E_USER_ERROR);
        $result = $this->Render($template, $params);
        if ($echo) {
            echo $result;
        } else {
            return $result;
        }
    }
}
