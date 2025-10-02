@extends('layouts.app')

@section('title', 'Empréstimos')

@section('content')
    <div class="panel">
        <div class="data-container">
            <h2 class="page-title">Empréstimos</h2>

            <div class="loans-page" style="display: flex; gap: 1.5rem; align-items: flex-start;">
                <div class="sidebar-wrapper" id="sidebar-container">
                    @include('pages.loans.partials.sidebar', ['loans' => $loans])
                </div>

                <div class="table-wrapper" id="data-table-container" style="flex: 1;">
                    @include('pages.loans.partials.table', ['loans' => $loans])
                </div>
            </div>

            <div class="pagination-container" id="pagination-wrapper" style="margin-top: 1rem;">
                @include('vendor.pagination.custom', ['paginator' => $loans])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/loans/loans.js')
@endpush
