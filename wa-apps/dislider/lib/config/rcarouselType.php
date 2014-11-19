<?php

return array(
    'main_class'=>array(
        'title'=>'Theme',
        'type'=>'select',
        'values'=>array(
            'Orange'=>'rcarousel-orange', 'Blue'=>'rcarousel-blue'
        )
    ),
    'orientation'=>array(
        'title'=>'Slider mode',
        'type'=>'select',
        'values'=>array(
            'Horizontal'=>'horizontal', 'Vertical'=>'vertical'
        )
    ),
    'direction'=>array(
        'title'=>'Slider direction',
        'type'=>'select',
        'values'=>array(
            'Forward'=>'next', 'Backward'=>'prev'
        )
    ),
    'enabled'=>array(
        'title'=>'Auto play',
        'type'=>'checkbox',
        'default'=>'1'
    ),
    'interval'=>array(
        'title'=>'Time interval (ms)',
        'type'=>'text',
        'size'=>'5',
        'maxlenght'=>'6',
        'default'=>'5000'
    ),
    'speed'=>array(
        'title'=>'Animation speed',
        'type'=>'select',
        'values'=>array(
            'Very slow'=>'2000', 'Slow'=>'1000', 'Normal'=>'600', 'Fast'=>'300', 'Very fast'=>'100'
        )
    ),
    'visible'=>array(
        'title'=>'Visible slides',
        'type'=>'text',
        'size'=>'3',
        'maxlenght'=>'4',
        'default'=>'3'
    ),
    'step'=>array(
        'title'=>'Offset (in one step)',
        'type'=>'text',
        'size'=>'3',
        'maxlenght'=>'4',
        'default'=>'1'
    ),
    'margin'=>array(
        'title'=>'Margin between slides (px)',
        'type'=>'text',
        'size'=>'3',
        'maxlenght'=>'4',
        'default'=>'0'
    ),
    'navigation'=>array(
        'title'=>'Navigation buttons',
        'type'=>'checkbox',
        'default'=>'1'
    ),
    'spacer_second_nav'=>array(
        'title'=>'Secondary navigation',
        'type'=>'spacer'
    ),
    'noname'=>array(
        'title'=>'Do not show',
        'type'=>'radio',
        'group'=>'second_nav',
        'default'=>'',
        'unset'=>'1'
    ),
    'dots'=>array(
        'title'=>'Show dots',
        'type'=>'radio',
        'group'=>'second_nav',
        'default'=>'1'
    ),
    'numbers'=>array(
        'title'=>'Show numbers',
        'type'=>'radio',
        'group'=>'second_nav',
        'default'=>''
    )
);
