<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\Setting;
use App\Models\View\Loan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

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
    public function index(Request $request)
    {
        $filterData = Loan::getFilterData();

        $filterUrl = route('loans.filter');

        return view('pages.loans.index', compact('filterData', 'filterUrl'));
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

        $loan_start_date = Carbon::parse($loan->loan_start_date)->format('d/m/Y');
        $loan_due_date = Carbon::parse($loan->loan_due_date)->format('d/m/Y');

        return view('pages.loans.partials.menu-modal', compact('loan', 'loan_start_date', 'loan_due_date'))->render();
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

        $extensionDays = Setting::where('key', 'extension_days')->value('value');

        $currentDate = Carbon::parse($loan->loan_due_date)->format('d/m/Y');

        $extendedDate = Carbon::parse($loan->loan_due_date)
            ->addDays((int) $extensionDays)
            ->format('d/m/Y')
        ;

        return view('pages.loans.partials.extend-modal', compact('currentDate', 'extendedDate'))->render();
    }
}
