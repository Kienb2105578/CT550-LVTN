<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;

class Review extends Model
{
    use HasFactory, QueryScopes;

    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'email',
        'gender',
        'fullname',
        'images',
        'phone',
        'description',
        'score',
        'product_id',
        'customer_id',
        'replies'
    ];

    protected $table = 'reviews';

    public function reviewable()
    {
        return $this->morphTo();
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
