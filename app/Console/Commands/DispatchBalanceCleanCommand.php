<?php

namespace App\Console\Commands;

use App\Jobs\FindBalanceHistoryDuplicatesJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DispatchBalanceCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:dispatch
                            {--from= : Начальная дата (Y-m-d), defaults to oldest записи}
                            {--to=   : Конечная дата (Y-m-d), defaults to now()->subMonth()->toDateString()}
                            {--batch=1000 : Строк на батч (Job параметр)}
                            {--sleep=1    : Пауза между батчами (Job параметр)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch CleanBalanceHistoryJob for each day older than one month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $to = Carbon::parse($this->option('to') ?: now()->subMonth()->toDateString())->startOfDay();

        if ($this->option('from')) {
            $from = Carbon::parse($this->option('from'))->startOfDay();
        } else {
            $min = DB::table('balance_history')->min('created_at');
            if (! $min) {
                $this->error('No data in balance_history');

                return 1;
            }
            $from = Carbon::parse($min)->startOfDay();
        }

        if ($from->gt($to)) {
            $this->error('`from` must be before or equal to `to`');

            return 1;
        }

        $batchSize = (int) $this->option('batch');
        $sleep = (int) $this->option('sleep');

        $this->info("Dispatching cleanup jobs from {$from->toDateString()} to {$to->toDateString()}");

        for ($date = $from->copy(); $date->lte($to); $date->addDay()) {
            FindBalanceHistoryDuplicatesJob::dispatch(
                $date->toDateString(),
                $batchSize,
                $sleep
            );

            $this->info(' → Job dispatched for date: '.$date->toDateString());
        }

        $this->info('All jobs dispatched.');

        return 0;

    }
}
