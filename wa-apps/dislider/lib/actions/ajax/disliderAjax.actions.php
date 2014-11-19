<?php

class disliderAjaxActions extends waJsonActions
{

    public function addSliderAction() {
        $res = array();
        $title = waRequest::post('dislider-title', null, 'string');
        $width = waRequest::post('dislider-width', null, 'int');
        $height = waRequest::post('dislider-height', null, 'int');
        $model = new disliderSlidersModel();
        if(isset($title, $width, $height)){
            $save = $model->insert(array(
                'title' => strip_tags($title),
                'width' => max(1, (int) $width),
                'height' => max(1, (int) $height),
                'created' => date('Y-m-d H:i:s')
            ));
            if($save) $res['id'] = $save;
        }
        $this->response = $res;
    }

    public function sortSliderAction(){
        $errors = 0;
        $sort = waRequest::post('slide', array(), 'array_int');
        if(count($sort)){
            $model = new disliderImagesModel();
            foreach ($sort as $k=>$s){
                if(!$model->updateById((int) $s, array('sort' => (int) $k+1)))
                        $errors++;
            }
        }
        $this->response['errors'] = $errors;
    }
    public function saveSlideAction(){
        $errors = 0;
        $sid = waRequest::post('sid', null, 'int');
        $id = waRequest::post('ssid', null, 'int');
        if($sid && $id){
            $title = waRequest::post('title', '', 'string');
            $link = waRequest::post('link', '', 'string');
            $description = waRequest::post('description', '', 'string');
            $data = array(
                'title' => strip_tags($title),
                'link' => strip_tags($link),
                'description' => htmlentities($description, ENT_QUOTES, "UTF-8")
            );
            $model = new disliderImagesModel();
            if(!$model->updateById($id, $data))
                    $errors++;
        }else $errors++;

        $this->response['errors'] = $errors;
    }

    public function saveOptionsAction(){
        $errors = 0; $msg = 'done'; $data = array();
        $sid = waRequest::post('sid', null, 'int');
        $itype = waRequest::post('itype', null, 'string');
        if($sid && $itype){
            $dislider_class = 'dislider'.$itype.'Slider';
            if(class_exists($dislider_class)){
                $slider_type = new $dislider_class();
                $type = $slider_type->getSliderOptions();
            }
            if(isset($type['options']) && count($type['options'])){
                $opts = $type['options'];
                foreach ($opts as $key=>$opt){
                    if($opt['type'] == 'radio'){
                        $option = waRequest::post($opt['group'], null);
                    }else{
                        $option = waRequest::post($key, null);
                    }
                    if($option){
                        switch ($opt['type']){
                            case 'select' : $data[$key] = strip_tags($option); break;
                            case 'text' : $data[$key] = strip_tags($option); break;
                            case 'checkbox' : $data[$key] = '1'; break;
                            case 'radio' : $data[strip_tags($option)] = '1'; break;
                            case 'hidden' : $data[$key] = strip_tags($option); break;
                        }
                    }
                }
                foreach ($opts as $key=>$opt){ //fill empty fields
                    if(!isset($data[$key]) && $opt['type'] != 'spacer')
                        $data[$key] = 0;
                }
                if(count($data)){
                    $upd = array('itype'=>$itype, 'params'=>json_encode($data));
                    $s_model = new disliderSlidersModel();
                    if(!$s_model->updateById($sid, $upd)){
                        $errors++;
                        $msg = 'Update Slider Fail ('.$sid.')';
                    }
                    $this->response['upd'] = $upd;
                }else{
                    $errors++;
                    $msg = 'Empty Data';
                }
            }else{
                $errors++;
                $msg = 'Empty Type Options ('.$itype.')';
            }
        }else{
            $errors++;
            $msg = 'No valid params';
        }

        $this->response['post'] = waRequest::post();
        $this->response['errors'] = $errors;
        $this->response['msg'] = $msg;
        $this->response['sid'] = $sid;
    }

    public function addImageToSliderAction(){
        $err = 0; $errmsg = 'all done';
        $sid = waRequest::post('slider_id', null, 'int');
        $image_id = waRequest::get('image_id', null, 'int');
        $processing = waRequest::post('processing', null, 'int');
        $processing_offset_width = waRequest::post('processing_offset_width', null, 'int');
        $processing_offset_height = waRequest::post('processing_offset_height', null, 'int');
        $settings = compact("processing", "processing_offset_width", "processing_offset_height");
        if($sid && $image_id){
            $i_model = new disliderImagesModel();
            $image = $i_model->getById($image_id);
            if($image){
                try {
                    disliderImage::appendImage($image, $sid, $settings);
                } catch (Exception $e) {
                    $err++;
                    $errmsg = $e->getMessage();
                }
            }else{
                $err++;
                $errmsg = 'DB fail';
            }
        }else{
            $err++;
            $errmsg = 'no valid params';
        }
        $this->response['mess'] = $errmsg;
        $this->response['errors'] = $err;
    }

    public function addImageAction() {
        $success = 0; $errors = array();
        $sid = waRequest::post('sid', null, 'int');
        $processing = waRequest::post('processing', null, 'int');
        $processing_offset_width = waRequest::post('processing_offset_width', null, 'int');
        $processing_offset_height = waRequest::post('processing_offset_height', null, 'int');
        $settings = compact("processing", "processing_offset_width", "processing_offset_height");
        $files = waRequest::file('files');
        $model = new disliderImagesModel();
        foreach ($files as $file) {
            try {
                $image = $this->save($file, $model);
                if($image){
                    disliderImage::createThumbs($image);
                    if($sid > 0) disliderImage::appendImage($image, $sid, $settings);
                    $success++;
                }
            } catch (Exception $e) {
                $errors[] = array(
                    'filename' => $file->name,
                    'error' => $e->getMessage()
                );
            }
        }
        $this->response['result'] = _w('Uploaded files').': '.$success;
        $this->response['errors'] = count($errors);
        if(count($errors)) $this->response['errHTML'] = $this->getErrHtml($errors);
        if($sid) $this->response['sid'] = $sid;
    }

    protected function getErrHtml($errors){
        $res  = _w('Count Errors').': '.count($errors).', ';
        $res .= '<i class="icon10 rarr"></i><a id="upload-errors" href="#" class="inline-link"><b>'._w('Details').'</b></a>';
        $res .= '<ol id="error-details" class="display-none" style="margin:5px 0; padding:0 0 0 15px;">';
        foreach ($errors as $err)
            $res .= '<li style="margin-bottom:5px;"><b>'.$err['filename'].': </b><span class="upload-err">'.$err['error'].'</span></li>';
        return $res .= '</ol>';
    }

    protected function save(waRequestFile $file, $model){
        //check image
        if (!($image = $file->waImage())) {
            throw new waException(_w('Incorrect image format'));
        }
        $data = array(
            'name' => preg_replace('/\.[^\.]+$/', '' ,basename($file->name)),
            'ext' => $file->extension,
            'size' => $file->size,
            'width' => $image->width,
            'height' => $image->height,
            'created'=> date('Y-m-d H:i:s'),
        );
        unset($image);
        $data['id'] = $model->insert($data);
        if (!$data['id']) {
            throw new waException(_w('Database error'));
        }
        $path = disliderImage::getImagePath($data);
        if ((file_exists($path) && !is_writable($path)) || (!file_exists($path) && !waFiles::create($path))) {
            $this->model->deleteById($data['id']);
            throw new waException(sprintf(_w("Can't write file in %s folder."), substr($path, strlen($this->getConfig()->getRootPath()))));
        }
        $file->moveTo($path);

        return $data;
    }

    public function deleteImagesAction(){
        $errors = 0;
        $deleted = array(); $removed_thumbs = array(); $removed_files = array();
        $ids = waRequest::post('images', array(), 'array_int');
        if(count($ids)){
            //delete from DB
            $model = new disliderImagesModel();
            foreach ($ids as $id){
                if(!$model->deleteById($id))
                    $errors++;
                else
                    $deleted[] = $id;
            }
        }
        if(count($deleted)){
            //remove files
            foreach ($deleted as $del){
                $thumb_path = disliderImage::getImageThumbDir(array('id' => $del));
                if($thumb_path)
                    if(!waFiles::delete($thumb_path, true))
                        $errors++;
                    else
                        $removed_thumbs[] = $del;
                $path = wa()->getDataPath(disliderImage::getImageFolder($del), false, 'dislider');
                if($path)
                    if(!waFiles::delete($path, true))
                        $errors++;
                    else
                        $removed_files[] = $del;
            }
        }

        $this->response['errors'] = $errors;
        $this->response['deleted'] = $deleted;
        $this->response['removed_thumbs'] = $removed_thumbs;
        $this->response['removed_files'] = $removed_files;
    }

    public function deleteSlideAction(){
        $id = waRequest::post('id', null, 'int');
        $sid = waRequest::post('sid', null, 'int');
        if($id && $sid){
            $model = new disliderImagesModel();
            $model->deleteById($id);
        }
        //remove files
        $path = disliderImage::getImageThumbDir(array('id' => $id));
        if($path) waFiles::delete ($path, true);

        $this->response['sid'] = $sid;
    }

    public function deleteSliderAction(){
        $err = 0; $msg = 'all done';
        $sid = waRequest::post('sid', null, 'int');
        if($sid){
            $i_model = new disliderImagesModel();
            $s_model = new disliderSlidersModel();
            $images = $i_model->getByField('sID', $sid, true);
            if(!$i_model->deleteByField('sID', $sid)){
                $err++;
                $msg = 'Deleting images Fail';
            }else{
                foreach ($images as $img){
                    //remove files
                    $path = disliderImage::getImageThumbDir(array('id' => $img['id']));
                    if($path){
                        try {
                            waFiles::delete ($path, true);
                        } catch (Exception $e) {
                            $err++;
                            $msg = $e->getMessage();
                        }
                    }
                }
            }
            if(!$del = $s_model->deleteById($sid)){
                $err++;
                $msg = 'Deleting slider Fail';
            }
        }else{
            $err++;
            $msg = 'no valid params';
        }
        if($del){
            //get first slider in list
            $first = $s_model->order('created DESC')->fetch();
            $this->response['sid'] = $first['id'];
        }
        $this->response['err'] = $err;
        $this->response['msg'] = $msg;
    }

}
