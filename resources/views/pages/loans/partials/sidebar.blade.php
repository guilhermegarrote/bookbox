@php
    use Carbon\Carbon;
@endphp

<aside class="sidebar">
    <ul class="loan-list">
        @foreach ($loans_sidebar as $loan)
            @php
                try {
                    $dueDate = Carbon::createFromFormat('d/m/Y', $loan->loan_due_date);
                } catch (\Exception $e) {
                    $dueDate = Carbon::parse($loan->loan_due_date);
                }

                $daysDiff = now()->startOfDay()->diffInDays($dueDate->startOfDay(), false);
                $isOverdue = $daysDiff < 0;

                if ($isOverdue) {
                    $color = '#e74c3c';
                    $label = 'vencido há:';
                    $days = abs($daysDiff);
                } elseif ($daysDiff <= 5) {
                    $color = '#f39c12';
                    $label = 'vencimento em:';
                    $days = $daysDiff;
                } else {
                    $color = '#1fbd60ff';
                    $label = 'vencimento em:';
                    $days = $daysDiff;
                }
            @endphp

            <li class="loan-item" data-loan-id="{{ $loan->id }}">
                <div class="loan-color" style="background-color: {{ $color }}"></div>

                <div class="loan-content">
                    <div>
                        <div class="loan-title">{{ $loan->title }}</div>
                        <div class="loan-label" style="color: {{ $color }}">{{ $label }}</div>
                    </div>

                    <div class="loan-days">
                        <div class="loan-days-number">{{ $days }}</div>
                        <div class="loan-days-text">dias</div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</aside>
