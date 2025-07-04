<?php

namespace App\Traits;

use App\Models\Event;
use App\Models\Sage;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Throwable;

trait SageTrait
{

    private ?string $sageEventAcronym = null;
    private ?string $sageYear = null;
    private ?string $sageReference = null;
    private ?string $sageAnalyticsCode = null;


    public function sageData(): MorphMany
    {
        return $this->morphMany(Sage::class, 'model');
    }

    /**
     * @return void
     *  Store Sage Data
     */
    public function syncSageData(): void
    {
        $this->sageData()->delete();

        $sageData = (array)request('sage');
        $models   = [];

        $sageData = array_filter($sageData);

        if ( ! $sageData) {
            return;
        }

        foreach ($sageData as $code => $value) {
            $models[] = new Sage([
                'name'  => (string)$code,
                'value' => (string)$value,
            ]);
        }

        $this->sageData()->saveMany($models);
    }

    /**
     * @return void
     *  Store Sage Data
     */
    public function syncFromPlural(string $key, int $model_id): void
    {
        $value = request('sage.'.$key.'.'.$model_id) ?: $this->defaultSageReferenceValue();

        Sage::updateOrCreate(
            [
                'model_id'   => $model_id,
                'model_type' => static::class,
                'name'       => $key,
            ],
            [
                'value'    => $value,
                'model_type' => static::class,
                'model_id' => $model_id,
            ],
        );
    }

    public function getSageCode(): string
    {
        return $this->getSageEventAcronym().$this->getSageYear().$this->getSageDatabaseId();
    }


    public function getSageEventAcronym(): string
    {
        if ($this->sageEventAcronym === null) {
            try {
                $eventCode = $this->event->sageData->where('name', 'code_event')->value('value');

                if ( ! $eventCode) {
                    $eventCode = $this->event->texts->subname ?: $this->event->texts->name;
                }

                $this->sageEventAcronym = Str::upper(Str::substr(Str::slug($eventCode, ''), 0, 3));
            } catch (Throwable) {
                $this->sageEventAcronym = 'EVN';
            }
        }

        return $this->sageEventAcronym;
    }

    public function getSageYear(): string
    {
        if ($this->sageYear === null) {
            try {
                $this->sageYear = Carbon::parse($this->getSageEvent()->getRawOriginal('starts'))->format('y');
            } catch (Throwable) {
                $this->sageYear = 'YR';
            }
        }

        return $this->sageYear;
    }

    /**
     * @return string
     * Requis que l'ID de l'entrée en DB (ID) soit de quatre digits, va savoir pourquoi...
     * Problème d'ID tronqué à partir de 9999 +1
     * On est bête et discipliné, on fait...
     */
    public function getSageDatabaseId(): string
    {
        return str_pad(Str::substr($this->id, 0, 4), 4, '0', STR_PAD_LEFT);
    }

    public function getSageReferenceValue(string $name='code_article'): string
    {
        return (string)$this->sageData->where('name', $name)->value('value') ?: $this->defaultSageReferenceValue();

    }

    public function defaultSageReferenceValue(): string
    {
        return '';
    }

    public function getSageEvent(): ?Event
    {
        return null;
    }

    public function getSageAnalyticsCode(): string
    {
        if ($this->sageAnalyticsCode === null) {
            $event = $this->getSageEvent();
            if ( ! $event) {
                $this->sageAnalyticsCode = '';
            }

            $this->sageAnalyticsCode = (string)$event->sageData->where('name', 'code_stats')->value('value');
        }

        return $this->sageAnalyticsCode;
    }

}
