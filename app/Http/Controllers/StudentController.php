<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Models\View\StudentSchoolClass;

class StudentController extends Controller
{
    public function filter()
    {
        return view('pages.students.partials.filters');
    }

    public function index()
    {
        $filterData = StudentSchoolClass::getFilterData();

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

    public function createModal()
    {
        $filterData = StudentSchoolClass::getFilterData();

        return view('pages.students.partials.create-modal', compact('filterData'))->render();
    }

    public function menuModal(String $id)
    {
        $student = StudentSchoolClass::where('student_id', Utils::convertUuidToBinary($id))
            ->firstOrFail();

        return view('pages.students.partials.menu-modal', compact('student'))->render();
    }
}
