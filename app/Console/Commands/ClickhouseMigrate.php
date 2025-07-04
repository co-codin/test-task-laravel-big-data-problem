<?php

namespace App\Console\Commands;

use App\Jobs\Clickhouse\MigrateBalanceBatchToClickhouseJob;
use App\Models\BalanceHistory;
use Illuminate\Console\Command;

class ClickhouseMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:queue-migrate-to-clickhouse {--batch=1000}';

    protected $description = 'Create balance_histories table in ClickHouse';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $batchSize = (int) $this->option('batch', 1000);
        $total = BalanceHistory::count();
        $batches = (int) ceil($total / $batchSize);

        $this->info("Total records: $total");
        $this->info("Dispatching $batches jobs with batch size $batchSize...");

        for ($i = 0; $i < $batches; $i++) {
            $offset = $i * $batchSize;
            MigrateBalanceBatchToClickhouseJob::dispatch($offset, $batchSize);

            $this->line("→ Job dispatched for offset $offset");
        }

        $this->info('All jobs dispatched.');

        return 0;
    }
}
