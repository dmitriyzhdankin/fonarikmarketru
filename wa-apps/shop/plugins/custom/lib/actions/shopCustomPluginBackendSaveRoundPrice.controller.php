<?php

class shopCustomPluginBackendSaveRoundPriceController extends waJsonController {
    
    public function execute() {
        $code = waRequest::post('code', '', waRequest::TYPE_STRING_TRIM);
        
        if (!$code) {
            $this->errors[] = _w("Error when round price");
            return;
        }
        $roundprice_model = new shopCustomPluginBackendSaveRoundPriceModel();
        if(!$roundprice_model->roundPrice($code)) {
            $this->errors[] = _w("Error when round price");
            return;
        }
    }
}