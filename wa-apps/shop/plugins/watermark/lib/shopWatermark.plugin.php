<?php

class shopWatermarkPlugin extends shopPlugin
{
    public function imageUpload(waImage $image)
    {
        $settings = $this->getSettings();

        $opacity = $settings['opacity'];
        $result = null;
        if ($opacity && !empty($settings['text'])) {
            $font_path = $this->path.'/config/data/arial.ttf';

            $config = wa('shop')->getConfig();
            /**
             * @var shopConfig $config
             */
            $size = $config->getImageSize('big');
            if (!$size) {
                return false;
            }

            $options = array(
                'watermark'        => $settings['text'],
                'opacity'          => $opacity,
                'font_file'        => $font_path,
                'font_size'        => $settings['text_size'] * max($image->width, $image->height) / $size,
                'font_color'       => $settings['text_color'],
                'text_orientation' => $this->orientation($settings['text_orientation']),
                'align'            => $this->align($settings['text_align']),
            );
            $image->watermark($options);
            $result = true;
        }
        if ($opacity && !empty($settings['image'])) {
            $watermark_path = wa()->getDataPath('data/', true).$settings['image'];
            $watermark = waImage::factory($watermark_path);
            $options = array(
                'watermark' => $watermark,
                'opacity'   => $opacity,
                'align'     => $this->align($settings['image_align']),
            );
            $image->watermark($options);
            $result = true;
        }
        return $result;
    }

    public function getPath()
    {
        return $this->path;
    }

    private function align($code)
    {
        $align = waImage::ALIGN_TOP_LEFT;
        switch ($code) {
            case 'tl':
                $align = waImage::ALIGN_TOP_LEFT;
                break;
            case 'tr':
                $align = waImage::ALIGN_TOP_RIGHT;
                break;
            case 'bl':
                $align = waImage::ALIGN_BOTTOM_LEFT;
                break;
            case 'br':
                $align = waImage::ALIGN_BOTTOM_RIGHT;
                break;
        }
        return $align;
    }

    private function orientation($code)
    {
        return $code == 'v' ? waImage::ORIENTATION_VERTICAL : waImage::ORIENTATION_HORIZONTAL;
    }

    public function validateSettings($new_settings)
    {
        if (!empty($new_settings['image'])
            && ($new_settings['image']->error_code != UPLOAD_ERR_NO_FILE)
            && ($new_settings['image']->error_code != UPLOAD_ERR_OK)
        ) {
            throw new waException($new_settings['image']->error);
        }
        return $new_settings;
    }

    public function saveSettings($settings = array())
    {
        $settings = $this->validateSettings($settings);

        if (isset($settings['delete_image']) && $settings['delete_image']) {
            $settings['image'] = '';
            unset($settings['delete_image']);
        } elseif (isset($settings['image']) && ($settings['image'] instanceof waRequestFile)) {
            /**
             * @var waRequestFile $file
             */
            $file = $settings['image'];
            if ($file->uploaded()) {
                // check that file is image
                try {
                    // create waImage
                    $image = $file->waImage();
                } catch (Exception $e) {
                    throw new Exception(_w("File isn't an image"));
                }
                $path = wa()->getDataPath('data/', true);
                $file_name = 'watermark.'.$image->getExt();
                if (!file_exists($path) || !is_writable($path)) {
                    $message = _wp(
                        'File could not be saved due to the insufficient file write permissions for the %s folder.'
                    );
                    throw new waException(sprintf($message, 'wa-data/public/shop/data/'));
                } elseif (!$file->moveTo($path, $file_name)) {
                    throw new waException(_wp('Failed to upload file.'));
                }
                $settings['image'] = $file_name;
            } else {
                $image = $this->getSettings('image');
                $settings['image'] = $image;
            }
        }

        parent::saveSettings($settings);

        return array(
            'filesrc' => self::fileSrc($this->getSettings('image'))
        );

    }

    private static function fileSrc($file_name)
    {
        $src = '';
        if ($file_name) {
            $file_path = wa()->getDataPath('data/', true, 'shop').$file_name;
            if (file_exists($file_path)) {
                $src = wa()->getDataUrl('data/', true, 'shop', true).$file_name.'?'.filemtime($file_path);
            }
        }
        return $src;
    }

    public static function getFileControl($name, $params)
    {
        $plugin = wa('shop')->getPlugin('watermark');
        /**
         * @var shopWatermarkPlugin $plugin
         */
        $view = wa()->getView();

        $file_name = $plugin->getSettings('image');

        $view->assign('plugin_id', $plugin->id);
        $view->assign('src', self::fileSrc($file_name));
        $view->assign('file_name', $file_name);

        return $view->fetch($plugin->getPath().'/templates/SettingsFileControl.html');
    }
}
