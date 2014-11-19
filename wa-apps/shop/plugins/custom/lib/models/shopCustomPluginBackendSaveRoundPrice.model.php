<?php
class shopCustomPluginBackendSaveRoundPriceModel extends waModel {

    public function roundPrice( $code ) {
        $currency_model = new shopCurrencyModel();
        $primary = $currency_model->getPrimaryCurrency();
        if ($code == $primary) {
            return false;
        }
        $currency = $currency_model->getById($code);
        if (!$currency) {
            return false;
        }
        
        $this->roundProductPrimaryPrices($code);
        $this->roundServicePrimaryPrices($code);

        return true;
    }
   
    private function roundProductPrimaryPrices($code = null) {
        $where = $code ? "WHERE p.currency = '".$this->escape($code)."'" : '';
        $sql = "UPDATE `shop_product` p 
            SET p.min_price = CEIL(p.min_price), p.max_price = CEIL(p.max_price), p.price = CEIL(p.price), p.compare_price = CEIL(p.compare_price)
            " . $where;

        $this->exec($sql);
        
        $sql = "UPDATE `shop_product` p
                JOIN `shop_product_skus` ps ON p.id = ps.product_id
                SET ps.primary_price = CEIL(ps.primary_price)
                $where";
        $this->exec($sql);
    }

    private function roundServicePrimaryPrices($code = null) {
        $where = $code ? "WHERE s.currency = '".$this->escape($code)."'" : '';

        $sql = "UPDATE `shop_service_variants` sv
                JOIN `shop_service` s ON s.id = sv.service_id
                SET sv.primary_price = CEIL(sv.primary_price)
                $where";
        $this->exec($sql);

        $sql = "UPDATE `shop_product_services` ps
                JOIN `shop_service` s ON s.id = ps.service_id
                SET ps.primary_price = CEIL(ps.primary_price)
                $where";
        $this->exec($sql);

        $sql = "UPDATE `shop_service` s
                SET s.price = CEIL(s.price)
                $where";
        $this->exec($sql);
    }
}