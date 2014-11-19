<?php
class shopCustommenuPluginBackendActions extends waViewActions {
    public function __construct($system = null) {
        parent::__construct($system);
        $this->setLayout(new shopBackendLayout());
        waSystem::getInstance()->getResponse()->addCss('wa-apps/shop/plugins/custommenu/css/backend.css');
        waSystem::getInstance()->getResponse()->addJs('wa-apps/shop/plugins/custommenu/js/custommenu_backend.js');
    }
    function listAction() {
        $this->setTemplate('plugins/custommenu/templates/actions/backend/ListMenu.html');
        
        $model = new shopCustommenuModel();
        if($data = waRequest::post()) {
            foreach($data['menu'] as $id => $row) {
                $id = intval($id);
                if (!empty($row['name'])) {
                    if ($id > 0) {
                        if(!empty($row['delete'])) {
                            $model->deleteByField(array('menu_id' => $id));
                        } else {
                            $model->updateByField(array('menu_id' => $id), $row);
                        }
                    } elseif ($id <= 0) {
                        $model->insert($row);
                    }
                }
            }
        }
        $menus = $model->select('*')->order('menu_id DESC')->fetchAll();
        $this->view->assign('menus', $menus);
    }
    function editmenuAction() {
        if(!$menu_id = waRequest::get('id')) {
            return false;
        }
        
        $this->setTemplate('plugins/custommenu/templates/actions/backend/EditMenu.html');

        $modelCM = new shopCustommenuModel();
        $menu = $modelCM->getByField(array('menu_id' => $menu_id));
        $this->view->assign('menu', $menu);
        
        $model = new shopCustommenuItemModel();

        if($data = waRequest::post()) {
            foreach($data['item'] as $id => $row) {
                if (!empty($row['title'])) {
                    $row = $this->validateItem($row);
                    if(($row['parent_id'] > 0 && $model->getByField(array( 'id' => $row['parent_id']))) || $row['parent_id'] == 0 ) {
                        $id = intval($id);
                        if ($id > 0 && $model->getByField(array( 'id' => $id )) ) {
                            if( isset($row['delete']) && !empty($row['delete']) ) {
                                $model->deleteByField(array('id' => $id));
                                $model->deleteByField(array('parent_id' => $id));
                            } else {
                                $model->updateByField(array('id' => $id), $row);
                            }
                        } elseif ($id <= 0) {
                            $row['menu_id'] = $menu_id;
                            $model->insert($row);
                        }
                    }
                }
            }
        }
        
        $items = $model->select('*')->where('menu_id = '.$menu_id)->order('`column` ASC, `sort` ASC')->fetchAll();
        //echo '<pre>';var_dump($items);
        $items = $this->sortItems($items);
        //echo '<pre>';var_dump($items);die;
        $this->view->assign('items', $items);  
    }
    
    private function validateItem($data) {
        foreach($data as $key=>&$val ) {
            $val = trim($val);
            switch($key) {
                case 'menu_id' : { $val = intval($val); break;}
                case 'sort' : { $val = intval($val); break;}
                case 'parent_id' : { $val = intval($val); break;}
                case 'column' : { $val = intval($val); break;}
            }
        }
        return $data;
    }
    
    private function sortItems($unsorted_items) {
        $items = array();
        foreach($unsorted_items as $key=>$item) {
            $items[$item['parent_id']][] = $item;
        }
        return $items;
    }
    
    public function getMenuItems($menu_id) {
        $model = new shopCustommenuItemModel();
        $items = $model->select('*')->where('menu_id = '.$menu_id)->order('`column` ASC, `sort` ASC')->fetchAll();
        $items = $this->sortItems($items);
        return $items;
    }
}