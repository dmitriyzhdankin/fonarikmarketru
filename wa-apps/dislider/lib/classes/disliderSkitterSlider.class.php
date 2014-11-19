<?php

class disliderSkitterSlider extends disliderSlider
{
    private $id = '';
    private $class = '';

    public function __construct($width = null, $height = null) {
        parent::__construct($width, $height);
        $this->type = 'Skitter';
        $this->name = 'Skitter';
        if(!isset(self::$options[$this->type]) || !count(self::$options[$this->type])){
            $path = wa()->getAppPath('lib/config/', 'dislider');
            self::$options[$this->type] = include $path.'skitterType.php';
        }
        $this->fields['title'] = 1;
        $this->fields['link'] = 1;
        $this->js = array(
            array(
                'path' => 'third-party/',
                'file' => 'jquery.easing.1.3.js'
            ),
            array(
                'path' => 'third-party/skitter/',
                'file' => 'jquery.skitter.js'
            )
        );
        $this->css = array(
            array(
                'path' => 'third-party/skitter/',
                'file' => 'skitter.styles.css'
            )
        );
        $this->id = 'dislider-skitterSlider-'.rand();
        $this->class = 'box_skitter';
    }

    public function getSliderHTML($params = array(), $slides = array()){
        $html = array();
        $arrows = $dots = $nums = $thumbs = false;
        $padding = $padding_bottom = '';
        if(count($slides)){
            $params = $this->getIntParams($params, array('interval'));
            if(isset($params['navigation']) && $params['navigation']) $arrows = true;
            if(isset($params['dots']) && $params['dots']) $dots = true;
            if(isset($params['numbers']) && $params['numbers']) $nums = true;
            if(isset($params['thumbs']) && $params['thumbs']) $thumbs = true;
            switch ($params['theme']) {
                case 'minimalist':
                    if($arrows) $padding = 'padding:0 45px;';
                    if($dots) $padding_bottom = 'padding-bottom:40px;';
                    if($thumbs) $padding_bottom = 'padding-bottom:50px;';
                    break;
                case 'round':
                    if($arrows) $padding = 'padding:0 23px;';
                    if($dots) $padding_bottom = 'padding-bottom:40px;';
                    if($thumbs) $padding_bottom = 'padding-bottom:50px;';
                    break;
                case 'clean':
                    if($thumbs) $padding_bottom = 'padding-bottom:50px;';
                    break;
                case 'square':
                    if($dots) $padding_bottom = 'padding-bottom:40px;';
                    if($thumbs) $padding_bottom = 'padding-bottom:50px;';
                    break;
            }
            $html[] = '<div style="width:'.$this->width.'px; height:'.$this->height.'px;'.$padding.$padding_bottom.'">';
            $html[] = '<div id="'.$this->id.'" class="'.$this->class.'" style="width:'.$this->width.'px; height:'.$this->height.'px;">';
            $html[] = '<ul>';
            foreach ($slides as $slide){
                $line = '<img src="'.$slide['url'].'" alt="'.$slide['name'].'.'.$slide['ext'].'" />';
                if(isset($slide['title']) && $slide['title']){
                    $line .= '<div class="label_text">'.$slide['title'].'</div>';
                }
                if(isset($slide['link']) && $slide['link']){
                    $line = '<a href="'.$slide['link'].'">'.$line.'</a>';
                }
                $html[] = '<li>'.$line.'</li>';
            }
            $html[] = '</ul>';
            $html[] = '</div>';
            $html[] = '</div>';
        }else{
            $html[] = 'No Slides found.';
        }

        return $html;
    }

    public function getSliderJSCall($params = array()){
        $html = array();
        $html[] = "<script type='text/javascript' language='javascript'>";
        $html[] = "$(document).ready(function() {";
        $html[] = " $('#".$this->id."').skitter({";
        if(count($params)){
            $params = $this->getIntParams($params, array('interval'));
            foreach ($params as $key=>$val){
                if(!isset(self::$options[$this->type][$key]['unset'])){
                    if($val !== 0){
                        if($val * 1 === 0) $val = "'".$val."'";
                        $html[] = "     ".$key.": ".$val.",";
                    }elseif(isset(self::$options[$this->type][$key]['show_nulls']) && self::$options[$this->type][$key]['show_nulls'] === '1'){
                        $html[] = "     ".$key.": ".$val.",";
                    }
                }
            }
        }
        $html[] = " });";
        $html[] = "});";
        $html[] = "</script>";

        return $html;
    }

}
