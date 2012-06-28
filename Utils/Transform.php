<?php
namespace FireKit\Utils;
/**
 * User: olton
 * Date: 04.08.11
 * Time: 16:17
 */
 
class Transform {
    public static function PhoneNumber($number) {
        $number = trim((string)$number);
        $number = str_replace(array(" ", "(", ")", ".", "-", "+"), "", $number);
        //var_dump($number);
        $n1 = substr($number, -7);
        //var_dump($n1);
        if (strlen($number<=7)){
            $n1 = "380.44" . $n1;
        } else {
            $n2 = substr($number, 0, strlen($number)-7);
            //var_dump($n2);
            if (strlen($n2) < 3) {
                $n1 = "380".$n2.$n1;
            } elseif (strlen($n2) == 3) {
                $n1 = "380.".substr($n2, 1, 2).$n1;
            } elseif (strlen($n2) == 4) {
                $n1 = "380.".substr($n2, 2, 2).$n1;
            } elseif (strlen($n2) == 5) {
                $n1 = "380.".substr($n2, 3, 2).$n1;
            } else {
                $n1 = "380".substr($n1, -9);
            }
        }
        return $n1;
    }

    public static function SmartSubstr($string, $limit, $break=" ", $pad="...") {
        if(strlen($string) <= $limit) return $string;
        $breakpoint = strpos($string, $break, $limit);
        if($breakpoint !== false) {
            if($breakpoint < strlen($string) - 1) {
                $string = substr($string, 0, $breakpoint) . $pad;
            }
        }

        return $string;
    }
}
