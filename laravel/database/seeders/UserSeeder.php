<?php

namespace Database\Seeders;

use App\Entity\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            [
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'email' => 'admin@example.com',
                'role_id' => DB::table('roles')
                    ->where('name', '=', 'ADMIN')
                    ->value('id')
            ]
        );
    }
}
