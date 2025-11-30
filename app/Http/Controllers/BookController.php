<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Models\View\Book;
use Illuminate\Contracts\View\View;

/**
 * Controller responsible for managing book-related pages and modals.
 */
class BookController extends Controller
{
    /**
     * Display the book filter modal view.
     *
     * @return View the view containing book filter options
     *
     * @see resources/views/pages/books/partials/filters.blade.php
     */
    public function filter()
    {
        return view('pages.books.partials.filters');
    }

    /**
     * Display the main book management page.
     *
     * Retrieves paginated book data along with filter options
     * and passes them to the view.
     *
     * @return View the main book management view
     *
     * @see resources/views/pages/books/index.blade.php
     */
    public function index(): View
    {
        $filterData = Book::getFilterData();

        $filterUrl = route('books.filter');

        return view('pages.books.index', compact('filterData', 'filterUrl'));
    }

    /**
     * Display the modal view for creating a new book.
     *
     * @return string the rendered HTML for the book creation modal
     *
     * @see resources/views/pages/books/partials/create-modal.blade.php
     */
    public function createModal(): string
    {
        $filterData = Book::getFilterData();

        return view('pages.books.partials.create-modal', compact('filterData'))->render();
    }

    /**
     * Display the menu modal view for a specific book.
     *
     * @param string $id the UUID of the book
     *
     * @return string the rendered HTML for the book menu modal
     *
     * @see resources/views/pages/books/partials/menu-modal.blade.php
     */
    public function menuModal(string $id): string
    {
        $book = Book::where('id', Utils::convertUuidToBinary($id))->firstOrFail();

        return view('pages.books.partials.menu-modal', compact('book'))->render();
    }

    /**
     * Display the update modal view for a specific book.
     *
     * @param string $id the UUID of the book
     *
     * @return string the rendered HTML for the book update modal
     *
     * @see resources/views/pages/books/partials/update-modal.blade.php
     */
    public function updateModal(string $id): string
    {
        $book = Book::where('id', Utils::convertUuidToBinary($id))->firstOrFail();

        return view('pages.books.partials.update-modal', compact('book'))->render();
    }
}
