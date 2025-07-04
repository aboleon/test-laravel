<?php

namespace App\Interfaces;

use App\Models\Event;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface SageInterface
{
    public function sageFields(): array;

    public function sageData(): MorphMany;

    public function syncSageData(): void;
    public function getSageCode(): string;
    public function getSageEvent(): ?Event;
    public function getSageReferenceValue(): string;
    public function defaultSageReferenceValue(): string;

}
