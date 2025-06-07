<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::query()->updateOrCreate([
            'username' => 'admin'
        ], [
            'name' => 'Super Admin',
            'email' => 'admin@site.com',
            'email_verified_at' => null,
            'image' => '66fd5b32600f51727879986.png',
            'password' => Hash::make('admin'),
            'remember_token' => null,
        ]);
    }
}
