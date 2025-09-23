<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Models\View\Loan;

class loanController extends Controller
{
    public function filter()
    {
        return view('pages.loans.partials.filters');
    }

    public function index()
    {
        $filterData = Loan::getFilterData();

        $loans = Loan::select([
            'id',
            'name',
            'number',
            'title',
            'author',
            'loan_due_date',
            'loan_returned_date',
        ])->orderBy('loan_due_date')->paginate(10);

        $filterUrl = route('loans.filter.view');

        return view('pages.loans.index', compact('loans', 'filterData', 'filterUrl'));
    }

    public function createModal()
    {
        $filterData = Loan::getFilterData();

        return view('pages.loans.partials.create-modal', compact('filterData'))->render();
    }

    public function menuModal(String $id)
    {
        $loan = Loan::where('id', Utils::convertUuidToBinary($id))
            ->firstOrFail();

        return view('pages.loans.partials.menu-modal', compact('loan'))->render();
    }
}
