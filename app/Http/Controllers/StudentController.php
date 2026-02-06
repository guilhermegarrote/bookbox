<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\View\SchoolClass;
use App\Models\View\StudentSchoolClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller responsible for managing student-related pages and modals.
 */
class StudentController extends Controller
{
    /**
     * Display the student filter modal view.
     *
     * @return View the view containing student filter options
     *
     * @see resources/views/pages/students/partials/filters.blade.php
     */
    public function filter(): View
    {
        return view('pages.students.partials.filters');
    }

    /**
     * Display the main student management page.
     *
     * Retrieves paginated student and class data for the main view.
     *
     * @return View the main student management view
     *
     * @see resources/views/pages/students/index.blade.php
     */
    public function index(Request $request)
    {
        $filterData = StudentSchoolClass::getFilterData();

        $filterUrl = route('students.filter');

        $selectData = SchoolClass::whereDate('end_date', '>', now())
            ->orderBy('course')
            ->orderBy('start_date')
            ->get();

        return view('pages.students.index', compact('filterData', 'filterUrl', 'selectData'));
    }

    /**
     * Display the modal view for creating a new student.
     *
     * @return string the rendered HTML for the student creation modal
     *
     * @see resources/views/pages/students/partials/create-modal.blade.php
     */
    public function createModal(): string
    {
        return view('pages.students.partials.create-modal')->render();
    }

    /**
     * Display the menu modal for a specific student.
     *
     * @param string $id the UUID of the student
     *
     * @return string the rendered HTML for the student menu modal
     *
     * @see resources/views/pages/students/partials/menu-modal.blade.php
     */
    public function menuModal(string $id): string
    {
        $student = StudentSchoolClass::where('student_id', Utils::convertUuidToBinary($id))->firstOrFail();

        return view('pages.students.partials.menu-modal', compact('student'))->render();
    }

    /**
     * Display the update modal for a specific student.
     *
     * @param string $id the UUID of the student
     *
     * @return string the rendered HTML for the student update modal
     *
     * @see resources/views/pages/students/partials/update-modal.blade.php
     */
    public function updateModal(string $id): string
    {
        $student = StudentSchoolClass::where('student_id', Utils::convertUuidToBinary($id))->firstOrFail();

        return view('pages.students.partials.update-modal', compact('student'))->render();
    }
}
