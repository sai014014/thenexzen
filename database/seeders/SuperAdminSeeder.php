<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SuperAdmin::create([
            'name' => 'Super Admin',
            'email' => 'admin@nexzen.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567890',
            'is_active' => true,
        ]);
    }
}
