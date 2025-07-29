<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCopiesView extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_copies');
        DB::statement("
            CREATE VIEW vw_copies AS
            SELECT
                c.id AS id,
                b.id AS book_id,
                b.isbn AS isbn,
                b.title AS title,
                b.author AS author,
                b.genre_id AS genre_id,
                b.genre_name AS genre_name,
                b.genre_color_hex AS genre_color_hex,
                b.publisher AS publisher,
                c.number AS number,
                c.available AS available
            FROM copies c
            JOIN vw_books b ON c.book_id = b.id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_copies');
    }
}
