<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBoxRequest extends Model
{
    use HasFactory;

    protected $fillable = ["request_event_type", "request_data"];

    protected $casts = [
        "request_data" => "json",
    ];
}
