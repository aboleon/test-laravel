<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\EventAccessor;
use App\Accessors\EventContactAccessor;
use App\Accessors\Front\Program\Interventions;
use App\Accessors\Front\Sellable\Accommodation;
use App\Accessors\Front\Sellable\Invitations;
use App\Accessors\Front\Sellable\Service;
use App\Accessors\Front\Transport\Transports;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use App\Models\EventContact;
use Exception;
use Illuminate\Contracts\Support\Renderable;

class DashboardController extends EventBaseController
{
    /**
     * @throws Exception
     */
    public function index(string $locale, Event $event): Renderable
    {
        $eventAccessor        = new EventAccessor($event);
        $eventContactAccessor = (new EventContactAccessor())->setEventContact($this->eventContact);

        Seo::generator(__('front/seo.dashboard_title'));

        return view('front.user.dashboard', [
            'event'                => $event,
            'eventContact'         => $this->eventContact,
            'eventContactAccessor' => $eventContactAccessor,
            'orderAmount'          => $eventContactAccessor->getAllRemainingPayments(), // Remaining unpaid orders
            'moderatorOratorItems' => Interventions::getOratorModeratorItems($this->eventContact),
            'invitationItems'      => Invitations::getItems($event, $this->eventContact),
            'serviceItems'         => Service::getServiceItems($this->eventContact),
            'accommodationItems'   => Accommodation::getAccommodationItems($this->eventContact),
            'transportItems'       => Transports::getDashboardItems($event, $this->eventContact),
            'eventAccessor'        => $eventAccessor,
            'eventTimeline'        => $eventAccessor->timeline(),
            'grantDeposit'         => $eventContactAccessor->paidGrantDeposit(),
        ]);
    }

    public function groupManagementDemand(string $locale, Event $event, EventContact $eventContact): Renderable
    {
        return view('front.user.dashboard-not-main-contact-yet', [
            'event'        => $event,
            'eventContact' => $eventContact,
        ]);
    }
}
