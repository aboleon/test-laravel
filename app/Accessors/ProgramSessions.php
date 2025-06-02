<?php

namespace App\Accessors;

use App\Accessors\EventManager\Pec\Event;
use App\Helpers\DateHelper;
use App\Models\EventContact;
use App\Models\EventManager\Program\EventProgramDayRoom;
use App\Models\EventManager\Program\EventProgramSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProgramSessions
{

    public static $allowInbetweenContainersMove = false;

    public static function moveSessionUp($sessionId)
    {
        $currentSession = EventProgramSession::find($sessionId);
        if (null === $currentSession) {
            throw new \Exception("Session not found");
        }

        self::sortSessionsPositions($currentSession->event_program_day_room_id);

        $previousSession = EventProgramSession::where('event_program_day_room_id', $currentSession->event_program_day_room_id)
            ->where('position', '<', $currentSession->position)
            ->orderBy('position', 'desc')
            ->first();

        // If there's a previous session on the same container, swap their positions
        if ($previousSession) {
            $tempPosition = $currentSession->position;
            $currentSession->position = $previousSession->position;
            $previousSession->position = $tempPosition;

            $currentSession->save();
            $previousSession->save();
        } // If the session is the first session of the day, move it to the end of the previous day.
        else {
            if (self::$allowInbetweenContainersMove) {
                $previousDay = EventProgramDayRoom::where('event_id', $currentSession->programDay->event_id)
                    ->where('datetime_start', '<', $currentSession->programDay->datetime_start)
                    ->orderBy('datetime_start', 'desc')
                    ->first();


                if (!$previousDay) {
                    // This is the very first session of the entire event; nothing to do
                    return true;
                }

                // Get the last session of the previous day to determine the new position
                $lastSessionOfPreviousDay = EventProgramSession::where('event_program_day_room_id', $previousDay->id)
                    ->orderBy('position', 'desc')
                    ->first();

                $newPosition = $lastSessionOfPreviousDay ? $lastSessionOfPreviousDay->position + 1 : 1;

                // Before updating the current session, decrement all sessions that were positioned after it on the same day
                EventProgramSession::where('event_program_day_room_id', $currentSession->event_program_day_room_id)
                    ->where('position', '>', $currentSession->position)
                    ->decrement('position');

                $currentSession->position = $newPosition;
                $currentSession->event_program_day_room_id = $previousDay->id;
                $currentSession->save();
            }
        }

        return true;
    }


    public static function moveSessionDown($sessionId)
    {
        $currentSession = EventProgramSession::find($sessionId);
        if (null === $currentSession) {
            throw new \Exception("Session not found");
        }
        self::sortSessionsPositions($currentSession->event_program_day_room_id);

        $nextSession = EventProgramSession::where('event_program_day_room_id', $currentSession->event_program_day_room_id)
            ->where('position', '>', $currentSession->position)
            ->orderBy('position', 'asc')
            ->first();

        // If there's a next session on the same container, swap their positions
        if ($nextSession) {
            $tempPosition = $currentSession->position;
            $currentSession->position = $nextSession->position;
            $nextSession->position = $tempPosition;

            $currentSession->save();
            $nextSession->save();
        } // If the session is the last session of the day, move it to the start of the next day.
        else {
            if (self::$allowInbetweenContainersMove) {
                $nextDay = EventProgramDayRoom::where('event_id', $currentSession->programDay->event_id)
                    ->where('datetime_start', '>', $currentSession->programDay->datetime_start)
                    ->orderBy('datetime_start', 'asc')
                    ->first();

                if (!$nextDay) {
                    // This is the very last session of the entire event; nothing to do
                    return true;
                }

                // Get the first session of the next day to determine the new position
                $firstSessionOfNextDay = EventProgramSession::where('event_program_day_room_id', $nextDay->id)
                    ->orderBy('position', 'asc')
                    ->first();

                // Increment all session positions on the next day by 1
                EventProgramSession::where('event_program_day_room_id', $nextDay->id)
                    ->increment('position');

                // Move the current session to the next day with position 1
                $currentSession->position = 1;
                $currentSession->event_program_day_room_id = $nextDay->id;
                $currentSession->save();
            }

        }

        return true;
    }


    public static function swapByPosition(int $sessionId, int $newPosition)
    {
        $session = EventProgramSession::find($sessionId);
        if (null === $session) {
            throw new \Exception("Session not found");
        }

        $day_id = $session->event_program_day_room_id;
        $newPositionSession = EventProgramSession::where('event_program_day_room_id', $day_id)
            ->where('position', $newPosition)
            ->first();

        if (null === $newPositionSession) {
            throw new \Exception("New position session not found");
        }

        if ($session->position !== $newPosition) {
            $tempPosition = $session->position;
            $session->position = $newPositionSession->position;
            $newPositionSession->position = $tempPosition;

            $session->save();
            $newPositionSession->save();
            self::sortSessionsPositions($day_id);
        }
        return true;
    }


    public static function getPracticalSummary(EventProgramSession $session): array
    {
        return [
            'date' => $session->programDay->datetime_start->format(config("app.date_display_format")),
            'start_time' => $session->programDay->datetime_start->format("H\hi"),
            'duration' => DateHelper::convertMinutesToReadableDuration($session->interventions->sum('duration'), 'h'),
            'room' => $session->room->name,
        ];
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private static function sortSessionsPositions(int $dayRoomId)
    {
        DB::beginTransaction();

        try {
            $sessions = EventProgramSession::where('event_program_day_room_id', $dayRoomId)
                ->orderBy('position')
                ->get();

            // Reset the positions
            $position = 1;
            foreach ($sessions as $session) {
                $session->position = $position++;
                $session->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
