<?php

return array(
    'theme'=>array(
        'title'=>'Theme',
        'type'=>'select',
        'values'=>array(
            'Metallic'=>'metallic',
            'Minimalist'=>'minimalist-round',
            'Construction'=>'construction',
            'CS-Portfolio'=>'cs-portfolio'
        )
    ),
    'mode'=>array(
        'title'=>'Slider mode',
        'type'=>'select',
        'values'=>array(
            'Horizontal'=>'h',
            'Vertical'=>'v',
            'Fade'=>'f'
        )
    ),
    'playRtl'=>array(
        'title'=>'Slider direction',
        'type'=>'select',
        'values'=>array(
            'Forward'=>'0', 'Backward'=>'1'
        ),
    ),
    'autoPlay'=>array(
        'title'=>'Auto play',
        'type'=>'checkbox',
        'default'=>'1',
        'show_nulls'=>'1'
    ),
    'pauseOnHover'=>array(
        'title'=>'Stop on mouse over',
        'type'=>'checkbox',
        'default'=>'1'
    ),
    'easing'=>array(
        'title'=>'Animation effect',
        'type'=>'select',
        'values'=>array(
            'linear'=>'linear',
            'easeOutElastic'=>'easeOutElastic',
            'easeInOutElastic'=>'easeInOutElastic',
            'easeOutBounce'=>'easeOutBounce',
            'easeOutBack'=>'easeOutBack',
            'easeInBack'=>'easeInBack',
            'easeInOutBack'=>'easeInOutBack',
            'easeInQuart'=>'easeInQuart',
            'easeOutQuart'=>'easeOutQuart'
        )
    ),
    'animationTime'=>array(
        'title'=>'Animation speed',
        'type'=>'select',
        'values'=>array(
            'Very slow'=>'3000', 'Slow'=>'1500', 'Normal'=>'800', 'Fast'=>'300', 'Very fast'=>'100'
        )
    ),
    'delay'=>array(
        'title'=>'Time interval (ms)',
        'type'=>'text',
        'size'=>'5',
        'maxlenght'=>'6',
        'default'=>'3000'
    ),
    'buildArrows'=>array(
        'title'=>'Navigation buttons',
        'type'=>'checkbox',
        'default'=>'1'
    ),
    'toggleArrows'=>array(
        'title'=>'Hide on mouse over',
        'type'=>'checkbox',
        'default'=>'1'
    ),
    'enableKeyboard'=>array(
        'title'=>'Enable arrow keys',
        'type'=>'checkbox',
        'default'=>'0'
    ),
    'spacer_second_nav'=>array(
        'title'=>'Secondary navigation',
        'type'=>'spacer'
    ),
    'buildNavigation'=>array(
        'title'=>'Show dots',
        'type'=>'checkbox',
        'default'=>'0'
    ),
    'toggleControls'=>array(
        'title'=>'Hide on mouse over',
        'type'=>'checkbox',
        'default'=>'0'
    ),
    'spacer_descs'=>array(
        'title'=>'Titles & Descriptions',
        'type'=>'spacer'
    ),
    'descPosition'=>array(
        'title'=>'Position of Title & Description block',
        'type'=>'select',
        'values'=>array(
            'Do not show'=>'0', 'Top'=>'desc-top', 'Left'=>'desc-left',
            'Right'=>'desc-right', 'Bottom'=>'desc-bottom'
        )
    ),
    'expand'=>array(
        'type'=>'hidden',
        'value'=>'1'
    )
);