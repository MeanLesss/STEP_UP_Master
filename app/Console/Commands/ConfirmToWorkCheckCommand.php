<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConfirmToWorkCheckCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'step_up:confirm-work';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the freelancer accept the order from client within 7 days. so refund and send email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
