<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceHistory extends Model
{
    /** @use HasFactory<\Database\Factories\BlanaceHistoryFactory> */
    use HasFactory;

    protected $guarded = ['id'];
}
