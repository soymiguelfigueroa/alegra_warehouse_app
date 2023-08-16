<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::paginate();

        return view('ingredient.index', compact('ingredients'));
    }

    public function orders()
    {
        $orders_ingredients = $this->getOrdersIngredients();

        foreach ($orders_ingredients as &$order_ingredient) {
            $ingredient = Ingredient::find($order_ingredient['ingredient_id']);
            $order_ingredient['ingredient'] = $ingredient;
        }

        return view('ingredient.undelivered', compact('orders_ingredients'));
    }

    public function deliver(Ingredient $ingredient, Request $request)
    {
        $order_id = $request->order_id;
        $quantity = $request->quantity;

        if ($ingredient->quantity >= $quantity) {
            $ingredient->quantity -= $quantity;
            $ingredient->save();

            $delivered = $this->deliverIngredient($ingredient, $order_id);
            if ($delivered) {
                return redirect(route('ingredient.orders'))->with('success', __('Ingredient delivered'));
            }

            return redirect(route('ingredient.orders'))->with('error', __('Ingredient can not be delivered, try again later'));
        }

        $purchased = $this->purchaseIngredientInMarket($ingredient, $quantity);
        if ($purchased) {
            $ingredient->quantity -= $quantity;
            $ingredient->save();

            $delivered = $this->deliverIngredient($ingredient, $order_id);
            if ($delivered) {
                return redirect(route('ingredient.orders'))->with('success', __('Ingredient delivered'));
            }
        }

        return redirect(route('ingredient.orders'))->with('error', __('Ingredient can not be delivered, try again later'));
    }

    private function deliverIngredient($ingredient, $order_id): bool
    {
        $response = Http::patch(env('API_KITCHEN_ENDPOINT') . 'ingredients/deliver', [
            'ingredient_id' => $ingredient->id,
            'order_id' => $order_id,
        ]);

        if ($response->ok()) {
            return true;
        }

        return false;
    }

    private function getOrdersIngredients()
    {
        $response = Http::get(env('API_KITCHEN_ENDPOINT') . 'ingredients/undelivered');

        return $response->json();
    }

    private function purchaseIngredientInMarket($ingredient, $quantity): bool
    {
        while ($ingredient->quantity < $quantity) {
            $response = Http::get('https://recruitment.alegra.com/api/farmers-market/buy?ingredient=' . strtolower($ingredient->name));

            $quantitySold = $response->json()['quantitySold'];
            if ($quantitySold <= 0)
                continue;

            $purchase = new Purchase();
            $purchase->quantity = $quantitySold;
            $purchase->ingredient_id = $ingredient->id;
            $purchase->save();

            $ingredient->quantity += $quantitySold;
            $ingredient->save();
        }

        return true;
    }
}
