<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class LoanController extends Controller
{
    public function index()
    {
        return view('pages.loans.index');
    }
}
