@extends('layouts.app')

@section('title', 'Alunos')

@section('content')
    <div class="panel">
        <div class="data-container">
            <h2 class="page-title">Alunos</h2>

            @include('pages.students.partials.table')
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/students/index.js')
@endpush
