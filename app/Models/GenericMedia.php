<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MetaFramework\Mediaclass\Interfaces\MediaclassInterface;
use MetaFramework\Mediaclass\Traits\Mediaclass;

class GenericMedia extends Model implements MediaclassInterface
{
    use Mediaclass;

    public function mediaclassSettings(): array {
        return [
            'banner_large' => [
                'width'     => 1270,
                'height'    => 140,
                'label'     => 'Bannière Large',
                'cropable' => true
            ],
            'banner_medium' => [
                'width'     => 510,
                'height'    => 140,
                'label'     => 'Bannière Medium',
                'cropable' => true
            ],
            'thumbnail' => [
                'width'     => 600,
                'height'    => 380,
                'label'     => 'Image carrée',
                'cropable' => true
            ],
        ];
    }
}
