<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->down();
        DB::unprepared("CREATE EVENT ev_delete_finished_school_classes
                ON SCHEDULE EVERY 1 DAY
                STARTS CURRENT_TIMESTAMP
            DO
            BEGIN
                DELETE FROM students
                WHERE id IN (
                    SELECT s.id FROM (
                        SELECT s.id
                        FROM students s
                        JOIN student_school_class ssc ON ssc.student_id = s.id
                        JOIN school_classes sc ON sc.id = ssc.school_class_id
                        WHERE sc.end_date < CURDATE()
                          AND NOT EXISTS (
                              SELECT 1 FROM loans l WHERE l.student_id = s.id AND l.returned_date IS NULL
                          )
                        GROUP BY s.id
                    ) AS s
                );
                DELETE FROM school_classes
                WHERE end_date < CURDATE()
                  AND NOT EXISTS (
                      SELECT 1 FROM student_school_class ssc WHERE ssc.school_class_id = school_classes.id
                  );
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP EVENT IF EXISTS ev_delete_finished_school_classes;");
    }
};
