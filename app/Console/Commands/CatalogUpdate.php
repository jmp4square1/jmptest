<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Libraries\Crawler\CrawlerAppliances;

class CatalogUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalog:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates catalog data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $v_crawler = new CrawlerAppliances();        
        $v_crawler->run();
    }
}
