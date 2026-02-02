<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $this->down();
        DB::unprepared("CREATE VIEW vw_students AS
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
                    WHEN COUNT(l.id) < st.value
                        AND MAX(CASE WHEN l.due_date < CURDATE() THEN 1 ELSE 0 END) = 0
                        AND MAX(CASE WHEN cls.end_date < CURDATE() THEN 1 ELSE 0 END) = 0
                    THEN 1
                    ELSE 0
                END AS can_borrow
            FROM students s
            CROSS JOIN (
                SELECT value FROM settings WHERE `key` = 'max_book_loans' LIMIT 1
            ) st
            LEFT JOIN loans l
                ON l.student_id = s.id
                AND l.returned_date IS NULL
            LEFT JOIN student_school_class ssc
                ON ssc.student_id = s.id
            LEFT JOIN school_classes cls
                ON cls.id = ssc.school_class_id
            GROUP BY
                s.id, s.name, s.cpf, s.cpf_hash,
                s.email, s.email_hash,
                s.phone, s.phone_hash, st.value
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS vw_students');
    }
};
