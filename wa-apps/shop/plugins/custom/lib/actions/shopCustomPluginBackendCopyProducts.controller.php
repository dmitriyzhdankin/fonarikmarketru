<?php

class shopCustomPluginBackendCopyProductsController extends waJsonController
{
    public function execute()
    {
        $products_id = waRequest::post('product_id', '', waRequest::TYPE_ARRAY_INT);
        for($i=0;$i<count($products_id);$i++) {
            $product = new shopProduct(intval($products_id[$i]));
            if( !$product ) continue;            
 
            $features_model = new shopProductFeaturesModel();
            $features = $features_model->getByField('product_id', $product['id'], true);
            
            $related_model = new shopProductRelatedModel();
            $related = $related_model->getByField('product_id', $product['id'], true);
            
            $tags_model = new shopProductTagsModel();
            $tags = $tags_model->getByField('product_id', $product['id'], true);
            
            $categories_model = new shopCategoryProductsModel();
            $categories = $categories_model->getByField('product_id', $product['id'], true);
            
            $data = array(
                'name' => $product['name'] . ' (Copy)',
                'summary' => $product['summary'],
                'meta_title' => $product['meta_title'],
                'meta_keywords' => $product['meta_keywords'],
                'meta_description' => $product['meta_description'],
                'description' => $product['description'],
                'contact_id' => $product['contact_id'],
                'status' => 0,
                'type_id' => $product['type_id'],
                'category_id' => $product['category_id'],
                'skus' => array( 
                    array('available' => 1)
                )                
            ); 
            
            $new_product = new shopProduct();
            $new_product->save($data, true, $this->errors);
            if( !$this->errors ) {
                $product_id = $new_product->getId();
                foreach($features as $value) {
                    $features_model->multipleInsert(array('product_id' => $product_id, 'feature_id' => $value['feature_id'], 'feature_value_id' => $value['feature_value_id']));
                }
                foreach($related as $value) {
                    $related_model->multipleInsert(array('product_id' => $product_id, 'type' => $value['type'], 'related_product_id' => $value['related_product_id']));
                }
                foreach($tags as $value) {
                    $tags_model->multipleInsert(array('product_id' => $product_id, 'tag_id' => $value['tag_id'] ));
                }
                foreach($categories as $value) {
                    $categories_model->multipleInsert(array('product_id' => $product_id, 'category_id' => $value['category_id'], 'sort' => $value['sort'] ));
                    $categories_id[] = $value['category_id'];
                }
                $categories_model->add(array($product_id),$categories_id);
                $shop_category_model = new shopCategoryModel();
                $shop_category_model->recount($categories_id);
            }
        }
        
        $this->response['lists'] = $this->getLists();
    }
    public function getLists()
    {
        $product_model  = new shopProductModel();
        $category_model = new shopCategoryModel();
        $set_model  = new shopSetModel();
        $type_model = new shopTypeModel();
        return array(
            'category' => $category_model->getAll('id'),
            'set'  => $set_model->getAll('id'),
            'type' => $type_model->getAll('id'),
            'all'  => array(
                'count' => $product_model->countAll()
            )
        );
    }
}