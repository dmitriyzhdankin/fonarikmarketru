<?php
return array(
    'shop_custommenu' => array(
        'menu_id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'name' => array('varchar', 255, 'null' => 0),
        ':keys' => array(
            'PRIMARY' => 'menu_id',
        ),
    ),
    'shop_custommenu_item' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'menu_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        'parent_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        'title' => array('varchar', 255, 'null' => 0),
        'url' => array('varchar', 255, 'null' => 0),
        'column' => array('int', 11, 'null' => 0, 'default' => '0'),
        'sort' => array('int', 11, 'null' => 0, 'default' => '0'),
        'type' => array('varchar', 255, 'null' => 0),
        ':keys' => array(
            'PRIMARY' => 'id',
            'sort' => 'sort',
        ),
    ),
);

/**
CREATE TABLE IF NOT EXISTS `shop_custommenu_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `column` int(11) NOT NULL,
  `sort` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
 */