<?php

class disliderRcarouselSlider extends disliderSlider
{
    private $id = '';
    private $class = '';

    public function __construct($width = null, $height = null) {
        parent::__construct($width, $height);
        $this->type = 'Rcarousel';
        $this->name = 'rCarousel';
        if(!isset(self::$options[$this->type]) || !count(self::$options[$this->type])){
            $path = wa()->getAppPath('lib/config/', 'dislider');
            self::$options[$this->type] = include $path.'rcarouselType.php';
        }
        $this->fields['link'] = 1;
        $this->js = array(
            array(
                'path' => '/wa-content/js/jquery-ui/',
                'file' => 'jquery.ui.core.min.js'
            ),
            array(
                'path' => '/wa-content/js/jquery-ui/',
                'file' => 'jquery.ui.widget.min.js'
            ),
            array(
                'path' => 'third-party/rcarousel/',
                'file' => 'rc.callback.js'
            ),
            array(
                'path' => 'third-party/rcarousel/',
                'file' => 'jquery.ui.rcarousel.js'
            )
        );
        $this->css = array(
            array(
                'path' => 'third-party/rcarousel/',
                'file' => 'rcarousel.css'
            )
        );
        $this->id = 'dislider-rcarouselSlider-'.rand();
        $this->class = 'rcarousel';
    }

    // private function correctParams($params = array()){
    //     //check for text input params - must be int
    //     $text_params = array('interval', 'visible', 'step', 'margin');
    //     foreach ($text_params as $p) {
    //         if(isset($params[$p])) $params[$p] = max(0, (int) $params[$p]);
    //         //if 0 or not setted - set default
    //         if(!isset($params[$p]) || $params[$p] == 0) $params[$p] = (int) self::$options[$this->type][$p]['default'];
    //     }

    //     return $params;
    // }

    public function getSliderHTML($params = array(), $slides = array()){
        $html = array();
        $arrows = $dots = false;
        $arrow_style = '';
        $padding = $padding_dots = '';
        $margin = 0;
        $width = $this->width;
        $height = $this->height;
        if(count($slides)){
            $params = $this->getIntParams($params, array('interval', 'visible', 'step', 'margin'));
            if($params['visible'] > 1 && $params['margin'] > 0) $margin = $params['margin'] * ($params['visible'] - 1);
            if(isset($params['navigation']) && $params['navigation']){
                $arrows = true;
                if($params['orientation'] == 'horizontal'){
                    $padding = 'padding:0 50px;';
                    $arrow_style = ' style="height:'.$this->height.'px;"';
                    $width = $this->width * $params['visible'] + $margin;
                    $height = $this->height;
                }else{
                    $padding = 'padding:50px 0;';
                    $arrow_style = ' style="width:'.$this->width.'px;"';
                    $width = $this->width;
                    $height = $this->height * $params['visible'] + $margin;
                }
            }
            if((isset($params['dots']) && $params['dots']) || (isset($params['numbers']) && $params['numbers'])){
                $dots = true;
                if($params['orientation'] == 'horizontal') $padding_dots = 'padding-bottom:25px;';
                else $padding_dots = 'padding-right:25px;';
            }
            $class = $params['main_class'].' '.$params['orientation'];
            $html[] = '<div class="'.$class.'" style="width:'.$width.'px;height:'.$height.'px;'.$padding.$padding_dots.'">';
            if($arrows){
                $html[] = '<a id="'.$this->id.'-prev"'.$arrow_style.' class="rcarousel-prev" href="#"><span>prev</span></a>';
                $html[] = '<a id="'.$this->id.'-next"'.$arrow_style.' class="rcarousel-next" href="#"><span>next</span></a>';
            }
            if($dots){
                $html[] = '<div class="pages"></div>';
            }
            $html[] = '<div id="'.$this->id.'" class="'.$this->class.'" style="width:'.$this->width.'px; height:'.$this->height.'px;">';
            foreach ($slides as $slide){
                $line = '<img src="'.$slide['url'].'" alt="'.$slide['name'].'.'.$slide['ext'].'" />';
                if(isset($slide['link']) && $slide['link']){
                    $line = '<a href="'.$slide['link'].'">'.$line.'</a>';
                }
                $html[] = $line;
            }
            $html[] = '</div></div>';
        }else{
            $html[] = 'No Slides found.';
        }

        return $html;
    }

    public function getSliderJSCall($params = array()){
        $html = array(); $enabled = 'false'; $direction = 'next'; $interval = '5000';
        $html[] = "<script type='text/javascript' language='javascript'>";
        $html[] = "$(document).ready(function() {";
        $html[] = " $('#".$this->id."').rcarousel({";
        if(count($params)){
            $params = $this->getIntParams($params, array('interval', 'visible', 'step', 'margin'));
            $appended = array('main_class', 'direction', 'enabled', 'interval', 'navigation', 'dots', 'numbers');
            foreach ($params as $key=>$val){
                if(!in_array($key, $appended) && !isset(self::$options[$this->type][$key]['unset'])){
                    if($val !== 0){
                        if($val * 1 === 0) $val = "'".$val."'";
                        $html[] = "     ".$key.": ".$val.",";
                    }elseif(isset(self::$options[$this->type][$key]['show_nulls']) && self::$options[$this->type][$key]['show_nulls'] === '1'){
                        $html[] = "     ".$key.": ".$val.",";
                    }
                }
            }
            $html[] = "     width: ".$this->width.",";
            $html[] = "     height: ".$this->height.",";
            if(isset($params['enabled']) && $params['enabled'] == '1'){
                $enabled = 'true';
            }
            if(isset($params['direction']) && $params['direction'] == 'prev'){
                $direction = 'prev';
            }
            if(isset($params['interval']) && ((int) $params['interval'] > 0)){
                $interval = (int) $params['interval'];
            }
            $html[] = "     auto: {enabled: ".$enabled.", direction: '".$direction."', interval: ".$interval."},";
            if(isset($params['navigation']) && $params['navigation'] == '1'){
                $html[] = "     navigation: {next: '#".$this->id."-next', prev: '#".$this->id."-prev'},";
            }
            if(isset($params['dots']) && $params['dots'] == '1'){
                $html[] = "     start: generateDots,";
                $html[] = "     pageLoaded: pageLoaded,";
            }elseif(isset($params['numbers']) && $params['numbers'] == '1'){
                $html[] = "     start: generateNumbs,";
                $html[] = "     pageLoaded: pageLoaded,";
            }
        }
        $html[] = " });";
        $html[] = "});";
        $html[] = "</script>";

        return $html;
    }

}
