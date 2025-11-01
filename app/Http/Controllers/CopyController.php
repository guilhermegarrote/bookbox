<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Models\View\Book;
use App\Models\View\Copy;

class CopyController extends Controller
{
    public function filter()
    {
        return view('pages.books.partials.filters');
    }

    public function index()
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
            'total_copies'
        ])->orderBy('title')->paginate(10);

        $filterUrl = route('books.filter.view');

        return view('pages.books.index', compact('books', 'filterData', 'filterUrl'));
    }

    public function createModal()
    {
        $filterData = Book::getFilterData();

        return view('pages.books.partials.create-modal', compact('filterData'))->render();
    }

    public function managerModal(String $id)
    {
        $copies = Copy::where('book_id', Utils::convertUuidToBinary($id))
            ->firstOrFail();

        return view('pages.copies.partials.manager-modal', compact('copies'))->render();
    }
}
