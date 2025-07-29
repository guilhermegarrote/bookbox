<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSchoolClassesView extends Migration
{
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS vw_school_classes');
        DB::statement("
            CREATE OR REPLACE VIEW vw_school_classes AS
            SELECT
                school_classes.id AS id,
                school_classes.course AS course,
                school_classes.term AS term,
                school_classes.start_date AS start_date,
                school_classes.end_date AS end_date,
                CASE
                    WHEN school_classes.term = 'Annual' THEN TIMESTAMPDIFF(YEAR, school_classes.start_date, CURDATE()) + 1
                    WHEN school_classes.term = 'Semester' THEN TIMESTAMPDIFF(MONTH, school_classes.start_date, CURDATE()) DIV 6 + 1
                    ELSE NULL
                END AS period  -- Número do período atual da turma baseado no regime e data atual
            FROM school_classes
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS vw_school_classes");
    }
}

