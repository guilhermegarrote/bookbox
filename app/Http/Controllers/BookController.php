<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\View\Book;

class BookController extends Controller
{
    public function index()
    {
           $books = Book::select([
            'id',
            'isbn',
            'title',
            'author',
            'genre_id',
            'genre_name',
            'genre_color_hex',
            'publisher',
            'available',
        ])->orderBy('title')->paginate(10);

        //$filterUrl = route('students.filter.view');

        return view('pages.books.index', compact('books'));
    }
}
