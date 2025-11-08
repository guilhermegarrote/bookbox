<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateStudentSchoolClassView extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_student_school_class');
        DB::statement("
            CREATE VIEW vw_student_school_class AS
            SELECT
                ssc.id,
                ssc.student_id,
                sv.name,
                sv.cpf,
                sv.cpf_hash,
                sv.email,
                sv.email_hash,
                sv.phone,
                sv.phone_hash,
                sv.can_borrow,
                ssc.school_class_id,
                sc.course,
                sc.term,
                sc.start_date,
                sc.end_date,
                sc.period,
                CONCAT(
                    sc.period, '° ',
                    CASE sc.term
                        WHEN 'Semester' THEN 'Semestre - '
                        WHEN 'Annual'   THEN 'Ano - '
                        ELSE CONCAT(sc.term, ' - ') -- fallback, mostra o valor cru
                    END,
                    sc.course
                ) AS formatted_class_name
            FROM student_school_class ssc
            LEFT JOIN vw_students sv ON sv.id = ssc.student_id
            LEFT JOIN vw_school_classes sc ON sc.id = ssc.school_class_id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_student_school_class');
    }
}
