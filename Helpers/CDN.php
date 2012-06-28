<?php
namespace FireKit\Helpers;
/**
 * User: olton
 * Date: 07.08.11
 * Time: 20:33
 */

class CDN {
    public static function jQuery($ver = "1.7.1"){
        return "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/$ver/jquery.min.js'></script>\n";
    }

    public static function jQueryUI($ver = "1.8.17"){
        return "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jqueryui/$ver/jquery-ui.min.js'></script>\n";
    }

    public static function jQueryUICss($ver = "1.8.17", $theme = "redmond"){
        return "<link rel='Stylesheet' href='http://ajax.aspnetcdn.com/ajax/jquery.ui/$ver/themes/$theme/jquery-ui.css' />\n";
    }

    public static function jQueryValidation($ver = "1.9"){
        return "<script type='text/javascript' src='https://ajax.aspnetcdn.com/ajax/jquery.validate/$ver/jquery.validate.min.js'></script>\n";
    }

    public static function jQueryValidationAdditionalMethods($ver = "1.9"){
        return "<script type='text/javascript' src='https://ajax.aspnetcdn.com/ajax/jquery.validate/$ver/additional-methods.min.js'></script>\n";
    }

    public static function jQueryValidationLocale($ver = "1.9", $locale = "ru"){
        return "<script type='text/javascript' src='https://ajax.aspnetcdn.com/ajax/jquery.validate/$ver/localization/messages_$locale.js'></script>\n";
    }

    public static function jQueryTemplates($ver = "beta1"){
        return "<script type='text/javascript' src='http://ajax.aspnetcdn.com/ajax/jquery.templates/$ver/jquery.tmpl.min.js'></script>\n";
    }

    public static function jQueryTemplatesPlus($ver = "beta1"){
        return "<script type='text/javascript' src='http://ajax.aspnetcdn.com/ajax/jquery.templates/$ver/jquery.tmplPlus.min.js'></script>\n";
    }

    public static function MooTools($ver = "1.4.1"){
        return "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/mootools/$ver/mootools-yui-compressed.js'></script>\n";
    }

    public static function Prototype($ver = "1.7.0.0"){
        return "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/prototype/$ver/prototype.js'></script>\n";
    }

    public static function ScriptAculoUs($ver = "1.9.0"){
        return "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/scriptaculous/$ver/scriptaculous.js'></script>\n";
    }

    public static function SWFObject($ver = "2.2"){
        return "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/swfobject/$ver/swfobject.js'></script>\n";
    }

    public static function YahooUI($ver = "3.3.0"){
        return "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/yui/$ver/build/yui/yui-min.js'></script>\n";
    }

    public static function Dojo($ver = "1.6.1"){
        return "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/dojo/$ver/dojo/dojo.xd.js'></script>\n";
    }

    public static function WebFontLoader($ver = "1.0.22"){
        return "<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/webfont/$ver/webfont.js'></script>\n";
    }

    public static function Less($ver = "1.2.1"){
        return "<script type='text/javascript' src='http://lesscss.googlecode.com/files/less-$ver.min.js'></script>\n";
    }

    public static function HTML5_CssReset($ver = "1.6.1"){
        return "<link rel=\"stylesheet\" type=\"text/css\" href='https://html5resetcss.googlecode.com/files/html5reset-$ver.css' />";
    }

    public static function TwitterCssBootstrap($ver = "1.4.0"){
        return "<link rel=\"stylesheet\" href=\"http://twitter.github.com/bootstrap/$ver/bootstrap.min.css\" />";
    }

    public static function CssMediaQueriesIE(){
        return "<!--[if lt IE 9]><script src=\"http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js\"></script><![endif]-->";
    }
}
