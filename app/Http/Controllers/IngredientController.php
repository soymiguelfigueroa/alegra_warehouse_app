<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Purchase;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class IngredientController extends Controller
{
    public function index(): View
    {
        $ingredients = Ingredient::paginate();

        return view('ingredient.index', compact('ingredients'));
    }

    public function orders(): View
    {
        $orders_ingredients = $this->getOrdersIngredients();

        foreach ($orders_ingredients as &$order_ingredient) {
            $ingredient = Ingredient::find($order_ingredient['ingredient_id']);
            $order_ingredient['ingredient'] = $ingredient;
        }

        return view('ingredient.undelivered', compact('orders_ingredients'));
    }

    public function deliver(Ingredient $ingredient, Request $request): RedirectResponse
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

    public function getDeliveredOrders(): View
    {
        $response = Http::get(env('API_KITCHEN_ENDPOINT') . 'ingredients/delivered');

        $orders = $response->json();
        foreach ($orders as &$order) {
            $ingredient = Ingredient::find($order['ingredient_id']);
            $order['ingredient'] = $ingredient;
        }

        return view('ingredient.delivered', compact('orders'));
    }

    public function getIngredients(): Collection
    {
        return Ingredient::all();
    }

    public function getIngredientsByOrder(Request $request): array
    {
        $ingredients_order = $request->ingredients_order;
        $ingredients = [];
        foreach ($ingredients_order as $ingredient_order) {
            $ingredient = Ingredient::find($ingredient_order['ingredient_id']);
            $ingredient->quantity = $ingredient_order['quantity']; // Order quantity
            $ingredients[] = $ingredient;
        }
        return $ingredients;
    }

    public function getIngredientsByReceipt(Request $request): array
    {
        $ingredients_receipt = $request->ingredients_receipt;
        $ingredients = [];
        foreach ($ingredients_receipt as $ingredient_receipt) {
            $ingredient = Ingredient::find($ingredient_receipt['ingredient_id']);
            $ingredient->quantity = $ingredient_receipt['quantity']; // Receipt quantity
            $ingredients[] = $ingredient;
        }
        return $ingredients;
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
