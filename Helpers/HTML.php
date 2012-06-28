<?php
namespace FireKit\Helpers;
/**
 * User: olton
 * Date: 08.08.11
 * Time: 10:31
 */
 
class HTML {
    public static function DOCTYPE($type = 'HTML5', $subtype = ""){
        $type = strtoupper($type);
        $subtype = strtoupper($subtype);
        switch ($type){
            case 'HTML4': {
                if ($subtype == "STRICT") {
                    $doctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">";
                } elseif ($subtype == "FRAMESET" || $subtype == "FRAME"){
                    $doctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Frameset//EN\" \"http://www.w3.org/TR/html4/frameset.dtd\">";
                } else {
                    $doctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">";
                }
                break;
            }
            case 'XHTML1':
            case 'XHTML1.0':
            case 'XHTML10': {
                if ($subtype == "STRICT") {
                    $doctype = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";
                } elseif ($subtype == "FRAMESET" || $subtype == "FRAME"){
                    $doctype = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
                } else {
                    $doctype = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">";
                }
                break;
            }
            case 'XHTML':
            case 'XHTML11':
            case 'XHTML1.1': {
                $doctype = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.1//EN\">";
                break;
            }
            default: $doctype = "<!DOCTYPE html>";
        }
        return $doctype."\n";
    }

    public static function META_CHARSET($charset = "UTF-8"){
        return "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$charset\">";
    }
}
