<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = $this->getPurchases();

        return view('purchases.index', compact('purchases'));
    }

    public function getPurchases(): Collection
    {
        $purchases = Purchase::all();
        foreach ($purchases as &$purchase) {
            $purchase['ingredient'] = $purchase->ingredient;
        }
        return $purchases;
    }
}
