<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class BookController extends Controller
{
    public function index()
    {
        return view('pages.book.index');
    }
}
