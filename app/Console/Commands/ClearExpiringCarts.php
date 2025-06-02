<?php

namespace App\Console\Commands;

use App\Mutators\Front\Cart\FrontCartMutator;
use Illuminate\Console\Command;

class ClearExpiringCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-expiring-carts';

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
        FrontCartMutator::clearExpiringCarts();
        return null;
    }
}
