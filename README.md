Задание:
Есть таблица:

CREATE TABLE public.balance_history (
id bigserial NOT NULL,
account_id int8 NOT NULL,
currency_id int8 NOT NULL,
amount numeric(36, 22) NOT NULL,
created_at timestamp(0) NULL,
updated_at timestamp(0) NULL,
CONSTRAINT balance_history_pkey PRIMARY KEY (id)
);
В ней 50гб данных, медленная и тяжелая. Делать с ней что-то невыносимо больно.
На таблицу завязана куча отчетов, которые постоянно используются.

Есть модель \App\Models\BalanceHistory

Нужна laravel artisan команда которая обработает записи старше 1 мес.:
- на каждый день нужно оставить только одну первую запись для каждого account_id и currency_id
- остальные записи удалить
- учитывать объем таблицы и то что она может использоваться другими сервисами, не должно быть блокировок
- чистка должна выполняться с минимальной нагрузкой на серввер

БД - Postgresql

Например:
Исходная таблица:
Дата / currency_id / account_id / amount
10.02.25 12:00 / 1 / 1 / 100
10.02.25 13:00 / 1 / 1 / 200
10.02.25 13:00 / 2 / 1 / 200
11.02.25 14:00 / 2 / 3 / 200
11.02.25 15:00 / 2 / 3 / 200

Результат
10.02.25 12:00 / 1 / 1 / 100
10.02.25 13:00 / 2 / 1 / 200
11.02.25 14:00 / 2 / 3 / 200



```запусть проекта```
```make all```
