<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder for application settings.
 *
 * Inserts default settings into the database if they do not already exist.
 * Currently seeds the 'max_book_loans' setting.
 */
class SettingsSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * Checks if the setting with key 'max_book_loans' exists. If not, it creates
     * a new setting record with a UUID as the primary key and default value.
     */
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
