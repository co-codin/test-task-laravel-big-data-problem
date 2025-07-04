<?php

namespace App\Jobs;

use App\Models\BalanceHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CleanBalanceHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected array $ids, protected int $sleep = 1) {}

    public function handle()
    {
        if (empty($this->ids)) {
            return;
        }

        DB::transaction(function () {
            BalanceHistory::query()->whereIn('id', $this->ids)->delete();
        });

        sleep($this->sleep);
    }
}
