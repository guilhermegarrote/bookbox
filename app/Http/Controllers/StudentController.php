<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\View\StudentSchoolClass;

class StudentController extends Controller
{
    private function getFilterData()
    {
        return StudentSchoolClass::groupBy('course', 'period', 'term')
            ->orderBy('course')
            ->orderBy('period')
            ->orderBy('term')
            ->get(['course', 'period', 'term']);
    }

    public function index()
    {
        $filterData = $this->getFilterData();

        $students = StudentSchoolClass::select([
            'student_id',
            'name',
            'email',
            'phone',
            'can_borrow',
            'formatted_class_name',
        ])->orderBy('name')->paginate(10);

        $filterUrl = route('students.filter.view');

        return view('pages.students.index', compact('students', 'filterData', 'filterUrl'));
    }

    public function filter()
    {
        $filterData = $this->getFilterData();

        $courses = $filterData->pluck('course')->unique()->values();
        $periods = $filterData->pluck('period')->unique()->values();
        $terms = $filterData->pluck('term')->unique()->values();

        return view('pages.students.partials.filters', compact('courses', 'periods', 'terms'));
    }

    public function createModal()
    {
        $filterData = $this->getFilterData();

        return view('pages.students.partials.create-modal', compact('filterData'))->render();
    }

    public function menuModal(StudentSchoolClass $student)
    {
        return view('pages.students.partials.menu-modal', compact('student'))->render();
    }
}
