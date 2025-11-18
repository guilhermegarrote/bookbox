@extends('layouts.app')

@section('title', 'Livros')

@section('content')
    <div class="panel">
        <div class="data-container">
            <h2 class="page-title">Livros</h2>

            @include('pages.books.partials.table')
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/books/index.js')
@endpush
