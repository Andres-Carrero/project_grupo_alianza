<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cocktails extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'image_url', 'drink_id', 'user_id'
    ];

    protected $dates = ['deleted_at']; 
}
