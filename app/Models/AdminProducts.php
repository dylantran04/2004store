<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminProducts extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "description",
        "price",
        "image",
        "stock",
        "category_id",
    ];
}
