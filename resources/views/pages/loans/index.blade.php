@extends('layouts.app')

@section('title', 'Empréstimos')

@section('content')
    <div class="panel">
        <div class="data-container">
            <h2 class="page-title">Empréstimos</h2>

            <div class="loans-page" style="display: flex; gap: 1.5rem; align-items: flex-start;">
                <div class="sidebar-wrapper" id="sidebar-container">
                    @include('pages.loans.partials.sidebar')
                </div>

                @include('pages.loans.partials.table')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/loans/index.js')
@endpush
