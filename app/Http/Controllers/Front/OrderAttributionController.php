<?php

namespace App\Http\Controllers\Front;

use App\Accessors\Front\FrontCache;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Traits\AttributionTrait;
use Illuminate\Contracts\Support\Renderable;

class OrderAttributionController extends Controller
{
    use AttributionTrait;

    public function index(string $locale, Event $event)
    {
        $this->checkFrontGroup();

        $this->initGroupAccessor();
        $this->groupAccessor->setEvent($event);


        return view('front.orders.attributions.recap')->with([
            'accommodation'             => collect($this->groupAccessor->summarizedAccommodationQuery())->reject(function ($item) {
                if (empty($item->configs)) {
                    return false;
                }
                $configs = is_array($item->configs) ? (object) $item->configs : json_decode($item->configs);

                return isset($configs->cant_attribute) && $configs->cant_attribute == "1";
            })->groupBy(['event_hotel_id', 'date']),
            'accommodationAttributions' => $event->accommodationAttributions,
            'services'                  => collect($this->groupAccessor->summarizedServiceQuery())->reject(function ($item) {
                if (empty($item->configs)) {
                    return false;
                }
                $configs = is_array($item->configs) ? (object) $item->configs : json_decode($item->configs);

                return isset($configs->cant_attribute) && $configs->cant_attribute == "1";
            }),
            'event_services'            => $event->sellableService->load('event.services'),
        ]);
    }

    public function edit(string $locale, Event $event, string $type): Renderable
    {
        $this->type   = $type;
        $this->event  = $event;
        $this->locale = $locale;

        $this->checkFrontGroup();

        $this->initGroupAccessor();
        $this->groupAccessor->setEvent($event);

        $this->setGroupMembers();

        $viewData = array_merge(
            $this->baseViewData(),
            [
                'group' => FrontCache::getEventGroup(),
            ],
        );

        if (method_exists($this, $type.'Data')) {
            $viewData = array_merge($viewData, $this->accommodationData());
        }

        return view('front.orders.attributions.dispatch')->with($viewData);
    }

    protected function serviceData(): array
    {
        return [
            'ordered' => collect($this->groupAccessor->summarizedServiceQuery()),
        ];
    }


    private function accommodationData(): array
    {
        $this->ordered = collect($this->groupAccessor->summarizedAccommodationQuery())->groupBy(['event_hotel_id', 'date']);

        return array_merge(
            [
                'ordered' => $this->ordered,
            ],
            $this->getAccommodationViewData(),
        );
    }

    private function checkFrontGroup()
    {
        if ( ! FrontCache::getEventGroup()) {
            abort(404, "Le groupe pour cette commande ne peut pas être identifié.");
        }
    }

}
