<?php
namespace FireKit\Utils;
/**
 * User: olton
 * Date: 04.08.11
 * Time: 16:17
 */
 
class Checker {
    public static function IsMobile($agent) {
        $pattern = '/(blackberry|motorokr|motorola|sony|windows ce|240x320|176x220|palm|mobile|iphone|ipod|symbian|nokia|samsung|midp)/i';
        return (bool)preg_match($pattern, $agent);
    }

    public static function IsCrawler($agent) {
        $pattern = '/(google|yahoo|baidu|bot|webalta|ia_archiver)/';
        return (bool)preg_match($pattern, $agent);
    }
}
