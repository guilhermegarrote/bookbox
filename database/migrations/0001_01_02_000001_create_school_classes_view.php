<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
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
                CAST(
                    CASE
                        WHEN CURDATE() < sc.start_date THEN 0
                        WHEN LOWER(sc.term) = 'annual' THEN TIMESTAMPDIFF(YEAR, sc.start_date, LEAST(CURDATE(), sc.end_date)) + 1
                        WHEN LOWER(sc.term) = 'semester' THEN TIMESTAMPDIFF(MONTH, sc.start_date, LEAST(CURDATE(), sc.end_date)) DIV 6 + 1
                        ELSE NULL
                    END AS UNSIGNED
                ) AS period
            FROM school_classes sc
        ");
    }

    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS vw_school_classes');
    }
};
