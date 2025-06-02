<?php

namespace App\Console\Commands;

use App\Accessors\EventAccessor;
use App\Http\Controllers\MailController;
use App\Models\Event;
use App\Models\EventManager\Grant\Grant;
use App\Mutators\Front\Cart\FrontCartMutator;
use Illuminate\Console\Command;

class SendPreliminaryGrantList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-preliminary-grant-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = now()->addDays(7);
        $events = Event::with("grants");
        $events->each(function ($event) use ($targetDate) {
            $event->grants->each(function (Grant $grant) use ($targetDate) {
                if ($targetDate->isSameDay($grant->prenotification_date)) {
                    $mc = new MailController();
                    $mc->ajaxMode()->distribute('GrantPreliminaryListSendReminder', $grant)->fetchResponse();
                }
            });
        });
    }
}
