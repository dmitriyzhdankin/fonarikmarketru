<?php
return array(
    'dislider_images' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'sID' => array('int', 11, 'null' => 0, 'default' => '0'),
        'original' => array('int', 11, 'null' => 0, 'default' => '0'),
        'name' => array('varchar', 255, 'null' => 0, 'default' => ''),
        'ext' => array('varchar', 10, 'null' => 0, 'default' => ''),
        'size' => array('int', 11, 'null' => 0, 'default' => '0'),
        'width' => array('int', 5, 'null' => 0, 'default' => '0'),
        'height' => array('int', 5, 'null' => 0, 'default' => '0'),
        'title' => array('varchar', 255, 'null' => 0, 'default' => ''),
        'link' => array('varchar', 255, 'null' => 0, 'default' => ''),
        'description' => array('text'),
        'created' => array('datetime', 'null' => 0),
        'sort' => array('int', 11, 'null' => 0, 'default' => '0'),
        ':keys' => array(
            'PRIMARY' => 'id',
            'sID' => 'sID',
        ),
    ),
    'dislider_sliders' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'title' => array('varchar', 255, 'null' => 0, 'default' => ''),
        'width' => array('int', 11, 'null' => 0, 'default' => '1'),
        'height' => array('int', 11, 'null' => 0, 'default' => '1'),
        'itype' => array('varchar', 255, 'null' => 0, 'default' => 'Skitter'),
        'created' => array('datetime', 'null' => 0),
        'params' => array('text'),
        ':keys' => array(
            'PRIMARY' => 'id',
            'itype' => 'itype',
        ),
    ),
);