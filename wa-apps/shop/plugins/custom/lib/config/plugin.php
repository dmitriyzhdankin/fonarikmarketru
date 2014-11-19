<?php

return array(
    'name' => 'Плагин кастомизации Shop Script 5',
    'description' => 'Плагин кастомизации Shop Script 5 для fonarik',
    'vendor' => '992482',
    'version' => '1.0.0',
    'img' => '',
    'shop_settings' => true,
    'icons' => array(
        16 => '',
    ),
    'handlers' => array(
      'backend_menu' => 'addBackendSettingsScript',
      'backend_product' => 'addCategoriesForProduct',
      'backend_products' => 'addCopyProductsButton',
    ),
);
