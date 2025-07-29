<?php

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
                ssc.id AS id,
                ssc.student_id AS student_id,
                sv.name AS name,
                sv.cpf AS cpf,
                sv.cpf_hash AS cpf_hash,
                sv.email AS email,
                sv.email_hash AS email_hash,
                sv.phone AS phone,
                sv.phone_hash AS phone_hash,
                sv.can_borrow AS can_borrow,
                ssc.school_class_id AS school_class_id,
                sc.course AS course,
                sc.term AS term,
                sc.start_date AS start_date,
                sc.end_date AS end_date,
                sc.period AS period,
                CONCAT(
                    sc.period,
                    CASE
                        WHEN LOWER(sc.term) = 'semester' THEN '° Semestre - '
                        ELSE '° Ano - '
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
