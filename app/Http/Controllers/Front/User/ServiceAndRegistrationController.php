<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\EventManager\EventServices;
use App\Accessors\EventManager\SellableAccessor;
use App\Accessors\Front\FrontCache;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class ServiceAndRegistrationController extends EventBaseController
{
    public function dashboard(string $locale, Event $event)
    {
        Seo::generator(__('front/seo.service_registration_title'));

        $account = FrontCache::getAccount();
        $profile = $account->profile;
        $eventContact = FrontCache::getEventContact();

        $services = SellableAccessor::getFrontAvailableServices($event, $eventContact);

        $allowedGroupedServices = $this->getAllowedGroupedServices($event, $services, $eventContact->participation_type_id, $profile->profession?->id);

        return view('front.user.service_and_registration', [
            "event" => $event,
            'eventContact' => $eventContact,
            "allowedGroupedServices" => $allowedGroupedServices
        ]);
    }

    private function getAllowedGroupedServices($event, $services, $participationTypeId, $professionId)
    {
        $groupNameToId = [];
        $groupedServices = $services
            ->groupBy(function ($item) use (&$groupNameToId) {
                $groupNameToId[$item->group->name] = $item->group->id;
                return $item->group->name;
            })
            ->map(function ($group) use ($participationTypeId, $professionId) {
                return $group->filter(function ($service) use ($participationTypeId, $professionId) {
                    // Check if both professions and participations are empty
                    $hasProfessions = !empty($service->professions);
                    $hasParticipations = !empty($service->participations);

                    if (!$hasProfessions && !$hasParticipations) {
                        return true;
                    }

                    $includeService = false;

                    if ($hasParticipations) {
                        foreach ($service->participations as $participation) {
                            if ($participation['id'] == $participationTypeId) {
                                $includeService = true;
                                break;
                            }
                        }
                    }

                    if ($professionId !== null && $hasProfessions) {
                        foreach ($service->professions as $profession) {
                            if ($profession['id'] == $professionId) {
                                $includeService = true;
                                break;
                            }
                        }
                    }

                    return $includeService;
                })
                    ->sortBy(function ($item) {
                        return !$item->service_group_combined ? 0 : 1;
                    });
            });

        $groupNameToPosition = $this->getGroupNameToPosition($event, $groupNameToId);

        return $groupedServices->sortBy(function ($items, $key) use ($groupNameToPosition) {
            return $groupNameToPosition[$key] ?? PHP_INT_MAX;
        });
    }

    private function getGroupNameToPosition($event, $groupNameToId)
    {
        $groupNameToPosition = [];
        foreach ($groupNameToId as $name => $id) {
            $serviceConfig = EventServices::getEventService($event, $id);
            $position = $serviceConfig?->fo_family_position;
            $groupNameToPosition[$name] = $position;
        }
        return $groupNameToPosition;
    }
}


