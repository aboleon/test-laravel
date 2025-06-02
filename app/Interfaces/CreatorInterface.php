<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface CreatorInterface
{
    public function hasCreator(): bool;

    public function getCreator();
}
