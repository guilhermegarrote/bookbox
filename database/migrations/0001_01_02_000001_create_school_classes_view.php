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
                sc.id,
                sc.course,
                sc.term,
                sc.start_date,
                sc.end_date,
                CASE
                    WHEN CURDATE() < sc.start_date THEN 0
                    WHEN CURDATE() > sc.end_date THEN NULL
                    WHEN sc.term = 'Annual'
                        THEN TIMESTAMPDIFF(YEAR, sc.start_date, CURDATE()) + 1
                    WHEN sc.term = 'Semester'
                        THEN TIMESTAMPDIFF(MONTH, sc.start_date, CURDATE()) DIV 6 + 1
                    ELSE NULL
                END AS period
            FROM school_classes sc
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS vw_school_classes");
    }
}

