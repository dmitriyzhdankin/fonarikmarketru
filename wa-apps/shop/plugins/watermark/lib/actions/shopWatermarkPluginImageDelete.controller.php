<?php

class shopWatermarkPluginImageDeleteController extends waJsonController
{
    public function execute()
    {
        $plugin = wa()->getPlugin('watermark');
        /**
         * @var shopWatermarkPlugin $plugin
         */
        $settings = array(
            'delete_image' => 1
        );
        $this->response = $plugin->saveSettings($settings);
    }
}
