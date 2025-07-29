<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateBooksView extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_books');
        DB::statement("
            CREATE VIEW vw_books AS
            SELECT
                b.id AS id,
                b.isbn AS isbn,
                b.title AS title,
                b.author AS author,
                g.id AS genre_id,
                g.name AS genre_name,
                g.color_hex AS genre_color_hex,
                b.publisher AS publisher,
                CONCAT(
                    SUM(CASE WHEN c.available = 1 THEN 1 ELSE 0 END),
                    '/',
                    COUNT(0)
                ) AS available
            FROM books b
            JOIN genres g ON b.genre_id = g.id
            LEFT JOIN copies c ON c.book_id = b.id
            GROUP BY
                b.id,
                b.isbn,
                b.title,
                b.author,
                g.id,
                g.name,
                g.color_hex,
                b.publisher
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_books');
    }
}
