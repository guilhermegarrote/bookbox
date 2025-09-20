<aside class="sidebar"
    style="width: 250px; flex-shrink: 0; background-color: #f9f9f9; padding: 1rem; border: 1px solid #ddd; border-radius: 4px;">
    <h3>Empréstimos Próximos</h3>
    <ul class="loan-list" style="list-style: none; padding: 0; margin: 0;">
        @foreach ($loans as $loan)
            <li style="margin-bottom: 0.5rem;">
                <strong>{{ $loan->title }}</strong><br>
                <small>Vence em: {{ $loan->due_date }}</small>
            </li>
        @endforeach
    </ul>
</aside>
