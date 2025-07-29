<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateStudentsView extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_students');
        DB::statement("
            CREATE VIEW vw_students AS
            SELECT
                s.id AS id,
                s.name AS name,
                s.cpf AS cpf,
                s.cpf_hash AS cpf_hash,
                s.email AS email,
                s.email_hash AS email_hash,
                s.phone AS phone,
                s.phone_hash AS phone_hash,
                CASE
                    WHEN COUNT(DISTINCT l.id) < (
                        SELECT c.value
                        FROM settings c
                        WHERE c.key = 'max_book_loans'
                        LIMIT 1
                    )
                    AND NOT EXISTS (
                        SELECT 1
                        FROM loans l2
                        WHERE l2.student_id = s.id
                          AND l2.active IS TRUE
                          AND l2.due_date < CURDATE()
                        LIMIT 1
                    )
                    THEN 1
                    ELSE 0
                END AS can_borrow
            FROM students s
            LEFT JOIN loans l ON l.student_id = s.id AND l.active IS TRUE
            GROUP BY s.id, s.name, s.cpf, s.cpf_hash, s.email, s.email_hash, s.phone, s.phone_hash
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_students');
    }
}

