<?php

namespace App\Jobs\Clickhouse;

use App\Models\BalanceHistory;
use ClickHouseDB\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MigrateBalanceBatchToClickhouseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected int $offset, protected int $limit = 1000) {}

    public function handle(): void
    {
        $records = BalanceHistory::query()
            ->orderBy('id')
            ->offset($this->offset)
            ->limit($this->limit)
            ->get();

        if ($records->isEmpty()) {
            return;
        }

        $rows = $records->map(function ($row) {
            return [
                'id' => (int) $row->id,
                'account_id' => (int) $row->account_id,
                'currency_id' => (int) $row->currency_id,
                'amount' => (string) $row->amount,
                'created_at' => $row->created_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();

        app(Client::class)->insert(
            'balance_histories', $rows
        );
    }
}
