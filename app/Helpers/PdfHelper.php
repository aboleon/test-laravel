<?php

namespace App\Helpers;

class PdfHelper
{

    public static function imageToBase64($path){
        $imagePath =  public_path($path);
        $imageData = file_get_contents($imagePath);

        $base64Image = base64_encode($imageData);
        $src = 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . $base64Image;

        return $src;
    }
}
