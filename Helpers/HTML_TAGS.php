<?php
namespace FireKit\Helpers;
/**
 * User: Сергей Пименов
 * Date: 24.01.12
 * Time: 12:22
 * File: HTML_TAGS.php
 */

define("PACKAGE_MODEL_BEHAVIOR_PROCEDURAL", 0);
define("PACKAGE_MODEL_BEHAVIOR_FUNCTIONAL", 1);

abstract class HTML_TAGS {
    public $EOL = "\n";
    protected $MODEL = PACKAGE_MODEL_BEHAVIOR_PROCEDURAL;

    private function ExtractAttributes($attributes = array()){
        if (empty($attributes)) return "";
        $result = "";
        foreach($attributes as $attr => $value){
            $result .= "$attr='$value' ";
        }
        return $result;
    }

    private function ExtractEvents($events = array()){
        if (empty($events)) return "";
        $result = "";
        foreach($events as $event => $value){
            $result .= "$event='$value' ";
        }
        return $result;
    }

    public function doctype(){
        $result = "<!DOCTYPE html>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function comment($text = ""){
        $result = "<!-- $text -->";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function a($href ="#", $text = "", $attributes = array(), $events = array()){
        $result = "<a href='$href' {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>$text</a>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function abbr($text = "", $title = "", $attributes = array(), $events = array()){
        $result = "<abbr title='$title' {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>$text</abbr>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function address($text = "", $attributes = array(), $events = array()){
        $result = "<address {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>$text</address>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function article($text = "", $attributes = array(), $events = array()){
        $result = "<article {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>$text</article>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function aside($text = "", $attributes = array(), $events = array()){
        $result = "<aside {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>$text</aside>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    private function ExtractAudioSources($sources = array()){
        if (empty($sources)) return "";
        $result = "";
        foreach($sources as $source){
            $result .= "<source src='{$source['src']}' type='{$source['type']}'/>";
        }
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function audio($sources = array(), $no_support_text = "", $attributes = array(), $events = array()){
        $result = "<audio {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>{$this->ExtractAudioSources($sources)}</audio>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function b($text = "", $attributes = array(), $events = array()){
        $result = "<b {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>$text</b>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function base($href = "/", $target = "_self"){
        $result = "<base href='$href' target='$target' />";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function bdo($text = "", $dir = "ltr", $attributes = array(), $events = array()){
        $result = "<bdo dir='$dir' {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>$text</bdo>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function blockquote($text = "", $cite = "", $attributes = array(), $events = array()){
        $result = "<blockquote cite='$cite' {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>$text</blockquote>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function body($attributes = array(), $events = array()){
        $result = "<body {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function body_end(){
        $result = "</body>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function br($attributes = array(), $events = array()){
        $result = "<br {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)} />";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function button($text = "", $type="button", $attributes = array(), $events = array()){
        $result = "<button type='$type' {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>$text</button>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function canvas($id = "", $attributes = array(), $events = array()){
        $result = "<canvas id='$id' {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}></canvas>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function caption($text = "", $attributes = array(), $events = array()){
        $result = "<caption {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}></caption>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function cite($text = "", $attributes = array(), $events = array()){
        $result = "<cite {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}></cite>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function code($text = "", $attributes = array(), $events = array()){
        $result = "<code {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}></code>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    private function ExtractCols($cols = array()){
        if (empty($cols)) return "";
        $result = "";
        foreach($cols as $col){
            $result .= $col.$this->EOL;
        }
        return $result;
    }

    public function col($attributes = array(), $events = array()){
        $result = "<col {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)} />";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function colgroup($cols = array(), $attributes = array(), $events = array()){
        $result = "<colgroup {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>{$this->ExtractCols($cols)}</colgroup>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function command($text = "", $attributes = array(), $events = array()){
        $result = "<command {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>{$text}</command>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    private function ExtractDatalistOptions($options = array()){
        if (empty($options)) return "";
        $result = "";
        foreach($options as $option){
            $result .= "<option value='$option'>".$this->EOL;
        }
        return $result;
    }

    public function datalist($list = array(), $attributes = array(), $events = array()){
        $result = "<datalist {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}></datalist>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function dd($text = "", $attributes = array(), $events = array()){
        $result = "<dd {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>{$text}</dd>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    /*
     * General Attributes:
     * cite
     * datetime
     */
    public function del($text = "", $attributes = array(), $events = array()){
        $result = "<del {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>{$text}</del>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function detail($summary = "", $text = "", $attributes = array(), $events = array()){
        $result = "<details><summary>{$summary}</summary>{$text}</details>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function dfn($text = "", $attributes = array(), $events = array()){
        $result = "<dfn {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>{$text}</dfn>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function div($text = "", $attributes = array(), $events = array()){
        $result = "<div {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>{$text}</div>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function dl($attributes = array(), $events = array()){
        $result = "<dl {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function dl_end(){
        $result = "</dl>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function dt($text = "", $attributes = array(), $events = array()){
        $result = "<dt {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>{$text}</dt>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function em($text = "", $attributes = array(), $events = array()){
        $result = "<em {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)}>{$text}</em>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function embed($src="", $attributes = array(), $events = array()){
        $result = "<embed src='$src' {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)} />";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function fieldset($legend = "", $attributes = array(), $events = array()){
        $result = "<fieldset {$this->ExtractAttributes($attributes)}{$this->ExtractEvents($events)} ><legend>$legend</legend>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }

    public function fieldset_end(){
        $result = "</fieldset>";
        if ($this->MODEL) return $result . $this->EOL; else echo $result . $this->EOL;
    }


}
