<?php

namespace App\Http\Controllers\Front;

use App\Accessors\Front\FrontCache;
use App\Generators\Seo;
use App\Models\Event;
use MetaFramework\Traits\Responses;

class EventController
{
    use Responses;
    public function event(string $locale, Event $event, string $slug)
    {
        if (!$event->published) {
            $this->responseError(__('front/errors.unpublished_event'));
            $this->flashResponse();
            return redirect()->route('front.home', ['locale' => $locale]);
        }


        if (auth()->check()) {
            $eventContact = FrontCache::getEventContactModel();
            if ($eventContact && $eventContact->user_id ==auth()->id()) {
                return redirect()->route('front.event.dashboard', [
                    'locale' => $locale,
                    'event' => $event,
                ]);
            }
        }

        Seo::generator(
            title: "Divine ID - " . $slug,
            description: $event->texts->description,
        );
        return view('front.event', [
            'event' => $event,
        ]);
    }
}
