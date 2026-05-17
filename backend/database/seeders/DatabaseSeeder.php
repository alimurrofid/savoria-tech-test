<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
        ]);
        for ($i = 1; $i <= 20; $i++) {
            Product::create([
                'name' => "Product $i",
                'sku' => 'SKU-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'price' => $i * 100,
                'stock' => $i * 10,
            ]);
        }
    }
}
