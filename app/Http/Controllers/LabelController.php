<?php

namespace App\Http\Controllers;

use App\Models\View\Book;

class LabelController extends Controller
{
    public function generateLabelModal()
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
        ->paginate(10);

        return view('pages.labels.partials.generate-label-modal', compact('books'))->render();
    }
}
