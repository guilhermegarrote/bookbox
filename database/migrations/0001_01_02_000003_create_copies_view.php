<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_copies');
        DB::statement('CREATE VIEW vw_copies AS
            SELECT
                c.id AS id,
                b.id AS book_id,
                b.isbn AS isbn,
                b.title AS title,
                b.author AS author,
                b.genre_id AS genre_id,
                g.name AS genre_name,
                g.color_hex AS genre_color_hex,
                b.publisher AS publisher,
                c.number AS number,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM loans l
                        WHERE l.copy_id = c.id
                          AND l.returned_date IS NULL
                    ) THEN 0
                    ELSE 1
                END AS available
            FROM copies c
            JOIN books b ON c.book_id = b.id
            LEFT JOIN genres g ON b.genre_id = g.id
        ');
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_copies');
    }
};
