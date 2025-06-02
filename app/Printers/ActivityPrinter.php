<?php
namespace App\Printers;

use Spatie\Activitylog\Models\Activity;

class ActivityPrinter
{
    public static function printCauser(Activity $activity): string
    {
        $output = 'Front';

        if(isset($activity->causer) && $activity->causer_id != $activity->subject_id)
        {
            $output = $activity->causer?->names();
        }

        return $output;
    }
}
