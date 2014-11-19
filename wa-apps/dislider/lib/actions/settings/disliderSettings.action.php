<?php

class disliderSettingsAction extends waViewAction {

    public function execute() {
        $this->setLayout(new disliderBackendLayout());
        $settings = $this->getConfig()->getOption();
        if (waRequest::getMethod() == 'post') {
            $this->save($settings);
            $this->view->assign('saved', 1);
        }
        $this->view->assign('settings', $settings);
    }

    protected function save(&$settings) {
        $settings['processing'] = waRequest::post('processing', '1', 'int');
        $settings['processing_offset_width'] = waRequest::post('processing_offset_width', '1', 'int');
        $settings['processing_offset_height'] = waRequest::post('processing_offset_height', '1', 'int');
        $settings['sharpen'] = waRequest::post('sharpen') ? 1 : 0;
        $settings['save_quality'] = (float) waRequest::post('save_quality', '90');
        $config_file = $this->getConfig()->getConfigPath('config.php');
        waUtils::varExportToFile($settings, $config_file);
    }

}