<?php
class shopCustommenuPlugin extends shopPlugin {
    
    protected static $plugin;

    public function addBackendSettings() {
        return array('aux_li' => '<li class="small float-right"><a href="?plugin=custommenu&action=list">Custom Menu</a></li>');
    }
    protected static function getThisPlugin() {
        if (self::$plugin) {
            return self::$plugin;
        } else {
            $info = array(
                'id' => 'custommenu',
                'app_id' => 'shop'
            );
            return new shopCustommenuPlugin($info);
        }
    }
    public static function displayMenu($menu_id) {
        $plugin = self::getThisPlugin();
        $BA = new shopCustommenuPluginBackendActions();
        $items = $BA->getMenuItems($menu_id);
        $html = $plugin->renderMenu($items,0,0);
        return $html;
    }

    private function renderMenu($items,$parent_id,$level) {
        $html = '';
        if(isset($items[$parent_id])) {
            $col_num = 0;
            foreach($items[$parent_id] as $item) {
                if ($item['parent_id'] == 0){$level = 0;}

                if($level==1) {
                    if(!$col_num) {
                        $col_num = 1;
                        $html .= '<div class="col_1"><ul>';
                    } elseif($col_num != $item['column'] ) {
                        $col_num = $item['column'];
                        $html .= '</ul></div><div class="col_1"><ul>';
                    }
                }

                $a_class = '';
                if(isset( $items[$item['id']] ) && $level == 0 ) {
                    $a_class = 'class="drop" ';
                }
                if($item['type'] == 'product') {
                    $wa = new shopViewHelper(waSystem::getInstance());
                    $product = $wa->product($item['url']);
                    $html .= '<li>
                                <div class="grid_3 product">
                                  <div class="prev"><a href="'. $wa->productUrl($product) .'" title="'. $product['name'] . ($product['summary'] ? '&mdash; '. strip_tags($product['summary']) : '') .'">
                                        '. $wa->productImgHtml($product, '200x200', array('itemprop' => 'image', 'default' => "", 'alt' => $product['name']) ) .'
                                  </a></div>
                                  <h3 class="title">'. $product['name'] .'</h3>
                                  <div class="cart">
                                    <div class="price">
                                    <div class="vert">
                                      <div class="price_new">'. shop_currency(ceil($product['price'])) .'</div>
                                      '.($product['compare_price'] > 0 ? '<div class="price_old" itemprop="price">'. shop_currency(ceil($product['compare_price'])) .'</div>' : '') .'
                                    </div>
                                    </div>
                                    <form class="addtocart" method="post" action="'. waSystem::getInstance()->getRouteUrl('/frontendCart/add') .'">
                                    <input type="hidden" name="product_id" value="'. $product['id'] .'">
                                    <input type="submit" onclick="open_pop_up(\'#onclick_buy\');" '.( $product['price'] == 0 ? 'disabled="disabled" style="background:#CCC;"' : '' ) .' value="Купить">
                                    </form>
                                  </div>
                                </div>
                              </li>';
                } else {
                    $html .= '<li><a '.$a_class.'href="'. $item['url'] .'" title="'. $item['title'] .'">'.$item['title'].'</a>';
                }
                if(isset( $items[$item['id']] ) ) {
                    if($level == 0) { $html .= '<div class="dropdown_3columns align_left">'; }
                    if($level == 1) { $html .= '<ul class="nav">'; }
                    $html .= $this->renderMenu($items,$item['id'],$level+1);
                    if($level == 1) { $html .= '</ul>'; }
                    if($level == 0) { $html .= '</div>'; }
                }
                $html .= '</li>';
            }
            if($level==1) {
                $html .= '</ul></div>';
            }
        }
        return $html;
    }
}