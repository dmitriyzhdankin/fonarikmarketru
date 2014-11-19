<?php

class disliderBackendSidebarAction extends waViewAction
{
    public function execute() {
        $id = waRequest::get('id', null, 'int');
        $module = waRequest::get('module', null, 'string');
        $model = new disliderSlidersModel();
        $modelImg = new disliderImagesModel();
        $sliders = $model->order('created DESC')->fetchAll();
        if($sliders){
            foreach($sliders as &$s){
                if($s['id'] == $id){
                    $s['selected'] = 1;
                }else{
                    $s['selected'] = 0;
                }
                $s['count'] = $modelImg->countByField('sID', $s['id']);
            }
        }
        if($id){
            $this->view->assign('current', '3'); //Slider View
        }elseif(!$id && $module != 'settings'){
            $this->view->assign('current', '1'); //All Images View
        }elseif ($module == 'settings') {
            $this->view->assign('current', '2'); //Settings View
        }
        $this->view->assign('appUrl', wa()->getAppUrl());
        $this->view->assign('icount', $modelImg->countByField('sID', '0'));
        $this->view->assign('sliders', $sliders);
    }
}