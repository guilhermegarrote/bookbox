<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeederDev extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            SchoolClassesSeeder::class,
            StudentsSeeder::class,
            GenresSeeder::class,
            BooksSeeder::class,
            CopiesSeeder::class,
            LoansSeeder::class
        ]);
    }
}
