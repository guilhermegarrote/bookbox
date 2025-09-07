<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Loan;

class loanController extends Controller
{
    public function index()
    {
           $loans = Loan::select([
            'id',
            'student_id',
            'copy_id',
            'start_date',
            'due_date',
            'returned_date',
            'active',
        ])->orderBy('active')->paginate(10);

        //$filterUrl = route('students.filter.view');

        return view('pages.loans.index', compact('loans'));
    }
}
