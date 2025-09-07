@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="data-container">
            <h2 class="page-title">Livros</h2>

            <div class="table-wrapper" id="data-table-container">
                @include('pages.books.partials.table', ['books' => $books])
            </div>

            <div class="pagination-container" id="pagination-wrapper">
                @include('vendor.pagination.custom', ['paginator' => $books])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/books/books.js')
@endpush