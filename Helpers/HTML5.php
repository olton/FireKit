<?php
namespace FireKit\Helpers;
/**
 * User: olton
 * Date: 08.08.11
 * Time: 10:31
 */
 
class HTML5 {
    public static function DOCTYPE(){
        return "<!DOCTYPE html>\n";
    }

    public static function META($name = "", $content = ""){
        return "<meta name='$name' content='$content' />\n";
    }

    public static function META_CHARSET($charset = "UTF-8"){
        return "<meta charset=\"$charset\" />\n";
    }

    public static function FixHTML5ForIE(){
        return "
        <!--[if lt IE 9]>\n
            <script src=\"http://html5shiv.googlecode.com/svn/trunk/html5.js\"></script>\n
        <![endif]-->\n
        ";
    }
}
