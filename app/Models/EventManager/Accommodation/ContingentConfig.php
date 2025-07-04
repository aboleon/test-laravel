<?php

namespace App\Models\EventManager\Accommodation;

use App\Interfaces\SageInterface;
use App\Models\Event;
use App\Traits\SageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ContingentConfig extends Model implements SageInterface
{
    use HasFactory;
    use SageTrait;

    public $timestamps = false;
    protected $table = 'event_accommodation_contingent_config';
    protected $guarded = [];

    public function roomGroup(): BelongsTo
    {
        return $this->belongsTo(RoomGroup::class, 'group_room_id');
    }

    public function rooms(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function contingent(): BelongsTo
    {
        return $this->belongsTo(Contingent::class, 'contingent_id');
    }

    public function sageFields(): array
    {
        return [
            'code_article' => 'Référence'
        ];
    }

    public function getSageEvent(): ?Event
    {
        return $this->contingent->accommodation->event;
    }
    public function getSageDatabaseId(): string
    {
        return str_pad(Str::substr($this->id, 0, 5), 5, '0', STR_PAD_LEFT);
    }

    public function getSageReferenceValue(): string
    {
        if ($this->sageReference === null) {
            $this->sageReference = (string)$this->sageData->where('name', 'code_article')->value('value') ?: $this->defaultSageReferenceValue();
        }

        return $this->sageReference;
    }

    public function defaultSageReferenceValue(): string
    {
        return 'ROOM';
    }

}
