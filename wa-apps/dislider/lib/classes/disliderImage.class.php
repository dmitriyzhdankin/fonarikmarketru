<?php

class disliderImage
{
    const THUMB_HEIGHT = 150;
    const SHARP_AMOUNT = 6;

    public static function getImageUrl($data, $size = null, $absolute = false)
    {
        if(!$size) $size = 'thumb';
        $path = self::getImageFolder($data['id']).'/'.$data['id'];
        $path .= '/'.$data['id'].'.'.$size.'.'.$data['ext'];
        if (waSystemConfig::systemOption('mod_rewrite')) {
            return wa()->getDataUrl($path, true, 'dislider', $absolute);
        } else {
            $wa = wa();
            if (file_exists($wa->getDataPath($path, true, 'dislider'))) {
                return $wa->getDataUrl($path, true, 'dislider', $absolute);
            } else {
                return null;
            }
        }
    }

    public static function getImageThumbDir($data){
        $path = self::getImageFolder($data['id']).'/'.$data['id'];
        return wa()->getDataPath($path, true, 'dislider');
    }

    public static function getImageThumbPath($data, $size = null){
        $thumb_path = self::getImageThumbDir($data);
        if(!$size) $size = 'thumb';
        return $thumb_path.'/'.$data['id'].'.'.$size.'.'.$data['ext'];
    }

    public static function getImageFolder($id){
        $str = str_pad($id, 4, '0', STR_PAD_LEFT);
        return substr($str, -2).'/'.substr($str, -4, 2);
    }

    public static function getImagePath($data){
        $file_name = self::getImageFolder($data['id']).'/'.$data['id'].'.'.$data['ext'];
        return wa()->getDataPath($file_name, false, 'dislider');
    }

    public static function createThumbs($image, $path = null){
        if(!$path) $path = self::getImagePath($image);
        $thumb_path = self::getImageThumbPath($image);
        $preview_path = self::getImageThumbPath($image, 'preview');
        if ($path instanceof waImage) {
            $image = $path;
        } else {
            $image = waImage::factory($path);
        }

        //Create thumbnail
        $width = ($image->width * self::THUMB_HEIGHT) / $image->height;
        $image->resize($width, self::THUMB_HEIGHT, waImage::NONE);
        if($width > 250) $image->crop(250, self::THUMB_HEIGHT, waImage::CENTER, 0);
        $image->save($thumb_path);
        unset($image);
        //Create preview image
        $preview = waImage::factory($path);
        $preview->resize(640, 480, waImage::AUTO);
        $preview->save($preview_path);
    }

    protected static function getOffset($settings){
        $res = array();
        foreach ($settings as $k=>$v){
            $val = 0;
            switch ($v) {
                case 2: $val = waImage::CENTER; break;
                case 3: $val = waImage::BOTTOM; break;
            }
            switch ($k) {
                case 0: $res['w'] = $val; break;
                case 1: $res['h'] = $val; break;
            }
        }
        return $res;
    }

    protected static function getOverlayOffset($settings, $bg, $overlay){
        $res = array();
        switch ($settings[0]) {
            case 1: $res[0] = 0; break;
            case 2: $res[0] = ($bg[0] - $overlay[0])/2; break;
            case 3: $res[0] = $bg[0] - $overlay[0]; break;
        }
        switch ($settings[1]) {
            case 1: $res[1] = 0; break;
            case 2: $res[1] = ($bg[1] - $overlay[1])/2; break;
            case 3: $res[1] = $bg[1] - $overlay[1]; break;
        }
        return $res;
    }

    public static function appendImage($image, $sid, $settings){
        //Insert image in slider
        $s_model = new disliderSlidersModel();
        $i_model = new disliderImagesModel();
        $slider = $s_model->getById($sid);
        $size = $slider['width'].'x'.$slider['height'];
        $data = array(
            'sID' => $sid,
            'original' => $image['id'],
            'name' => $image['name'].'.'.$size,
            'ext' => $image['ext'],
            'width' => $slider['width'],
            'height' => $slider['height'],
            'created' => date('Y-m-d H:i:s'),
            'sort' => $i_model->getMaxSort($sid)
        );
        $data['id'] = $i_model->insert($data);
        if (!$data['id']) {
            throw new waException(_w('Database error'));
        }
        $slide_path = self::getImageThumbPath($data, $size); //path to new slider image

        if ((file_exists($slide_path) && !is_writable($slide_path)) || (!file_exists($slide_path) && !waFiles::create($slide_path))) {
            $i_model->deleteById($data['id']);
            throw new waException(sprintf(_w("Can't write file in %s folder."), substr($slide_path, strlen($this->getConfig()->getRootPath()))));
        }

        //Create resized image (slide)
        $path = self::getImagePath($image); //path to original image
        $img = waImage::factory($path);
        $offset = self::getOffset(array($settings['processing_offset_width'], $settings['processing_offset_height']));
        switch ($settings['processing']) {
            case 1: //Rectangle crop
                $img->resize($slider['width'], $slider['height'], waImage::INVERSE)
                    ->crop($slider['width'], $slider['height'], $offset['w'], $offset['h']);
                break;
            case 2: //Proportionally resizing
                $img->resize($slider['width'], $slider['height'], waImage::AUTO);
                break;
            case 3: //Not proportionally adjustment
                $img->resize($slider['width'], $slider['height'], waImage::NONE);
                break;
        }
        if(wa('dislider')->getConfig()->getOption('sharpen'))
            $img->sharpen(self::SHARP_AMOUNT);
        if(!$img->save($slide_path, wa('dislider')->getConfig()->getOption('save_quality'))){
            $i_model->deleteById($data['id']);
            throw new waException(sprintf(_w("Can't write file in %s folder."), substr($slide_path, strlen($this->getConfig()->getRootPath()))));
        }

        //Create background overlay
        if($img->width != $slider['width'] || $img->height != $slider['height']){
            $w = $img->width; $h = $img->height; $type = $img->type;
            unset($img);
            $tmp_name = self::getImageThumbPath($image, 'tmp'.  uniqid());
            rename($slide_path, $tmp_name);
            $offset = self::getOverlayOffset(
                array($settings['processing_offset_width'], $settings['processing_offset_height']),
                array($slider['width'], $slider['height']),
                array($w, $h)
            );
            switch ($type) {
                case 1: $source = imagecreatefromgif($tmp_name); break;
                case 2: $source = imagecreatefromjpeg($tmp_name); break;
                case 3: $source = imagecreatefrompng($tmp_name); break;
            }
            if(!$source) throw new waException(_w('Incorrect image format'));
            $bg = imagecreatetruecolor($slider['width'], $slider['height']);
            $color = imagecolorallocate($bg, 255, 255, 255);
            imageFilledRectangle($bg, 0, 0, $slider['width'] - 1, $slider['height'] - 1, $color);
            imagealphablending($source, true);
            imagecopy($bg, $source, $offset[0], $offset[1], 0, 0, $w, $h);
            imagejpeg($bg, $slide_path);
            imagedestroy($bg);
            imagedestroy($source);
            unlink($tmp_name);
        }

        //Update field 'size' in DB & create Thumbnail (from resized image)
        $i_model->updateById($data['id'], array('size' => filesize($slide_path)));
        self::createThumbs($data, $slide_path);
    }

}