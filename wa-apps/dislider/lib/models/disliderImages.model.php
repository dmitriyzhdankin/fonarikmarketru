<?php

class disliderImagesModel extends waModel
{
  protected $table = 'dislider_images';

  protected function getUrls(&$res, $size = null, $key = 'url'){
      foreach ($res as &$r)
          $r[$key] = disliderImage::getImageUrl($r, $size);
  }

  public function getAllImages($field = 'id', $dir = 'DESC'){
      $res = $this->select('*')->where('sID = 0')->order($field.' '.$dir)->fetchAll();
      if(count($res)){
          $this->getUrls($res, null, 'thumb_url');
          $this->getUrls($res, 'preview', 'preview_url');
          foreach ($res as &$r)
              $r['url'] = disliderImage::getImagePath(array('id' => $r['id'], 'ext' => $r['ext']));
      }
      return $res;
  }

  public function getSliderImages($sid){
      $res = $this->select('*')->where('sID = '.(int) $sid)->order('sort ASC')->fetchAll();
      if(count($res)){
          $this->getUrls($res, $res[0]['width'].'x'.$res[0]['height']);
          $this->getUrls($res, null, 'thumb_url');

          //Get original filenames & decode descriptions
          $originals = array();
          foreach ($res as &$r){
              if(isset($r['original']) && !in_array($r['original'], $originals)){
                  $originals[] = $r['original'];
              }
              if(isset($r['description'])) $r['description'] = html_entity_decode($r['description']);
          }
          if(count($originals)) $source = $this->getByField('id', $originals, true);
          if(count($source)){
              $sources = array();
              foreach ($source as $s)
                  $sources[$s['id']] = $s['name'];
              foreach ($res as &$rs)
                  $rs['source_name'] = isset($sources[$rs['original']]) ? $sources[$rs['original']] : null;
          }
      }
      return $res;
  }

  public function getMaxSort($sid){
      $res = 0;
      $images = $this->getSliderImages($sid);
      if(count($images)){
          $i = array_pop($images);
          $res = $i['sort'] + 1;
      }
      return $res;
  }

}