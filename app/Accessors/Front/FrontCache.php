<?php

namespace App\Accessors\Front;

use App\Accessors\{EventContactAccessor, EventManager\EventGroups, EventManager\SellableAccessor};
use App\Enum\ParticipantType;
use App\Models\{Account, Event, EventContact, EventManager\EventGroup, FrontCart};
use Illuminate\Support\Facades\Log;
use Throwable;

class FrontCache
{
    public static function accountConnected(): bool
    {
        return auth()->check() && ! auth()->user()->hasRole('dev');
    }

    public static function getEventContact(): ?EventContact
    {
        if (session()->has('front_event_contact_id')) {
            return EventContact::find(session('front_event_contact_id'));
        }
        Log::warning("Session missing: front_event_contact_id", [
            'session_data' => session()->all(),
            'trace'        => collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5))
                ->map(fn($trace) => "{$trace['file']}:{$trace['line']} @ {$trace['function']}")
                ->toArray(),
        ]);

        return null;
    }

    /**
     * Full Cached EventContact Model / Non Livewire tasks
     *
     * @return EventContact|null
     */
    public static function getEventContactModel(): ?EventContact
    {
        if ( ! session()->has('front_event_contact_model') || ! (session('front_event_contact_model') instanceof EventContact)) {
            Log::warning("Session missing: front_event_contact_model", ['session_data' => session()->all()]);
            $eventContact = self::getEventContact();
            if ($eventContact) {
                self::setEventContactFromInstance($eventContact);
            }
        }

        return session('front_event_contact_model');
    }

    /**
     * Full Cached Event Model / Non Livewire tasks
     *
     * @return EventContact|null
     */
    public static function getEventModel(): ?Event
    {
        if ( ! session()->has('front_event_model') || ! (session('front_event_model') instanceof Event)) {
            Log::warning("Session missing: front_event_model", ['session_data' => session()->all()]);
            self::setEvent(self::getEventId());
        }

        return session('front_event_model');
    }

    public static function getEventContactById(int $id): ?EventContact
    {
        return EventContact::find($id);
    }

    public static function setCanAccessTransport(): bool
    {
        try {
            if (session()->has("front_event_contact_can_access_transport")) {
                return session("front_event_contact_can_access_transport");
            }

            $group = self::getParticipationTypeGroup();
            $event = self::getEventModel();

            if ( ! $event) {
                self::setEvent(self::getEventContactModel()?->event_id);
                $event = self::getEventModel();
            }

            $canAccess
                = (
                (in_array(ParticipantType::ORATOR->value, (array)$event->transport) && $group == ParticipantType::ORATOR->value)
                || (in_array(ParticipantType::CONGRESS->value, (array)$event->transport) && $group == ParticipantType::CONGRESS->value)
            );

            session()->put("front_event_contact_can_access_transport", $canAccess);

            return $canAccess;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }

    public static function canAccessTransport(): ?bool
    {
        return session("front_event_contact_can_access_transport", false);
    }

    public static function getParticipationType()//: ?ParticipationType
    {
        return session("front_participation_type");
    }

    public static function setEventContact(int $event_id, int $user_id, bool $impersonated = false): void
    {
        $eventContact = EventContactAccessor::getEventContactByEventAndUser($event_id, $user_id);
        self::eventContactToSession($eventContact, $impersonated);
    }

    public static function setEventContactFromInstance(EventContact $eventContact, bool $impersonated = false): void
    {
        self::eventContactToSession($eventContact, $impersonated);
    }

    private static function eventContactToSession(EventContact $eventContact, bool $impersonated = false): void
    {
        session()->forget([
            'front_participation_type',
            'front_event_contact_id',
        ]);

        session([
            'front_participation_type'       => $eventContact?->participationType?->id,
            'front_participation_type_group' => $eventContact?->participationType?->group,
            'front_event_contact_id'         => $eventContact->id,
        ]);

        session()->put("front_event_contact_model", $eventContact);

        session()->put('', self::setCanAccessTransport());
        session()->put('setCanAccessTransport', self::setCanAccessTransport());
        session()->put('nonChoosableServicesCount', SellableAccessor::getEventContactPublishedNonChoosableServicesCount($eventContact->event_id, $eventContact->user_id));


        if ( ! $impersonated) {
            session(['front_event_main_contact' => EventGroups::isAMainContact($eventContact)]);
        }
    }

    public static function setEvent(Event|int $event): void
    {
        $eventId = is_int($event) ? $event : $event->id;
        session()->put("front_event_id", $eventId);

        if ( ! session()->has("front_event_model") || ! (session('front_event_model') instanceof Event)) {
            $fetchedEvent = Event::find($eventId);
            if ($fetchedEvent) {
                session()->put('front_event_model', $fetchedEvent);
            } else {
                Log::error("Failed to set event model in session", ['event_id' => $eventId]);
            }
        }
    }


    public static function getEvent(): ?Event
    {
        return Event::find(self::getEventId());
    }


    public static function getEventId(): int
    {
        return (int)session("front_event_id");
    }

    /*
     * Returns null or the group id
     */
    public static function isEventMainContact(): ?int
    {
        if ( ! session()->has('front_event_main_contact')) {
            session(['front_event_main_contact' => EventGroups::isAMainContact(FrontCache::getEventContactModel())]);
        }

        return session('front_event_main_contact');
    }

    public static function getAccount(): ?Account
    {/*
        if ( ! session()->has('front_account')) {
            session(["front_account_id" => self::getEventContactModel()?->user_id]);
            session(['front_account' => self::getEventContactModel()?->account]);
        }

        return session('front_account');
*/
        return Account::find(self::getEventContact()->user_id);
    }

    public static function getGroupManager(): ?EventContact
    {
        if (session()->has("front_group_manager_id")) {
            return EventContact::find(session("front_group_manager_id"));
        }

        return null;
    }

    public static function isConnectedAsGroupManager(): bool
    {
        return session()->has('front_group_manager_id');
    }

    public static function getGroupManagerParams(string $param = ''): mixed
    {
        $params = session('front_group_manager_params');

        if ( ! is_array($params) or ! $params) {
            return null;
        }

        return $param ? ($params[$param] ?? null) : $params;
    }

    public static function forceSetGroupManager(): ?EventContact
    {
        $eventContact = self::getEventContact();
        if ($eventContact) {
            if (FrontCart::where('group_manager_event_contact_id', $eventContact->id)->exists()) {
                session(['front_group_manager' => $eventContact]);
            }
        }

        return session('front_group_manager');
    }

    public static function setGroupManager(array $options = []): void
    {
        if (self::isEventMainContact()) {
            session([
                'front_group_manager_id'     => self::getEventContact()->id,
                'front_group_manager_params' => $options,
            ]);
        }
    }

    public static function forgetGroupManager(): void
    {
        session()->forget("front_group_manager_id");
    }

    public static function getEventGroup(): ?EventGroup
    {
        if (session()->has("front_eventgroup")) {
            return session("front_eventgroup");
        }
        session(['front_eventgroup' => EventGroups::getGroupByMainContact(FrontCache::getEventModel(), auth()->user())]);

        return session('front_eventgroup');
    }

    public static function getNonChoosableServicesCount(): int
    {
        return session('nonChoosableServicesCount', 0);
    }

    private static function getParticipationTypeGroup(): string
    {
        return session('front_participation_type_group');
    }

    public static function setOldGroupManager(): void
    {
        $currentGroupManager = self::getGroupManager();
        if ($currentGroupManager) {
            session(['old_group_manager_id' => $currentGroupManager->id]);
            session(['old_group_manager_params' => session('front_group_manager_params')]);
        }
    }

    public static function getOldGroupManager(): ?EventContact
    {
        return EventContact::find(session('old_group_manager_id'));
    }

    public static function restoreOldGroupManager(): void
    {
        $oldGroupManager = self::getOldGroupManager();
        if ($oldGroupManager) {
            session([
                'front_group_manager_id'     => $oldGroupManager->id,
                'front_group_manager_params' => session('old_group_manager_params'),
            ]);
            session()->forget(['old_group_manager_id', 'old_group_manager_params']);
        }
    }

}
