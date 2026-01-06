<?php

namespace Database\Seeders;

use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        foreach (StatusEnum::cases() as $status) {
            DB::table('order_statuses')->updateOrInsert(
                [
                    'name' => $status->value
                ]
            );
        }
    }
}
