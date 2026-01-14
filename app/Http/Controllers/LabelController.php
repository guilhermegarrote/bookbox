<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Validators;
use App\Models\View\Book;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Controller responsible for generating label-related modals and views.
 */
class LabelController extends Controller
{
    /**
     * Generates the HTML for the label modal with a list of books.
     *
     * If a search term is provided, it filters books by ISBN, title, or author.
     * Otherwise, it returns all books with their copies (if needed).
     *
     * @param Request $request The incoming HTTP request containing optional 'search'
     *
     * @return View|string Rendered HTML of the label modal or the list partial if AJAX
     *
     * @see resources/views/pages/labels/partials/generate-label-modal.blade.php
     * @see resources/views/pages/labels/partials/generate-label-modal-list.blade.php
     */
    public function generateLabelModal(Request $request): View|string
    {
        $booksQuery = Book::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $numericSearch = preg_replace('/\D/', '', $search);
            $isIsbn = Validators::validateIsbn($numericSearch);

            if ($isIsbn) {
                $booksQuery->where('isbn', $numericSearch);
            } else {
                $booksQuery->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%");
                });
            }
        }

        $books = $booksQuery->with(['copies:id,book_id,number'])
            ->select(['id', 'isbn', 'title', 'author'])
            ->orderBy('title')
            ->get();

        if ($request->ajax()) {
            return view('pages.labels.partials.generate-label-modal-list', compact('books'));
        }

        return view('pages.labels.partials.generate-label-modal', compact('books'))->render();
    }
}
