<?php

abstract class disliderSlider
{
    protected $type = '';
    protected $name = '';
    protected $width = '';
    protected $height = '';
    protected static $options = array();
    protected $fields = array();
    protected $js = array();
    protected $css = array();

    public function __construct($width = null, $height = null) {
        $this->width = $width;
        $this->height = $height;
        $this->fields['title'] = 0;
        $this->fields['link'] = 0;
        $this->fields['desc'] = 0;
    }

    public function getSliderOptions(){
        return array(
            'type' => $this->type,
            'name' => $this->name,
            'options' => self::$options[$this->type],
            'fields' => $this->fields
        );
    }

    public function getSliderJS($params = array(), $e_js = array()){
        $new_js = array();
        if(count($this->js)){
            foreach ($this->js as $js){
                if(!in_array($js['file'], $e_js)){
                    $new_js[] = $js['path'].$js['file'];
                }
            }
        }

        return $new_js;
    }

    public function getSliderCSS($params = array(), $e_css = array()){
        $new_css = array();
        if(count($this->css)){
            foreach ($this->css as $css){
                if(!in_array($css['file'], $e_css)){
                    $new_css[] = $css['path'].$css['file'];
                }
            }
        }

        return $new_css;
    }

    public function getIntParams($params = array(), $text_params = array()){
        if(count($params) && count($text_params)){
            //check for text input params - must be int
            foreach ($text_params as $p) {
                if(isset($params[$p])) $params[$p] = max(0, (int) $params[$p]);
                //if 0 or not setted - set default
                if(!isset($params[$p]) || $params[$p] == 0){
                    $params[$p] = isset(self::$options[$this->type][$p]['default']) ? (int) self::$options[$this->type][$p]['default'] : 0;
                }
            }
        }

        return $params;
    }

    abstract protected function getSliderHTML($params = array(), $slides = array());

    abstract protected function getSliderJSCall($params = array());

}