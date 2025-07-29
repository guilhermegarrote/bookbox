<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Str;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $key = 'max_book_loans';

        if (!Setting::where('key', $key)->exists()) {
            Setting::create([
                'id' => hex2bin(str_replace('-', '', (string) Str::uuid())),
                'key' => $key,
                'value' => '3',
            ]);
        }
    }
}
