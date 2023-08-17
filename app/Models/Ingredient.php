<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static find(mixed $ingredient_id)
 * @method static paginate()
 */
class Ingredient extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'quantity',
    ];
}
