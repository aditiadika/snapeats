<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Entity;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Entity
        $entity = Entity::create([
            'name' => 'PT Makanan Enak',
            'description' => 'Grup restoran dengan banyak cabang',
        ]);

        User::create([
            'entity_id' => $entity->id,
            'name' => 'Admin',
            'email' => 'admin@makanenak.id',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ]);

        // 2. Create Branches
        $sudirman = Branch::create([
            'entity_id' => $entity->id,
            'name' => 'Cabang Sudirman',
            'address' => 'Jl. Sudirman No. 123',
            'phone' => '081234567890',
        ]);

        $thamrin = Branch::create([
            'entity_id' => $entity->id,
            'name' => 'Cabang Thamrin',
            'address' => 'Jl. Thamrin No. 456',
            'phone' => '089876543210',
        ]);

        // 3. Create Categories
        $makanan = Category::create([
            'entity_id' => $entity->id,
            'name' => 'Makanan',
        ]);

        $minuman = Category::create([
            'entity_id' => $entity->id,
            'name' => 'Minuman',
        ]);

        // 4. Create Products
        $products = [
            ['Nasi Goreng Special', $makanan->id, 25000],
            ['Sate Ayam', $makanan->id, 30000],
            ['Gado-Gado', $makanan->id, 22000],
            ['Es Teh Manis', $minuman->id, 8000],
            ['Jus Alpukat', $minuman->id, 15000],
        ];

        $productIds = [];

        foreach ($products as [$name, $categoryId, $price]) {
            $product = Product::create([
                'entity_id' => $entity->id,
                'category_id' => $categoryId,
                'name' => $name,
                'price' => $price,
                'is_active' => true,
            ]);
            $productIds[$name] = $product->id;
        }

        // 5. Attach products to Sudirman with stock
        $sudirman->products()->attach([
            Product::where('name', 'Nasi Goreng Special')->value('id'),
            Product::where('name', 'Es Teh Manis')->value('id'),
            Product::where('name', 'Sate Ayam')->value('id'),
            Product::where('name', 'Gado-Gado')->value('id'),
            Product::where('name', 'Jus Alpukat')->value('id'),
        ]);

        // 6. Attach products to Thamrin
        $thamrin->products()->attach([
            Product::where('name', 'Nasi Goreng Special')->value('id'),
            Product::where('name', 'Es Teh Manis')->value('id'),
            Product::where('name', 'Sate Ayam')->value('id'),
            Product::where('name', 'Gado-Gado')->value('id'),
            Product::where('name', 'Jus Alpukat')->value('id'),
        ]);

        // Tables for Sudirman Branch
        $sudirmanTables = [
            ['entity_id' => $entity->id, 'branch_id' => $sudirman->id, 'table_number' => 'A1', 'capacity' => 4, 'qr_code' => Str::uuid(), 'is_available' => true],
            ['entity_id' => $entity->id, 'branch_id' => $sudirman->id, 'table_number' => 'A2', 'capacity' => 2, 'qr_code' => Str::uuid(), 'is_available' => true],
            ['entity_id' => $entity->id, 'branch_id' => $sudirman->id, 'table_number' => 'A3', 'capacity' => 6, 'qr_code' => Str::uuid(), 'is_available' => true],
        ];

        foreach ($sudirmanTables as $table) {
            Table::create($table);
        }

        // Tables for Sudirman Branch
        $thamrinTables = [
            ['entity_id' => $entity->id, 'branch_id' => $thamrin->id, 'table_number' => 'A1', 'capacity' => 4, 'qr_code' => Str::uuid(), 'is_available' => true],
            ['entity_id' => $entity->id, 'branch_id' => $thamrin->id, 'table_number' => 'A2', 'capacity' => 2, 'qr_code' => Str::uuid(), 'is_available' => true],
            ['entity_id' => $entity->id, 'branch_id' => $thamrin->id, 'table_number' => 'A3', 'capacity' => 6, 'qr_code' => Str::uuid(), 'is_available' => true],
        ];

        foreach ($thamrinTables as $table) {
            Table::create($table);
        }
    }
}
