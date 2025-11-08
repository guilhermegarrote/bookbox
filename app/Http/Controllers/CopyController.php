<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\View\Book;
use App\Models\View\Copy;
use Illuminate\Contracts\View\View;

/**
 * Controller responsible for managing copy-related pages and modals.
 */
class CopyController extends Controller
{
    /**
     * Display the book filter modal view (shared with book management).
     *
     * @return View the view containing book filter options
     *
     * @see resources/views/pages/books/partials/filters.blade.php
     */
    public function filter(): View
    {
        return view('pages.books.partials.filters');
    }

    /**
     * Display the main book listing view for copy management context.
     *
     * @return View the view displaying the list of books and related filters
     *
     * @see resources/views/pages/books/index.blade.php
     */
    public function index(): View
    {
        $filterData = Book::getFilterData();

        $books = Book::select([
            'id',
            'isbn',
            'title',
            'author',
            'genre_name',
            'publisher',
            'available_copies',
            'total_copies',
        ])->orderBy('title')->paginate(10);

        $filterUrl = route('books.filter.view');

        return view('pages.books.index', compact('books', 'filterData', 'filterUrl'));
    }

    /**
     * Display the modal view for creating a new book (shared form).
     *
     * @return string the rendered HTML for the creation modal
     *
     * @see resources/views/pages/books/partials/create-modal.blade.php
     */
    public function createModal(): string
    {
        $filterData = Book::getFilterData();

        return view('pages.books.partials.create-modal', compact('filterData'))->render();
    }

    /**
     * Display the copy management modal for a specific book.
     *
     * @param string $id the UUID of the book whose copies are being managed
     *
     * @return string the rendered HTML for the copy manager modal
     *
     * @see resources/views/pages/copies/partials/manager-modal.blade.php
     */
    public function managerModal(string $id): string
    {
        $copies = Copy::where('book_id', Utils::convertUuidToBinary($id))->firstOrFail();

        return view('pages.copies.partials.manager-modal', compact('copies'))->render();
    }
}
