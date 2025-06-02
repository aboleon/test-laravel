<?php

namespace App\Helpers;

use App\Accessors\Dates;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use MetaFramework\Traits\Responses;


class DateHelper
{

    use Responses;


    public static function getFrontDate(Carbon $date): string
    {
        $format = Dates::getFrontDateFormat();
        return $date->format($format);
    }

    public static function getFrontHourMinute(Carbon $datetime): string
    {
        $format = Dates::getFrontHourMinuteFormat();
        return $datetime->format($format);
    }

    public static function getDatesFromCarbonPeriod(CarbonPeriod $period, string $format = "Y-m-d"): array
    {
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format($format);
        }
        return $dates;

    }

    /**
     * Usage:
     * nbDaysBetweenDates($data->starts, $data->ends, 'd/m/Y')
     */
    public static function nbDaysBetweenDates($startDate, $endDate, $format = 'Y-m-d')
    {
        // Convert the date strings to DateTime objects
        $start = DateTime::createFromFormat($format, $startDate);
        $end = DateTime::createFromFormat($format, $endDate);

        // Check if the dates were parsed correctly
        if (!$start || !$end) {
            throw new Exception("Invalid date format");
        }

        // Check if the end date is before the start date
        if ($end < $start) {
            throw new Exception("End date cannot be before the start date");
        }

        // Calculate the difference between the two dates
        $interval = $start->diff($end);

        // Return the number of days
        return $interval->days;
    }

    /**
     * Returns the number of days to add to $d1 to get $d2
     */
    public static function getDaysDelta($d1, $d2): int
    {
        $date1 = new DateTime($d1);
        $date2 = new DateTime($d2);
        $interval = $date1->diff($date2);
        return (int)$interval->format('%R%a');
    }


    public function listDaysBetweenDates($startDate, $endDate, $format = 'Y-m-d'): array
    {
        // Convert the date strings to DateTime objects
        $start = DateTime::createFromFormat($format, $startDate);
        $end = DateTime::createFromFormat($format, $endDate);

        // Check if the dates were parsed correctly
        if (!$start || !$end) {
            $this->responseError("Format de date invalide");
        }

        // Check if the end date is before the start date
        if ($end < $start) {
            $this->responseError("La date de début ne peut pas être supérieure à la date de fin");
        }

        if ($this->hasErrors()) {
            return [];
        }

        $days = [];
        while ($start <= $end) {
            $days[] = $start->format($format);
            $start->modify('+1 day');
        }
        $this->responseElement('days', $days);

        return $days;
    }


    public static function minutesToHoursMinutesTime(int $minutes, string $sep = ':')
    {
        $hours = floor($minutes / 60);
        $minutes %= 60;
        return sprintf('%02d' . $sep . '%02d', $hours, $minutes);
    }

    public static function timeToHoursMinutesTime($time)
    {
        $hours = $time->format('H');
        $minutes = $time->format('i');

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public static function hoursMinutesTimeToMinutes($time)
    {
        $p = explode(':', $time);
        return intval($p[0]) * 60 + intval($p[1]);
    }

    public static function convertMinutesToReadableDuration($minutes, $hSuffix = 'h', $mSuffix = 'min')
    {
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

//        $fHours = sprintf('%02d', $hours);
        $fHours = $hours;
        $fMinutes = sprintf('%02d', $remainingMinutes);

        if ($hours > 0 && $remainingMinutes > 0) {
            return "{$fHours}{$hSuffix}{$fMinutes}{$mSuffix}";
        } elseif ($hours > 0) {
            return "{$fHours}{$hSuffix}";
        } else {
            return "{$fMinutes}{$mSuffix}";
        }
    }


    public static function appDateToSqlDate(?string $date): string|null
    {
        if (null === $date) {
            return null;
        }
        return Carbon::createFromFormat(config('app.date_display_format'), $date)->format('Y-m-d');
    }

    public static function parseFrontDate(string $frontDate, $asMysqlDate = true): Carbon|string
    {
        $dateFormat = Dates::getFrontDateFormat();
        $carbonDate = Carbon::createFromFormat($dateFormat, $frontDate);
        return $asMysqlDate ? $carbonDate->format('Y-m-d') : $carbonDate;
    }
}
