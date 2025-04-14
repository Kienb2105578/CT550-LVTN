<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTaking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'stock_takings';

    protected $fillable = [
        'user_id',
        'products',
        'description',
        'publish',
        'code',
    ];

    protected $casts = [
        'products' => 'json',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
