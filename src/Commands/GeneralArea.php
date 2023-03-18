<?php

namespace Tyeydy\Map\Commands;

use Illuminate\Console\Command;
use Tyeydy\Map\Services\GeneralAreaService;

class GeneralArea extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tyeydy-map:general-area';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Format the general area';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = new GeneralAreaService();
        $service->generalArea();
    }
}
