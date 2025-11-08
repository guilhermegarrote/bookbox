<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Main database seeder.
 *
 * Calls all individual seeders required to populate the database with
 * initial/default data.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Executes each seeder class listed in the call array.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
        ]);
    }
}
