<?php

return array(
    'theme'=>array(
        'title'=>'Theme',
        'type'=>'select',
        'values'=>array(
            'Minimalist'=>'minimalist', 'Round'=>'round', 'Clean'=>'clean', 'Square'=>'square'
        )
    ),
    'animation'=>array(
        'title'=>'Animation type',
        'type'=>'select',
        'values'=>array(
            'Cube'=>'cube', 'CubeRandom'=>'cubeRandom', 'Block'=>'block',
            'CubeStop'=>'cubeStop', 'CubeHide'=>'cubeHide', 'CubeSize'=>'cubeSize',
            'Horizontal'=>'horizontal', 'ShowBars'=>'showBars',
            'ShowBarsRandom'=>'showBarsRandom', 'Tube'=>'tube', 'Fade'=>'fade',
            'FadeFour'=>'fadeFour', 'Paralell'=>'paralell', 'Blind'=>'blind',
            'BlindHeight'=>'blindHeight', 'BlindWidth'=>'blindWidth',
            'DirectionTop'=>'directionTop', 'DirectionBottom'=>'directionBottom',
            'DirectionRight'=>'directionRight', 'DirectionLeft'=>'directionLeft',
            'CubeStopRandom'=>'cubeStopRandom', 'CubeSpread'=>'cubeSpread',
            'CubeJelly'=>'cubeJelly', 'GlassCube'=>'glassCube', 'GlassBlock'=>'glassBlock',
            'Circles'=>'circles', 'CirclesInside'=>'circlesInside',
            'CirclesRotate'=>'circlesRotate', 'CubeShow'=>'cubeShow', 'UpBars'=>'upBars',
            'DownBars'=>'downBars', 'HideBars'=>'hideBars', 'SwapBars'=>'swapBars',
            'SwapBarsBack'=>'swapBarsBack', 'SwapBlocks'=>'swapBlocks', 'Cut'=>'cut',
            'Random'=>'random', 'RandomSmart'=>'randomSmart'
        )
    ),
    'show_randomly'=>array(
        'title'=>'Show slides randomly',
        'type'=>'checkbox',
        'default'=>''
    ),
    'auto_play'=>array(
        'title'=>'Auto play',
        'type'=>'checkbox',
        'default'=>'1',
        'show_nulls'=>'1'
    ),
    'stop_over'=>array(
        'title'=>'Stop on mouse over',
        'type'=>'checkbox',
        'default'=>'1',
        'show_nulls'=>'1'
    ),
    'velocity'=>array(
        'title'=>'Animation speed',
        'type'=>'select',
        'values'=>array(
            'Very slow'=>'1', 'Slow'=>'5', 'Normal'=>'10', 'Fast'=>'15', 'Very fast'=>'20'
        )
    ),
    'interval'=>array(
        'title'=>'Time interval (ms)',
        'type'=>'text',
        'size'=>'5',
        'maxlenght'=>'6',
        'default'=>'2500'
    ),
    'progressbar'=>array(
        'title'=>'Show progressbar',
        'type'=>'checkbox',
        'default'=>''
    ),
    'enable_navigation_keys'=>array(
        'title'=>'Enable arrow keys',
        'type'=>'checkbox',
        'default'=>''
    ),
    'navigation'=>array(
        'title'=>'Navigation buttons',
        'type'=>'checkbox',
        'default'=>'1',
        'show_nulls'=>'1'
    ),
    'hideTools'=>array(
        'title'=>'Hide navigation tools',
        'type'=>'checkbox',
        'default'=>''
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
        'default'=>''
    ),
    'numbers'=>array(
        'title'=>'Show numbers',
        'type'=>'radio',
        'group'=>'second_nav',
        'default'=>'1',
        'show_nulls'=>'1'
    ),
    'thumbs'=>array(
        'title'=>'Show thumbs',
        'type'=>'radio',
        'group'=>'second_nav',
        'default'=>''
    ),
    'numbers_align'=>array(
        'title'=>'Secondary navigation align',
        'type'=>'select',
        'values'=>array(
            'Left'=>'left', 'Center'=>'center', 'Right'=>'right'
        )
    )
);
