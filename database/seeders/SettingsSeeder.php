<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seeder for application settings.
 *
 * Inserts default settings defined in the `settings.php` configuration file
 * into the database if they do not already exist.
 */
class SettingsSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * For each configuration defined in `config('settings')`, checks if a corresponding
     * record exists in the database. If it does not exist, creates a new setting
     * using the default value defined in the configuration.
     */
    public function run(): void
    {
        $settingsConfig = config('settings');

        foreach ($settingsConfig as $key => $config) {
            $defaultValue = $config['default'] ?? null;

            if ($defaultValue !== null && !Setting::where('key', $key)->exists()) {
                Setting::create([
                    'key' => $key,
                    'value' => (string) $defaultValue,
                ]);
            }
        }
    }
}
