<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClickhouseMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clickhouse:migrate';

    protected $description = 'Create balance_histories table in ClickHouse';


    /**
     * Execute the console command.
     */
    public function handle()
    {

    }
}
