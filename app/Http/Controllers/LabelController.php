<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\View\Book;
use Illuminate\Contracts\View\View;

/**
 * Controller responsible for generating label-related modals and views.
 */
class LabelController extends Controller
{
    /**
     * Display the modal view for generating book labels.
     *
     * Retrieves a paginated list of books for label generation.
     *
     * @return string the rendered HTML for the label generation modal
     *
     * @see resources/views/pages/labels/partials/generate-label-modal.blade.php
     */
    public function generateLabelModal(): string
    {
        $books = Book::select([
            'id',
            'isbn',
            'title',
            'author',
            'genre_name',
            'publisher',
            'available_copies',
            'total_copies',
        ])
            ->orderBy('title')
            ->paginate(10)
        ;

        return view('pages.labels.partials.generate-label-modal', compact('books'))->render();
    }
}
