<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // users
        // DB::table('users')->insert([
        //     'name' => 'hen',
        //     'email' => 'test@gmail.com',
        //     'password' => Hash::make('0329'),
        // ]);

        for ($i = 0; $i < 10; $i++) {
            DB::table('items')->insert([
                'name' => 'product' . ($i + 1),
                'price' => 10 * ($i + 1),
            ]);
        }
    }
}
