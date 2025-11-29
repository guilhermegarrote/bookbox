<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_loans');
        DB::statement('CREATE VIEW vw_loans AS
            SELECT
                l.id AS id,
                l.barcode_code AS barcode_code,
                c.book_id AS book_id,
                c.id AS copy_id,
                c.isbn AS isbn,
                c.title AS title,
                c.author AS author,
                c.genre_id AS genre_id,
                c.genre_name AS genre_name,
                c.genre_color_hex AS genre_color_hex,
                c.publisher AS publisher,
                c.number AS number,
                ssc.student_id AS student_id,
                ssc.name AS name,
                ssc.cpf AS cpf,
                ssc.cpf_hash AS cpf_hash,
                ssc.email AS email,
                ssc.email_hash AS email_hash,
                ssc.phone AS phone,
                ssc.phone_hash AS phone_hash,
                ssc.can_borrow AS can_borrow,
                ssc.school_class_id AS school_class_id,
                ssc.course AS course,
                ssc.term AS term,
                ssc.start_date AS school_class_start_date,
                ssc.end_date AS school_class_end_date,
                ssc.period AS period,
                ssc.formatted_class_name AS formatted_class_name,
                l.start_date AS loan_start_date,
                l.due_date AS loan_due_date,
                l.returned_date AS loan_returned_date
            FROM loans l
            LEFT JOIN vw_copies c ON c.id = l.copy_id
            LEFT JOIN vw_student_school_class ssc ON ssc.student_id = l.student_id
        ');
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_loans');
    }
};
