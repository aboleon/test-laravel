<?php

namespace App\Accessors;

use App\Enum\DesiredTransportManagement;
use App\Enum\EventProgramParticipantStatus;
use App\Models\DictionnaryEntry;
use App\Models\Event;
use App\Models\EventContact;
use App\Models\EventManager\Program\EventProgramDayRoom;
use App\Models\EventManager\Program\EventProgramIntervention;
use App\Models\EventManager\Program\EventProgramSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ProgramInterventions
{
    public static $allowInbetweenSessionsMove = false;

    public static function refreshStartEndTimes(Event $event)
    {
        $days = $event->programDays;

        foreach ($days as $day) {
            // This is the starting time for the day
            $currentTime = clone $day->datetime_start;

            // Fetch all sessions for the current day, sorted by position
            $sessions = $day->sessions->sortBy('position');

            foreach ($sessions as $session) {
                // Fetch all interventions for the current session, sorted by position
                $interventions = $session->interventions->sortBy('position');

                foreach ($interventions as $intervention) {
                    if (!is_null($intervention->preferred_start_time)) {
                        // Extract the time from the preferred_start_time
                        $preferredTime = $intervention->preferred_start_time->format('H:i:s');

                        // Construct a new datetime using the day's date and the preferred time
                        $intervention->start = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $day->datetime_start->toDateString() . ' ' . $preferredTime);
                        $intervention->end = (clone $intervention->start)->addMinutes($intervention->duration);
                    } else {
                        // Set start and end based on the accumulated time and the intervention duration
                        $intervention->start = clone $currentTime;
                        $intervention->end = (clone $currentTime)->addMinutes($intervention->duration);

                        // Adjust the current time for the next intervention
                        $currentTime->addMinutes($intervention->duration);
                    }
                    $intervention->save();
                }
            }
        }
    }


    public static function moveInterventionUp($interventionId)
    {
        $currentIntervention = EventProgramIntervention::find($interventionId);
        if (null === $currentIntervention) {
            throw new \Exception("Intervention not found");
        }

        $previousIntervention = EventProgramIntervention::where('event_program_session_id', $currentIntervention->event_program_session_id)
            ->where('position', '<', $currentIntervention->position)
            ->orderBy('position', 'desc')
            ->first();

        // If there's a previous intervention in the same session, swap them
        if ($previousIntervention) {
            $tempPosition = $currentIntervention->position;
            $currentIntervention->position = $previousIntervention->position;
            $previousIntervention->position = $tempPosition;

            $currentIntervention->save();
            $previousIntervention->save();
        } // If the intervention is the first in its session, move it to the end of the previous session
        else {
            if (self::$allowInbetweenSessionsMove) {
                $currentSession = $currentIntervention->session;
                $previousSession = EventProgramSession::where('event_program_day_room_id', $currentSession->event_program_day_room_id)
                    ->where('position', '<', $currentSession->position)
                    ->orderBy('position', 'desc')
                    ->first();

                if ($previousSession) {
                    $lastInterventionOfPreviousSession = EventProgramIntervention::where('event_program_session_id', $previousSession->id)
                        ->orderBy('position', 'desc')
                        ->first();

                    $newPosition = $lastInterventionOfPreviousSession ? $lastInterventionOfPreviousSession->position + 1 : 1;

                    $currentIntervention->position = $newPosition;
                    $currentIntervention->event_program_session_id = $previousSession->id;
                    $currentIntervention->save();
                } // If the session is the first session of the day, then move the intervention to the end of the last session of the previous day.
                else {
                    if (ProgramSessions::$allowInbetweenContainersMove) {
                        $previousDay = EventProgramDayRoom::where('event_id', $currentSession->programDay->event_id)
                            ->where('datetime_start', '<', $currentSession->programDay->datetime_start)
                            ->orderBy('datetime_start', 'desc')
                            ->first();

                        $lastSessionOfPreviousDay = null;
                        while ($previousDay) {
                            $lastSessionOfPreviousDay = EventProgramSession::where('event_program_day_room_id', $previousDay->id)
                                ->orderBy('position', 'desc')
                                ->first();

                            // If we find a session, break out of the loop
                            if ($lastSessionOfPreviousDay) {
                                break;
                            }

                            // Move to the previous day
                            $previousDay = EventProgramDayRoom::where('event_id', $currentSession->programDay->event_id)
                                ->where('datetime_start', '<', $previousDay->datetime_start)
                                ->orderBy('datetime_start', 'desc')
                                ->first();
                        }

                        if (!$lastSessionOfPreviousDay) {
                            // No session found on any preceding day; nothing to do
                            return true;
                        }

                        $lastInterventionOfLastSession = EventProgramIntervention::where('event_program_session_id', $lastSessionOfPreviousDay->id)
                            ->orderBy('position', 'desc')
                            ->first();

                        $newPosition = $lastInterventionOfLastSession ? $lastInterventionOfLastSession->position + 1 : 1;

                        // Decrement all the positions of the interventions of the $currentSession
                        EventProgramIntervention::where('event_program_session_id', $currentSession->id)
                            ->where('position', '>', $currentIntervention->position)
                            ->decrement('position');

                        $currentIntervention->position = $newPosition;
                        $currentIntervention->event_program_session_id = $lastSessionOfPreviousDay->id;
                        $currentIntervention->save();
                    }
                }
            }
        }

        return true;
    }


    public static function moveInterventionDown($interventionId)
    {
        $currentIntervention = EventProgramIntervention::find($interventionId);
        if (null === $currentIntervention) {
            throw new \Exception("Intervention not found");
        }

        $nextIntervention = EventProgramIntervention::where('event_program_session_id', $currentIntervention->event_program_session_id)
            ->where('position', '>', $currentIntervention->position)
            ->orderBy('position', 'asc')
            ->first();

        // If there's a next intervention in the same session
        if ($nextIntervention) {
            $tempPosition = $currentIntervention->position;
            $currentIntervention->position = $nextIntervention->position;
            $nextIntervention->position = $tempPosition;

            $currentIntervention->save();
            $nextIntervention->save();
        } // If the intervention is the last in its session
        else {
            if (self::$allowInbetweenSessionsMove) {
                $currentSession = $currentIntervention->session;
                $nextSession = EventProgramSession::where('event_program_day_room_id', $currentSession->event_program_day_room_id)
                    ->where('position', '>', $currentSession->position)
                    ->orderBy('position', 'asc')
                    ->first();

                // If there's a next session on the same day
                if ($nextSession) {
                    // Increment all the positions of the interventions of the next session
                    EventProgramIntervention::where('event_program_session_id', $nextSession->id)
                        ->increment('position');

                    $currentIntervention->position = 1;
                    $currentIntervention->event_program_session_id = $nextSession->id;
                    $currentIntervention->save();
                } // If the session is the last session of the day
                else {

                    if (ProgramSessions::$allowInbetweenContainersMove) {

                        $nextDay = EventProgramDayRoom::where('event_id', $currentSession->programDay->event_id)
                            ->where('datetime_start', '>', $currentSession->programDay->datetime_start)
                            ->orderBy('datetime_start', 'asc')
                            ->first();


                        $firstSessionOfNextDay = null;
                        while ($nextDay) {
                            $firstSessionOfNextDay = EventProgramSession::where('event_program_day_room_id', $nextDay->id)
                                ->orderBy('position', 'asc')
                                ->first();

                            // If we find a session, break out of the loop
                            if ($firstSessionOfNextDay) {
                                break;
                            }

                            // Move to the next day within the same event and ordered by datetime_start
                            $nextDay = EventProgramDayRoom::where('event_id', $currentSession->programDay->event_id)
                                ->where('datetime_start', '>', $nextDay->datetime_start)
                                ->orderBy('datetime_start', 'asc')
                                ->first();
                        }

                        if (!$firstSessionOfNextDay) {
                            // No session found on any subsequent day; nothing to do
                            return true;
                        }

                        // Increment all the positions of the interventions of the found session
                        EventProgramIntervention::where('event_program_session_id', $firstSessionOfNextDay->id)
                            ->increment('position');

                        $currentIntervention->position = 1;
                        $currentIntervention->event_program_session_id = $firstSessionOfNextDay->id;
                        $currentIntervention->save();
                    }
                }
            }
        }

        return true;
    }


    public static function swapByPosition(int $interventionId, int $newPosition)
    {
        $intervention = EventProgramIntervention::find($interventionId);
        if (null === $intervention) {
            throw new \Exception("Intervention not found");
        }

        $session_id = $intervention->event_program_session_id;
        $newPositionIntervention = EventProgramIntervention::where('event_program_session_id', $session_id)
            ->where('position', $newPosition)
            ->first();

        if (null === $newPositionIntervention) {
            throw new \Exception("New position intervention not found");
        }

        if ($intervention->position !== $newPosition) {
            $tempPosition = $intervention->position;
            $intervention->position = $newPositionIntervention->position;
            $newPositionIntervention->position = $tempPosition;

            $intervention->save();
            $newPositionIntervention->save();
        }
        return true;
    }
}
