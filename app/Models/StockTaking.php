<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class StockTaking extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

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


    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
