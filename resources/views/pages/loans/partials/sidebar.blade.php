@php
    use Carbon\Carbon;
@endphp

<aside class="sidebar"
    style="width: 260px; flex-shrink: 0; background-color: #f9f9f9; padding: 1rem; border: 1px solid #ddd; border-radius: 8px;">
    <h3 style="font-size: 1rem; margin-bottom: 1rem; color: #333; font-weight: 600;">
        Empréstimos Próximos
    </h3>

    <ul class="loan-list"
        style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.6rem;">

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
                    $color = '#2ecc71';
                    $label = 'vencimento em:';
                    $days = $daysDiff;
                }
            @endphp

            <li data-loan-id="{{ $loan->id }}"
                style="display: flex; align-items: stretch; border: 1px solid #e0e0e0; border-radius: 6px; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05); overflow: hidden; cursor: pointer; transition: transform 0.1s ease-in-out;"
                onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">

                <div style="width: 6px; background-color: {{ $color }}; align-self: stretch;"></div>

                <div style="padding: 0.6rem 0.8rem; flex: 1; display: flex; justify-content: space-between; align-items: baseline;">
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem; color: #333; margin-bottom: 0.25rem;">
                            {{ $loan->title }}
                        </div>
                        <div style="font-size: 0.75rem; color: {{ $color }}; font-weight: 500;">
                            {{ $label }}
                        </div>
                    </div>

                    <div style="text-align: right; min-width: 42px;">
                        <div style="font-weight: 700; font-size: 1rem; color: #333;">
                            {{ $days }}
                        </div>
                        <div style="font-size: 0.75rem; color: #555;">dias</div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</aside>
