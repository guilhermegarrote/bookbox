@extends('layouts.app')

@section('content')
    <div class="panel">
        <div class="data-container">
            <h2 class="page-title">Empréstimos</h2>

            <div class="table-wrapper" id="data-table-container">
                @include('pages.loans.partials.table', ['loans' => $loans])
            </div>

            <div class="pagination-container" id="pagination-wrapper">
                @include('vendor.pagination.custom', ['paginator' => $loans])
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/pages/loans/loans.js')
@endpush