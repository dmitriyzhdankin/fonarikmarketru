<?php

class disliderViewHelper
{

    /**
     * Method to preload Sliders CSS & JS files into <head> tag,
     * if Slider must be shown after when system methods $wa->css() & $wa->js() will be called.
     * For example, if Slider called in index.html theme file.
     *
     * IMPORTANT! This method must be called in <head> tag, BEFORE $wa->css() & $wa->js() call.
     *
     * @param string $ids - all Sliders ids (comma separated) called by this way.
     *
     * @return nothing.
     *
     * @example
     * <head>
     *  <title>{$wa->title()|escape}</title>
     *
     *  <!-- HERE WE CALL THIS METHOD, TO PRELOAD SLIDERS FILES -->
     *  {$wa->dislider->preloadFiles('4,7,12,25')}
     *
     *  <!-- css -->
     *  <link href="{$wa_theme_url}default.css?{$wa->version()}" rel="stylesheet" type="text/css"/>
     *  {$wa->css()} {* links active plugins CSS *}
     *  <!-- js -->
     *  <script type="text/javascript" src="{$wa_url}wa-content/js/jquery/jquery-1.8.2.min.js"></script>
     *  {$wa->js()} {* links active plugins JS *}
     * </head>
     *
     * <body>
     *
     *  <!-- HERE WE SHOW OUR SLIDERS -->
     *  {$wa->dislider->showSlider('4')}
     *  {$wa->dislider->showSlider('7')}
     *  {$wa->dislider->showSlider('12')}
     *  {$wa->dislider->showSlider('25')}
     *
     * </body>
     */

    public function preloadFiles($ids = null){
        if($ids){
            $ids = explode(',', $ids);
            if(count($ids)){
                //get sliders
                foreach ($ids as &$id){
                    $id = (int) $id;
                }
                $s_model = new disliderSlidersModel();
                $sliders = $s_model->getById($ids);

                if(count($sliders)){
                    //get already loaded <head> files
                    $e_css = $e_js = array();
                    $header = waSystem::getInstance()->getResponse();
                    $exist_css = $header->getCss(false);
                    if(count($exist_css)){
                        foreach ($exist_css as $ec){
                            $e_css[] = $this->parseName($ec);
                        }
                    }
                    $exist_js = $header->getJs(false);
                    if(count($exist_js)){
                        foreach ($exist_js as $ej){
                            $e_js[] = $this->parseName($ej);
                        }
                    }

                    //get sliders files array
                    $files = array('css' => array(), 'js' => array());
                    foreach ($sliders as $slider){
                        $dislider_class = 'dislider'.$slider['itype'].'Slider';
                        if(class_exists($dislider_class)){
                            $params = json_decode($slider['params'], true);
                            $slider = new $dislider_class();
                            $css = $slider->getSliderCSS($params, $e_css);
                            $js = $slider->getSliderJS($params, $e_js);
                            if(count($css)){
                                $files['css'] = array_merge($files['css'], $css);
                                foreach($css as &$_css){
                                        $file = explode('/', $_css);
                                        $_css = array_pop($file);
                                }
                                $e_css = array_merge($e_css, $css);
                            }
                            if(count($js)){
                                $files['js'] = array_merge($files['js'], $js);
                                foreach($js as &$_js){
                                        $file = explode('/', $_js);
                                        $_js = array_pop($file);
                                }
                                $e_js = array_merge($e_js, $js);
                            }
                        }
                    }

                    //add new CSS files to <head>
                    if(count($files['css'])){
                        foreach ($files['css'] as $css){
                            $app = false;
                            if(substr($css, 0, 1) != '/') $app = 'dislider';
                            else $css = substr($css, 1);
                            $header->addCss($css, $app);
                        }
                    }

                    //add new JS files to <head>
                    if(count($files['js'])){
                        foreach ($files['js'] as $js){
                            $app = false;
                            if(substr($js, 0, 1) != '/') $app = 'dislider';
                            else $js = substr($js, 1);
                            $header->addJs($js, $app);
                        }
                    }
                }
            }
        }
    }

    /**
     * Show Slider by ID
     *
     * @param int $id           - Slider ID.
     * @param bool $load_jquery - set it to TRUE, if you do not load jQuery yet.
     * @param array $except_js  - array of some JS files to manually prevent load them.
     *
     * @return string - HTML/JS markup of Slider OR Error message.
     */

    public function showSlider($id = 0, $load_jquery = false, $except_js = array()){
        $id = (int) $id;
        if($id > 0){
            $html = $slider_html = $slider_js = $except_css = array();

            //get slider data & params
            $s_model = new disliderSlidersModel();
            $slider_data = $s_model->getById($id);
            $params = json_decode($slider_data['params'], true);

            $type = $slider_data['itype'];

            //get slides
            $i_model = new disliderImagesModel();
            $slides = $i_model->getSliderImages($id);

            //create instance of slider type
            $dislider_class = 'dislider'.$type.'Slider';
            if($type && class_exists($dislider_class)){
                $slider = new $dislider_class($slider_data['width'], $slider_data['height']);

                $header = waSystem::getInstance()->getResponse();

                //get already loaded CSS files from <head>
                $e_css = $header->getCss(false);
                if(count($e_css)){
                    foreach ($e_css as $ec){
                        $except_css[] = $this->parseName($ec);
                    }
                }

                //get already loaded JS files from <head>
                $e_js = $header->getJs(false);
                if(count($e_js)){
                    foreach ($e_js as $ej){
                        $except_js[] = $this->parseName($ej);
                    }
                }

                //load jQuery if required by params
                if($load_jquery && !in_array('jquery-1.8.2.min.js', $except_js))
                    $header->addJs('wa-content/js/jquery/jquery-1.8.2.min.js');

                //load needed CSS files for this slider type
                $new_css = $slider->getSliderCSS($params, $except_css);
                if(count($new_css)){
                    foreach ($new_css as $css){
                        $app = false;
                        if(substr($css, 0, 1) != '/') $app = 'dislider';
                        else $css = substr($css, 1);
                        $header->addCss($css, $app);
                    }
                }

                //load needed JS files for this slider type
                $new_js = $slider->getSliderJS($params, $except_js);
                if(count($new_js)){
                    foreach ($new_js as $js){
                        $app = false;
                        if(substr($js, 0, 1) != '/') $app = 'dislider';
                        else $js = substr($js, 1);
                        $header->addJs($js, $app);
                    }
                }

                //create slider html markup
                $slider_html = $slider->getSliderHTML($params, $slides);

                //create JS slider call
                $slider_js = $slider->getSliderJSCall($params);

                //merge result html
                $html = array_merge($slider_html, $slider_js);

            }else{
                return _w('Uncorrect Slider Type');
            }

            return implode("\n", $html);
        }else{
            return _w('Uncorrect Slider ID');
        }
    }

    private function parseName($file){
        $parts = explode('/', $file);
        $qfname = array_pop($parts);
        $fname = explode('?', $qfname);

        return array_shift($fname);
    }
    
    public function getSlides( $slider_id ) {
        $slider_id = intval($slider_id);
        $i_model = new disliderImagesModel();
        $slides = $i_model->getSliderImages($slider_id);
        return $slides;
    }

}
