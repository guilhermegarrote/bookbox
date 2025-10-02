@extends('layouts.app')

@section('title', 'Alunos')

@section('content')
    <div class="panel">
        <div class="data-container">
            <h2 class="page-title">Alunos</h2>

            <div class="table-wrapper" id="data-table-container">
                @include('pages.students.partials.table', ['students' => $students])
            </div>

            <div class="pagination-container" id="pagination-wrapper">
                @include('vendor.pagination.custom', ['paginator' => $students])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/students/students.js')
@endpush
