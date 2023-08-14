<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::paginate();

        return view('ingredient.index', compact('ingredients'));
    }
    
    public function verify_ingredient_availability($ingredient, $quantity)
    {

    }

    public function delivery_ingredient()
    {

    }
}
