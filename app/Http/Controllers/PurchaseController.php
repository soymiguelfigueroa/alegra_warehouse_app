<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Collection;

class PurchaseController extends Controller
{
    public function getPurchases(): Collection
    {
        $purchases = Purchase::all();
        foreach ($purchases as &$purchase) {
            $purchase['ingredient'] = $purchase->ingredient;
        }
        return $purchases;
    }
}
