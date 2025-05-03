<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'last_name' => 'istrator',
            'email' => 'admin@admin.com',
            'password' => '12345678',
            'role_id' => Role::where('name', 'Admin')->first()->id
        ]);
    }
}
