<?php

namespace App\Abstract;

use App\Interfaces\GrantLocationInterface;
use Illuminate\Database\Eloquent\Model;

class GrantLocationAbstract extends Model implements GrantLocationInterface
{
    /**
     * @return array
     * example:
     * ['field_name' => 'field_type']
     */
    public function fields(): array
    {
        return [];
    }

}
