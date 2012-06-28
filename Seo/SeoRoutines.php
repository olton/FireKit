<?php
namespace FireKit\Seo;
/**
 * Created by JetBrains PhpStorm.
 * User: Сергей
 * Date: 28.11.11
 * Time: 13:45
 * To change this template use File | Settings | File Templates.
 */
class SeoRoutines {
    /**
     * PageRank Lookup (Based on Google Toolbar for Mozilla Firefox)
     *
     * @copyright   2011 HM2K <hm2k@php.net>
     * @link        http://pagerank.phurix.net/
     * @author      James Wade <hm2k@php.net>
     * @version     $Revision: 2.0 $
     * @require     PHP 4.3.0 (file_get_contents)
     * @updated		06/10/11
     */
    public static function GooglePR($domain,$host='toolbarqueries.google.com',$context=NULL){
        $seed = "Mining PageRank is AGAINST GOOGLE'S TERMS OF SERVICE. Yes, I'm talking to you, scammer.";
        $result = 0x01020345;
        $len = strlen($domain);
        for ($i=0; $i<$len; $i++) {
            $result ^= ord($seed{$i%strlen($seed)}) ^ ord($domain{$i});
            $result = (($result >> 23) & 0x1ff) | $result << 9;
        }
        $ch=sprintf('8%x', $result);
        $url='http://%s/tbr?client=navclient-auto&ch=%s&features=Rank&q=info:%s';
        $url=sprintf($url,$host,$ch,$domain);
        $pr=file_get_contents($url,false,$context);
        return $pr ? (int)substr(strrchr($pr, ':'), 1) : 0;
    }
    
    public static function YandexCy($domain){
        $domain = "http://$domain/";
	    $xml = file_get_contents("http://bar-navig.yandex.ru/u?ver=2&url=$domain&show=1&post=1");
        return $xml ? (int) substr(strstr($xml, 'value="'), 7) : 0;
    }

    public static function AlexaRank($site_url){
        $content = file_get_contents('http://data.alexa.com/data?cli=10&dat=snbamz&url='.urlencode($site_url));
        preg_match('/\<popularity url\="(.*?)" TEXT\="([0-9]+)"\/\>/si', $content, $alexa);
        return isset($alexa[2])?(int)$alexa[2]:0;
    }
}
