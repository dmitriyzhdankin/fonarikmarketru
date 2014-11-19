<?php

class disliderBackendActions extends waViewActions {

    public function preExecute() {
        $this->setLayout(new disliderBackendLayout());
    }

    public function defaultAction() {
        $id = waRequest::get('id', null, 'int');
        $model = new disliderSlidersModel();
        $model_images = new disliderImagesModel();
        $settings = wa('dislider')->getConfig()->getOption();
        if ($id > 0) {
            $slider = $model->getById($id);
            $images = $model_images->getSliderImages($id);
            $this->view->assign('slider', $slider);
        } else {
            $images = $model_images->getAllImages();
            $sliders = $model->order('created DESC')->fetchAll();
            $this->view->assign('sliders', $sliders);
        }
        $this->view->assign('settings', $settings);
        $this->view->assign('images', $images);
    }

}