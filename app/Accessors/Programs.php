<?php

namespace App\Accessors;

use App\Models\BankAccount;
use App\Models\Event;
use App\Models\EventManager\Program\EventProgramDayRoom;
use App\Models\EventManager\Program\EventProgramIntervention;
use App\Models\EventManager\Program\EventProgramSession;
use DateTime;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Cache;

class Programs
{


    public static string $signature = 'programs';


    public static function getDayRoomsSelectable($eventId): array
    {
        return EventProgramDayRoom::where('event_id', $eventId)->get()->mapWithKeys(function ($dayRoom) {
            return [
                $dayRoom->id => $dayRoom->datetime_start->format(config('app.date_display_format') . " H:i") . " (" . $dayRoom->room->place->name . " > " . $dayRoom->room->name . ')',
            ];
        })->toArray();
    }

    public static function getSessionsSelectable($eventId, $withDate = true): array
    {
        if ($withDate) {
            $sessionWithDays = [];
            Event::find($eventId)->programSessions->each(function ($session) use (&$sessionWithDays) {
                $r = $session->programDay->room;
                $date = $session->programDay->datetime_start->format(config('app.date_display_format'));
                $sessionWithDays[$session->id] = $session->name . " (" . $date . " - " . $r->name . ")";
            });
            return $sessionWithDays;
        }

        return Event::find($eventId)->programSessions->pluck('name', 'id')->toArray();
    }

    public static function getOratorsSelectable($eventId): array
    {
        return Places::selectableArray();
    }


    public static function getOrganizerPrintViewCollection(Event $event)
    {
        return $event->programDays()
            ->with([
                'sessions' => function ($query) {
                    $query->orderBy('position')
                        ->with([
                            'interventions' => function ($subQuery) {
                                $subQuery->orderBy('position');
                            }
                        ]);
                }
            ])
            ->orderBy('datetime_start')
            ->get();
    }

    public static function getOrganizerPrintViewCollectionByInterventions(array $interventionIds)
    {
        // Fetch interventions that match the provided IDs
        $interventions = EventProgramIntervention::whereIn('id', $interventionIds)
            ->with('session.programDay.event')
            ->get();

        // Extract unique session IDs and day IDs from these interventions
        $sessionIds = $interventions->pluck('session.id')->unique()->toArray();
        $dayIds = $interventions->pluck('session.programDay.id')->unique()->toArray();

        // Now, retrieve the days with their associated sessions and interventions
        return EventProgramDayRoom::whereIn('id', $dayIds)
            ->with([
                'sessions' => function ($query) use ($sessionIds, $interventionIds) {
                    $query->whereIn('id', $sessionIds)
                        ->orderBy('position')
                        ->with([
                            'interventions' => function ($subQuery) use ($interventionIds) {
                                $subQuery->whereIn('id', $interventionIds)
                                    ->orderBy('position');
                            }
                        ]);
                }
            ])
            ->orderBy('datetime_start')
            ->get();
    }



    public static function resetCache(): void
    {
        Cache::forget(self::$signature);
    }
}
