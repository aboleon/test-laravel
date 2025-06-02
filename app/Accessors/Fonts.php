<?php

namespace App\Accessors;

class Fonts
{
    public static function getFrontFontsSelectable(){
        return [
            'Arial' => 'Arial',
            'Arial Black' => 'Arial Black',
            'Comic Sans MS' => 'Comic Sans MS',
            'Courier New' => 'Courier New',
            'Georgia' => 'Georgia',
            'Helvetica' => 'Helvetica',
            'Impact' => 'Impact',
            'Lucida Grande' => 'Lucida Grande',
            'Palatino' => 'Palatino',
            'Tahoma' => 'Tahoma',
            'Times New Roman' => 'Times New Roman',
            'Trebuchet MS' => 'Trebuchet MS',
            'Verdana' => 'Verdana',
        ];
    }
}