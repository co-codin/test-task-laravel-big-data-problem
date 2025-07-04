<?php

declare(strict_types=1);

use Cog\Laravel\Clickhouse\Migration\AbstractClickhouseMigration;

return new class extends AbstractClickhouseMigration
{
    public function up(): void
    {
        $this->clickhouseClient->write(
            <<<SQL
                CREATE TABLE IF NOT EXISTS balance_history
                (
                    id UInt64,
                    account_id UInt64,
                    currency_id UInt64,
                    amount Decimal(36, 22),
                    created_at DateTime
                )
                ENGINE = MergeTree
                PARTITION BY toYYYYMM(created_at)
                ORDER BY (account_id, currency_id, created_at);

            SQL
        );
    }
};
