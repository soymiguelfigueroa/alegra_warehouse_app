<?php

namespace App\Console\Commands;

use App\Models\Ingredient;
use Illuminate\Console\Command;

class SeedDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed database with initial data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $initial_quantity = 5;
        
        $ingredient = new Ingredient();
        $ingredient->name = 'Tomato';
        $ingredient->quantity = $initial_quantity;
        $ingredient->save();
        
        $ingredient = new Ingredient();
        $ingredient->name = 'Lemon';
        $ingredient->quantity = $initial_quantity;
        $ingredient->save();
        
        $ingredient = new Ingredient();
        $ingredient->name = 'Potato';
        $ingredient->quantity = $initial_quantity;
        $ingredient->save();
        
        $ingredient = new Ingredient();
        $ingredient->name = 'Rice';
        $ingredient->quantity = $initial_quantity;
        $ingredient->save();
        
        $ingredient = new Ingredient();
        $ingredient->name = 'Ketchup';
        $ingredient->quantity = $initial_quantity;
        $ingredient->save();
        
        $ingredient = new Ingredient();
        $ingredient->name = 'Lettuce';
        $ingredient->quantity = $initial_quantity;
        $ingredient->save();
        
        $ingredient = new Ingredient();
        $ingredient->name = 'Onion';
        $ingredient->quantity = $initial_quantity;
        $ingredient->save();
        
        $ingredient = new Ingredient();
        $ingredient->name = 'Cheese';
        $ingredient->quantity = $initial_quantity;
        $ingredient->save();
        
        $ingredient = new Ingredient();
        $ingredient->name = 'Meat';
        $ingredient->quantity = $initial_quantity;
        $ingredient->save();
        
        $ingredient = new Ingredient();
        $ingredient->name = 'Chicken';
        $ingredient->quantity = $initial_quantity;
        $ingredient->save();

        $this->info('The command was successful!');
    }
}
