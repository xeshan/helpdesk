<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Ticket extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'subject','body','status','category','explanation','confidence','note','category_source',
    ];

    protected $casts = [
        'confidence' => 'float',
    ];
}
