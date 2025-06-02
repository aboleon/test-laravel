<?php

namespace App\Console\Commands;

use App\Actions\Order\StockActions;
use App\Models\EventManager\Accommodation\RoomGroup;
use App\Models\Order\StockTemp;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Throwable;

class ClearTempStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-temp-stock';

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
        try {
            // Hébergements
            StockTemp::where('created_at', '<', Carbon::now()->subMinutes(15))
                ->where('shoppable_type', RoomGroup::class)
                ->where('uuid','NOT LIKE', 'flc_%')
                ->delete();

            // Services
            (new StockActions())->clearServicesTempStock();

            return 0;

        } catch (Throwable) {
            return 1;
        }
    }
}
