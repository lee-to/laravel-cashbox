<?php

namespace Leeto\CashBox\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CashBoxRequest
 * @package Leeto\CashBox\Models
 */
class CashBoxRequest extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ["request_event_type", "request_data"];

    /**
     * @var string[]
     */
    protected $casts = [
        "request_data" => "json",
    ];
}
