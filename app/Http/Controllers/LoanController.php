<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\View\Loan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

/**
 * Controller responsible for managing loan-related pages and modals.
 */
class LoanController extends Controller
{
    /**
     * Display the loan filter modal view.
     *
     * @return View the view containing loan filter options
     *
     * @see resources/views/pages/loans/partials/filters.blade.php
     */
    public function filter(): View
    {
        return view('pages.loans.partials.filters');
    }

    /**
     * Display the main loan management page.
     *
     * Retrieves ongoing loans and sidebar data, then passes
     * them to the main loan management view.
     *
     * @return View the main loan management view
     *
     * @see resources/views/pages/loans/index.blade.php
     */
    public function index(): View
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
            ->paginate(10)
        ;

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
            ->get()
        ;

        $filterUrl = route('loans.filter.view');

        return view('pages.loans.index', compact('loans', 'loans_sidebar', 'filterData', 'filterUrl'));
    }

    /**
     * Display the modal view for creating a new loan.
     *
     * @return string the rendered HTML for the loan creation modal
     *
     * @see resources/views/pages/loans/partials/create-modal.blade.php
     */
    public function createModal(): string
    {
        $filterData = Loan::getFilterData();

        return view('pages.loans.partials.create-modal', compact('filterData'))->render();
    }

    /**
     * Display the menu modal for a specific loan.
     *
     * @param string $id the UUID of the loan
     *
     * @return string the rendered HTML for the loan menu modal
     *
     * @see resources/views/pages/loans/partials/menu-modal.blade.php
     */
    public function menuModal(string $id): string
    {
        $loan = Loan::where('id', Utils::convertUuidToBinary($id))->firstOrFail();

        return view('pages.loans.partials.menu-modal', compact('loan'))->render();
    }

    /**
     * Display the update modal for a specific loan.
     *
     * @param string $id the UUID of the loan
     *
     * @return string the rendered HTML for the loan update modal
     *
     * @see resources/views/pages/loans/partials/update-modal.blade.php
     */
    public function updateModal(string $id): string
    {
        $loan = Loan::where('id', Utils::convertUuidToBinary($id))->firstOrFail();

        return view('pages.loans.partials.update-modal', compact('loan'))->render();
    }

    /**
     * Display the modal for extending a loan due date.
     *
     * Automatically adds 7 days to the current due date
     * and renders the modal for confirmation.
     *
     * @param string $id the UUID of the loan
     *
     * @return string the rendered HTML for the loan extension modal
     *
     * @see resources/views/pages/loans/partials/extend-modal.blade.php
     */
    public function extendModal(string $id): string
    {
        $loan = Loan::where('id', Utils::convertUuidToBinary($id))->firstOrFail();

        $currentDate = Carbon::createFromFormat('d/m/Y', $loan->loan_due_date)->format('d/m/Y');

        $extendedDate = Carbon::createFromFormat('d/m/Y', $loan->loan_due_date)
            ->addDays(7)
            ->format('d/m/Y')
        ;

        return view('pages.loans.partials.extend-modal', compact('currentDate', 'extendedDate'))->render();
    }
}
