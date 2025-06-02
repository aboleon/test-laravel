<?php

namespace App\Traits;

use App\Models\Event;
use App\Models\GenericMedia;
use Mediaclass;
use MetaFramework\Mediaclass\Parser;
use MetaFramework\Mediaclass\Printer;

trait EventCommons
{

    public function getBanner(Event $event, string $group = 'banner_medium'): ?string
    {
        if ($event->media->isNotEmpty()) {
            $media = $event->media->filter(fn($m) => $m->group == $group)->first();
            if ($media) {
                return str_replace(config('app.url'), '', (new Printer(new Parser($media)))->url('cropped'));
            }
        }

        return str_replace(config('app.url'), '', Mediaclass::ghostUrl(GenericMedia::class, $group));
    }
}
