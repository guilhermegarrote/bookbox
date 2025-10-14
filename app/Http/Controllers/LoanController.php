<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Models\View\Loan;
use Carbon\Carbon;

class LoanController extends Controller
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
        ])
            ->whereNull('loan_returned_date')
            ->orderBy('loan_due_date', 'asc')
            ->paginate(10);

        $loans_sidebar = Loan::select([
            'id',
            'name',
            'number',
            'title',
            'author',
            'loan_due_date',
            'loan_returned_date',
        ])
            ->whereNull('loan_returned_date')
            ->orderBy('loan_due_date', 'asc')
            ->get();

        $filterUrl = route('loans.filter.view');

        return view('pages.loans.index', compact('loans', 'loans_sidebar', 'filterData', 'filterUrl'));
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

    public function updateModal(String $id)
    {
        $loan = Loan::where('id', Utils::convertUuidToBinary($id))
            ->firstOrFail();

        return view('pages.loans.partials.update-modal', compact('loan'))->render();
    }

    public function extendModal(string $id)
    {
        $loan = Loan::where('id', Utils::convertUuidToBinary($id))->firstOrFail();

        $currentDate = Carbon::createFromFormat('d/m/Y', $loan->loan_due_date)->format('d/m/Y');

        $extendedDate = Carbon::createFromFormat('d/m/Y', $loan->loan_due_date)
            ->addDays(7)
            ->format('d/m/Y');

        return view('pages.loans.partials.extend-modal', compact('currentDate', 'extendedDate'))->render();
    }
}
