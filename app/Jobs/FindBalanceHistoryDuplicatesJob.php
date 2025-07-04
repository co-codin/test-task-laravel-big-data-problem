<?php

namespace App\Jobs;

use App\Models\BalanceHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class FindBalanceHistoryDuplicatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected string $date, protected int $batchSize = 1000, protected int $sleep = 1) {}

    public function handle(): void
    {
        $date = Carbon::parse($this->date);
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $date = Carbon::parse($this->date);
        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();

        $idsToDelete = BalanceHistory::query()
            ->select('id', 'account_id', 'currency_id', 'created_at')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn ($row) => $row->account_id.'-'.$row->currency_id)
            ->map(function ($group) {
                return $group->sortBy('created_at')->pluck('id')->slice(1); // оставить первую
            })
            ->flatten()
            ->values();

        $total = $idsToDelete->count();

        if ($total === 0) {
            Log::info("No duplicates found on {$this->date}");

            return;
        }

        $batches = ceil($total / $this->batchSize);

        for ($i = 0; $i < $batches; $i++) {
            $batchIds = $idsToDelete->slice($i * $this->batchSize, $this->batchSize)->values();
            CleanBalanceHistoryJob::dispatch($batchIds->toArray(), $this->sleep);

            Log::info("Dispatched clean job for date {$this->date}, batch #{$i} with ".count($batchIds).' IDs');
        }
    }
}
