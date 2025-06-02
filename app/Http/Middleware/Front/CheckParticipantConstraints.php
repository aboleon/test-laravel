<?php

namespace App\Http\Middleware\Front;

use App\Accessors\Front\FrontCache;
use App\Enum\RegistrationType;
use App\Generators\Seo;
use App\Http\Controllers\Front\Auth\AuthenticatedSessionController;
use App\Models\Event;
use App\Models\EventContact;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use MetaFramework\Traits\Responses;

class CheckParticipantConstraints
{
    use Responses;

    public function handle(Request $request, Closure $next)
    {
        # Log::info("CheckParticipantConstraints starts, event id is ".FrontCache::getEvent()?->id);

        // Ensure the user is logged in
        if (!auth()->check()) {
            return $next($request);
        }

        if (auth()->user()->hasRole('dev')) {
            return (new AuthenticatedSessionController())->destroy($request);
        }

        if ($request->routeIs('front.event.logout')) {
            # Log::info("CheckParticipantConstraints LoggingOut");
            return $next($request);
        }

        $cachedEvent  = FrontCache::getEvent();
        $eventContact = FrontCache::getEventContact();

        if ( ! $cachedEvent?->id or ! $eventContact?->id) {
            $this->responseError(__('front/errors.missing_event_or_contact'));
            $this->flashResponse();

            Log::warning("Failed to match event or event contact", ['cachedEvent' => $cachedEvent?->id, 'eventContact' => $eventContact?->id]);

            return (new AuthenticatedSessionController())->destroy($request);
        }

        $routeEvent = $request->route('event');

        # Against URL manipulation
        if ($cachedEvent && $routeEvent && $cachedEvent->id !== $routeEvent->id) {
            $this->responseError(__('front/errors.mismatching_event'));
            $this->flashResponse();

            Log::warning("Failed to matche event or event contact with routeEvent", ['cachedEvent' => $cachedEvent?->id, 'eventContact' => $eventContact?->id, 'routeEventId' => $routeEvent?->id,'routeEvent' => $routeEvent]);
            return (new AuthenticatedSessionController())->destroy($request);
        }

        # Log::info("CheckParticipantConstraints EventContact is #".$eventContact->id.', user_id is #'.$eventContact->user_id.', auth user id is #'.auth()->id());

        View::share([
            'eventContact'                => $eventContact,
            'eventGroup'                  => FrontCache::getEventGroup(),
            'event'                       => $cachedEvent,
            'account'                     => FrontCache::getAccount(),
            'participationType'           => FrontCache::getParticipationType(),
            'isConnectedAsManager'        => FrontCache::isConnectedAsGroupManager(),
            'groupManager'                => FrontCache::getGroupManager(),
            'isMainContact'               => FrontCache::isEventMainContact(),
            'locale'                      => app()->getLocale(),
        ]);


        if ( ! FrontCache::getParticipationType()) {
            Log::info("CheckParticipantConstraints No participationType");

            return $this->renderNoParticipationType($cachedEvent, $eventContact);
        }

        // Check if the user is cached as a main contact
        if ($eventContact->registration_type == RegistrationType::GROUP->value && ! FrontCache::isEventMainContact()) {
            Log::info("CheckParticipantConstraints No EventMainContact");

            return $this->renderNotMainContactYet($cachedEvent, $eventContact);
        }

        Log::info('Session Data:', [session('_token')]);

        return $next($request);
    }

    protected function renderNoParticipationType(Event $event, EventContact $eventContact): Response
    {
        $registrationType = $eventContact->registration_type;
        if (
            RegistrationType::GROUP->value === $registrationType
            || RegistrationType::LOGIN->value === $registrationType
        ) {
            $registrationType = null;
        }

        Seo::generator(__('front/seo.dashboard_title'));

        return response()->view('front.user.dashboard-no-participation-type-yet', [
            'event'             => $event,
            'participationType' => null,
            'registrationType'  => $registrationType,
        ]);
    }

    protected function renderNotMainContactYet(Event $event, EventContact $eventContact): Response
    {
        Seo::generator(__('front/seo.dashboard_title'));

        return response()->view('front.user.dashboard-not-main-contact-yet', [
            'event'        => $event,
            'eventContact' => $eventContact,
        ]);
    }
}
