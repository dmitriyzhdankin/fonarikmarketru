<?php

class disliderBackendOptionsAction extends waViewAction
{
    public function execute() {
        $slider = array(); $options = array();
        $id = waRequest::get('id', null, 'int');
        $module = waRequest::get('module', null, 'string');
        $s_model = new disliderSlidersModel();
        if($id > 0){
            $itypes = array();
            $slider = $s_model->getById($id);
            if(isset($slider['params']) && $slider['params']){
                $slider['params'] = json_decode($slider['params'], true);
            }
            $types = include wa()->getConfig()->getAppConfigPath('slidertypes');
            foreach ($types as $type){
                $dislider_class = 'dislider'.$type.'Slider';
                if(class_exists($dislider_class)){
                    $slider_type = new $dislider_class();
                    $opts = $slider_type->getSliderOptions();
                    $itypes[] = array('id'=>$opts['type'], 'name'=>_w($opts['name']), 'flds'=>$opts['fields']);

                    //Translate options
                    foreach ($opts['options'] as $k=>&$v){
                        if(isset($v['title']) && $v['title']){
                            $v['title'] = _w($v['title']);
                        }
                        if(isset($v['values']) && $v['values']){
                            $vals = array();
                            foreach ($v['values'] as $key=>$val){
                                $vals[_w($key)] = $val;
                            }
                            $v['values'] = $vals;
                        }
                    }
                    $options[$opts['type']] = $opts['options'];
                }
            }

            $this->view->assign('itypes', $itypes);
            $this->view->assign('current', '3'); //Slider View
        }
        if(!$id && $module != 'settings'){
            $this->view->assign('current', '1'); //All Images View
        }elseif ($module == 'settings') {
            $this->view->assign('current', '2'); //Settings View
        }

        $this->view->assign('slider', $slider);
        $this->view->assign('options', $options);
    }
}