<?php
namespace FireKit\Utils;
/**
 * User: olton
 * Date: 04.08.11
 * Time: 16:17
 */
 
class Security {
    public static function XSS($data, $quotes = ENT_NOQUOTES){
        if (is_array($data)) {
            $escaped = array();
            foreach ($data as $key => $value) {
                $escaped[$key] = self::XSS($value);
            }
            return $escaped;
        }
        $data = self::ClearQuotes($data);
        return htmlspecialchars($data, $quotes);
    }

    public static function FilterData($data){
        if(is_array($data))
            $data = array_map(__FUNCTION__, $data);
        else{
            $data = trim(htmlentities(strip_tags($data)));
            if (get_magic_quotes_gpc())
                $data = stripslashes($data);
            $data = mysql_real_escape_string($data);
        }
        return $data;
    }

    public static function ClearQuotes($data, $replace = ""){
        if (is_array($data)) {
            $escaped = array();
            foreach ($data as $key => $value) {
                $escaped[$key] = self::ClearQuotes($value);
            }
            return $escaped;
        }
        return str_replace(array('"', "'"), "", $data);
    }

    public static function Password($len = 12, $custom_chars = false){
        $chars = $custom_chars?: 'QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm!@#$%^&*()_+-=1234567890';
        $password = "";
        for($i = 0; $i<$len; $i++){
            $rnd = rand(0, strlen($chars)-1);
            $password .= $chars[$rnd];
        }
        return $password;
    }
}
