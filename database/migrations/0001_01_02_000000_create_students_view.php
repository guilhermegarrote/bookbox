<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_students');
        DB::statement("CREATE VIEW vw_students AS
            SELECT
                s.id,
                s.name,
                s.cpf,
                s.cpf_hash,
                s.email,
                s.email_hash,
                s.phone,
                s.phone_hash,
                CASE
                    WHEN COUNT(l.id) < sc.value
                        AND MAX(CASE WHEN l.due_date < CURDATE() THEN 1 ELSE 0 END) = 0
                    THEN 1
                    ELSE 0
                END AS can_borrow
            FROM students s
            CROSS JOIN (
                SELECT value FROM settings WHERE `key` = 'max_book_loans' LIMIT 1
            ) sc
            LEFT JOIN loans l
                ON l.student_id = s.id
            AND l.returned_date IS NULL
            GROUP BY
                s.id, s.name, s.cpf, s.cpf_hash,
                s.email, s.email_hash,
                s.phone, s.phone_hash, sc.value
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_students');
    }
};
