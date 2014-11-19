<?php

class shopFrontendCategoryAction extends shopFrontendAction
{
    /**
     * @var shopCategoryModel $model
     */
    protected $model;

    /**
     * @return shopCategoryModel
     */
    protected function getModel()
    {
        if (!$this->model) {
            $this->model = new shopCategoryModel();
        }
        return $this->model;
    }

    /**
     * @return mixed
     * @throws waException
     */
    protected function getCategory()
    {
        $category_model = $this->getModel();
        $url_field = waRequest::param('url_type') == 1 ? 'url' : 'full_url';

        if (waRequest::param('category_id')) {
            $category = $category_model->getById(waRequest::param('category_id'));
            if ($category) {
                $category_url = wa()->getRouteUrl('/frontend/category', array('category_url' => $category[$url_field]));
                if (urldecode(wa()->getConfig()->getRequestUrl(false, true)) !== $category_url) {
                    $q = waRequest::server('QUERY_STRING');
                    $this->redirect($category_url.($q ? '?'.$q : ''), 301);
                }
            }
        } else {
            $category = $category_model->getByField($url_field, waRequest::param('category_url'));
            if ($category && $category[$url_field] !== urldecode(waRequest::param('category_url'))) {
                $q = waRequest::server('QUERY_STRING');
                $this->redirect(wa()->getRouteUrl('/frontend/category', array('category_url' => $category[$url_field])).($q ? '?'.$q : ''), 301);
            }
        }
        $route = wa()->getRouting()->getDomain(null, true) . '/' . wa()->getRouting()->getRoute('url');
        if ($category) {
            $category_routes_model = new shopCategoryRoutesModel();
            $routes = $category_routes_model->getRoutes($category['id']);
        }
        if (!$category || ($routes && !in_array($route, $routes))) {
            throw new waException('Category not found', 404);
        }
        $category['subcategories'] = $category_model->getSubcategories($category, $route);
        $category_url = wa()->getRouteUrl('shop/frontend/category', array('category_url' => '%CATEGORY_URL%'));
        foreach ($category['subcategories'] as &$sc) {
            $sc['url'] = str_replace('%CATEGORY_URL%', waRequest::param('url_type') == 1 ? $sc['url'] : $sc['full_url'], $category_url);
        }
        unset($sc);
        // params
        $category_params_model = new shopCategoryParamsModel();
        $category['params'] = $category_params_model->get($category['id']);

        // smarty description
        if ($this->getConfig()->getOption('can_use_smarty') && $category['description']) {
            $category['description'] = wa()->getView()->fetch('string:' . $category['description']);
        }
        return $category;
    }

    public function execute()
    {
        //##_## Added filters
        $category_model = new shopCategoryModel();
        if (waRequest::param('category_id')) {
            $category = $category_model->getById(waRequest::param('category_id'));

        } else {
            $category_url_arr=explode("/",waRequest::param('category_url'));
            $cat_url="";
            foreach($category_url_arr AS $arr)
            {
                if((substr_count($arr,"ff-")==0) AND (substr_count($arr,"price_min-")==0) AND (substr_count($arr,"price_max-")==0))
                {
                    $cat_url .= $arr."/";
                }elseif(substr_count($arr,"ff-")!=0)
                {
                    $f_url=$arr;
                    $f_url=str_replace("ff-","",$f_url);

                    $fexpl=explode("-",$f_url);

                    foreach($fexpl AS $expl_arr)
                    {
                        $f_url_arr=explode("_",$expl_arr);
                        $k[]=$f_url_arr[1];
                        $_GET[$f_url_arr[0]]=$k;
                    }

                }elseif(substr_count($arr,"price_min-")!=0)
                {
                    $f_url=$arr;
                    $f_url=str_replace("price_min-","",$f_url);
                    $_GET["price_min"]=$f_url;
                }elseif(substr_count($arr,"price_max-")!=0)
                {
                    $f_url=$arr;
                    $f_url=str_replace("price_max-","",$f_url);
                    $_GET["price_max"]=$f_url;
                }

            }
            $cat_url = str_replace("///","",$cat_url."//");

            $category = $category_model->getByField(waRequest::param('url_type') == 1 ? 'url' : 'full_url', $cat_url);
            $this->view->assign('cat_url', $cat_url);
        }
        $route = wa()->getRouting()->getDomain(null, true).'/'.wa()->getRouting()->getRoute('url');
        if (!$category || ( isset($category['route']) && $category['route'] && $category['route'] != $route)) {
            throw new waException('Category not found', 404);
        }

        if ($category['filter']) {
            $filter_ids = explode(',', $category['filter']);
            $feature_model = new shopFeatureModel();
            $features = $feature_model->getById(array_filter($filter_ids, 'is_numeric'));
            if ($features) {
                $features = $feature_model->getValues($features);
            }
            $filters = array();
            foreach ($filter_ids as $fid) {
                if ($fid == 'price') {
                    $filters['price'] = true;
                } elseif (isset($features[$fid])) {
                    $filters[$fid] = $features[$fid];
                }
            }
            $this->view->assign('filters', $filters);
        }

        $category['subcategories'] = $category_model->getSubcategories($category, true);
        $category_url = wa()->getRouteUrl('shop/frontend/category', array('category_url' => '%CATEGORY_URL%'));
        foreach ($category['subcategories'] as &$sc) {
            $sc['url'] = str_replace('%CATEGORY_URL%', waRequest::param('url_type') == 1 ? $sc['url'] : $sc['full_url'], $category_url);
        }
        unset($sc);

        if ($category['parent_id']) {
            $breadcrumbs = array();
            $path = array_reverse($category_model->getPath($category['id']));
            foreach ($path as $row) {
                $breadcrumbs[] = array(
                    'url' => wa()->getRouteUrl('/frontend/category', array('category_url' => waRequest::param('url_type') == 1 ? $row['url'] : $row['full_url'])),
                    'name' => $row['name']
                );
            }
            if ($breadcrumbs && $this->layout) {
                $this->layout->assign('breadcrumbs', $breadcrumbs);
            }
        }

        if ($category['type'] == shopCategoryModel::TYPE_DYNAMIC && !$category['sort_products']) {
            $category['sort_products'] = 'create_datetime DESC';
        }

        $category_params_model = new shopCategoryParamsModel();
        $category['params'] = $category_params_model->get($category['id']);

        if ($this->getConfig()->getOption('can_use_smarty') && $category['description']) {
            $category['description'] = wa()->getView()->fetch('string:'.$category['description']);
        }

        $this->view->assign('category', $category);

        if ($category['sort_products'] && !waRequest::get('sort')) {
            $sort = explode(' ', $category['sort_products']);
            if (isset($sort[1])) {
                $order = strtolower($sort[1]);
            } else {
                $order = 'asc';
            }
            $_GET['sort'] = $sort[0];
            $_GET['order'] = $order;
        } elseif (!$category['sort_products'] && !waRequest::get('sort')) {
            $this->view->assign('active_sort', '');
        }

        $this->setCollection(new shopProductsCollection('category/'.$category['id']));

        $title = $category['meta_title'] ? $category['meta_title'] : $category['name'];
        wa()->getResponse()->setTitle($title);
        wa()->getResponse()->setMeta('keywords', $category['meta_keywords']);
        wa()->getResponse()->setMeta('description', $category['meta_description']);

        /**
         * @event frontend_category
         * return array[string]string $return[%plugin_id%] html output for category
         */
        $this->view->assign('frontend_category', wa()->event('frontend_category'));
        $this->setThemeTemplate('category.html');
    }

    /**
     * @param shopDimensionValue|double $v
     * @return double
     */
    protected function getFeatureValue($v)
    {
        if ($v instanceof shopDimensionValue) {
            return $v->value_base_unit;
        }
        if (is_object($v)) {
            return $v->value;
        }
        return $v;
    }

    protected function sortSkus($a, $b)
    {
        if ($a['sort'] == $b['sort']) {
            return 0;
        }
        return ($a['sort'] < $b['sort']) ? -1 : 1;
    }
}
