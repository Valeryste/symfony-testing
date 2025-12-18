<?php

namespace Database\Seeders;

use App\Enum\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Role::cases() as $role) {
            DB::table('roles')->updateOrInsert(
                [
                    'name' => $role->value
                ]
            );
        }
    }
}
