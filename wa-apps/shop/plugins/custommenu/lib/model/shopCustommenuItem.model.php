<?php
class shopCustommenuItemModel extends waModel
{
    protected $table = 'shop_custommenu_item';
    protected $id = null;

//    public function changeMenu($data) {
//        
//        if( isset($data['menu_id']) && $data['menu_id'] ) { // change
//            if( ! $this->getByField('menu_id',$data['menu_id']) ) { return false; }
//            
//            if( isset($data['delete']) && $data['delete'] ) { // delete
//                $this->deleteByField(array('menu_id'=>$data['menu_id']));
//            } else { // update
//                $this->updateByField(array('menu_id'=>$data['menu_id']), $data);
//            }
//        } else { // insert
//            $this->insert(array('menu_id'=>$menu_id,'category_id'=>$id), 2);
//        }
//    }
}
