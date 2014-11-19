<?php

class shopCustomPluginBackendProductCategoriesAction extends waViewAction
{
    public function execute()
    {
        $category_model = new shopCategoryModel();
        $categories = $category_model->getFullTree('id, name, depth, url, full_url', true);        
        $this->view->assign('categories', $categories);
        $product = new shopProduct(waRequest::get('id', 0, waRequest::TYPE_INT));
        $this->view->assign('product', $product);
    }
}