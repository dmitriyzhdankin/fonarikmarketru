<?php

class disliderAnythingSlider extends disliderSlider
{
    private $id = '';
    private $class = '';

    public function __construct($width = null, $height = null) {
        parent::__construct($width, $height);
        $this->type = 'Anything';
        $this->name = 'Anything Slider';
        if(!isset(self::$options[$this->type]) || !count(self::$options[$this->type])){
            $path = wa()->getAppPath('lib/config/', 'dislider');
            self::$options[$this->type] = include $path.'anythingType.php';
        }
        $this->fields['title'] = 1;
        $this->fields['link'] = 1;
        $this->fields['desc'] = 1;
        $this->js = array(
            array(
                'path' => 'third-party/anything/',
                'file' => 'jquery.anythingslider.js'
            ),
            array(
                'path' => 'third-party/anything/',
                'file' => 'ats.callback.js'
            )
        );
        $this->css = array(
            array(
                'path' => 'third-party/anything/',
                'file' => 'anythingslider.css'
            )
        );
        $this->id = 'dislider-anythingSlider-'.rand();
        $this->class = 'atSlider';
    }

    public function getSliderJS($params = array(), $e_js = array()){
        if(isset($params['easing']) && $params['easing'] !== 'linear'){
            $this->js[] = array(
                'path' => 'third-party/',
                'file' => 'jquery.easing.1.3.js'
            );
        }

        return parent::getSliderJS($params, $e_js);
    }

    public function getSliderCSS($params = array(), $e_css = array()){
        $new_css = array();
        $this->css[] = array(
            'path' => 'third-party/anything/',
            'file' => 'theme-'.$params['theme'].'.css'
        );
        foreach ($this->css as $css){
            if(!in_array($css['file'], $e_css)){
                $new_css[] = $css['path'].$css['file'];
            }
        }

        return $new_css;
    }

    public function getSliderHTML($params = array(), $slides = array()){
        $html = array();
        $arrows = $dots = false;
        $width = $this->width;
        $padding = '';
        $description = false;
        if(count($slides)){
            $class = $this->class;
            $params = $this->getIntParams($params, array('delay'));
            if(isset($params['descPosition']) && $params['descPosition'] !== 0){
                $description = true;
                $class .= ' '.$params['descPosition'];
                if($params['descPosition'] == 'desc-top' || $params['descPosition'] == 'desc-bottom'){
                    $desc_style = 'width:'.$this->width.'px;height:auto;';
                }else{
                    $desc_style = 'width:25%;height:'.$this->height.'px;';
                }
            }
            if(isset($params['theme'])){
                if(isset($params['buildArrows']) && $params['buildArrows'] == '1') $arrows = true;
                if(isset($params['buildNavigation']) && $params['buildNavigation'] == '1') $dots = true;
                switch ($params['theme']){
                    case 'metallic': if($arrows) $padding = 'padding:0 23px;'; break;
                    case 'minimalist-round': if($arrows) $padding = 'padding:0 30px;'; break;
                    case 'construction': if($arrows) $padding = 'padding:0 45px;'; break;
                    case 'cs-portfolio': if($arrows || $dots) $padding = 'padding-bottom:0px;'; break;
                }
            }
            $html[] = '<div class="'.$class.'" style="position:relative;width:'.$width.'px;height:'.$this->height.'px;'.$padding.'">';
            $html[] = '<div class="ats-wrapper '.$params['theme'].'" style="width:'.$this->width.'px; height:'.$this->height.'px;">';
            $html[] = '<ul id="'.$this->id.'">';
            foreach ($slides as $slide){
                $desc = '';
                $line = '<img src="'.$slide['url'].'" alt="'.$slide['name'].'.'.$slide['ext'].'" />';
                if(isset($slide['link']) && $slide['link']){
                    $line = '<a href="'.$slide['link'].'">'.$line.'</a>';
                }
                if(isset($slide['title']) && $slide['title']){
                    $desc .= '<div class="label_text">'.$slide['title'].'</div>';
                }
                if(isset($slide['description']) && $slide['description']){
                    $desc .= '<div class="desc_text">'.html_entity_decode($slide['description']).'</div>';
                }
                if($description && $desc != ''){
                    $line .= '<div style="'.$desc_style.'" class="ats-desc-wrapper">'.$desc.'</div>';
                }
                $html[] = '<li>'.$line.'</li>';
            }
            $html[] = '</ul>';
            $html[] = '</div></div>';
        }else{
            $html[] = 'No Slides found.';
        }

        return $html;
    }

    public function getSliderJSCall($params = array()){
        $html = array();
        $html[] = "<script type='text/javascript' language='javascript'>";
        $html[] = "$(document).ready(function() {";
        $html[] = " $('#".$this->id."').anythingSlider({";
        if(count($params)){
            $params = $this->getIntParams($params, array('delay'));
            if(isset($params['autoPlay']) && $params['autoPlay'] == '1'){
                $params = array_merge(array('autoPlayLocked' => '1'), $params);
            }
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
            $html[] = "     onSlideComplete: showDesc,";
            $html[] = "     onSlideBegin: closeDescs,";
            $html[] = "     onInitialized: showDesc,";
        }
        $html[] = " });";
        $html[] = "});";
        $html[] = "</script>";

        return $html;
    }

}
