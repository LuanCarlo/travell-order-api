<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderSatus extends Model
{
    /** @use HasFactory<\Database\Factories\OrderSatusFactory> */
    use HasFactory;

    protected $table = 'order_status';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['status'];
}
