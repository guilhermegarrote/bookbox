<h2>Gerenciar Usuários</h2>
<div class="card-list">
    @php
        $users = ['Administrador', 'Professor', 'Aluno'];
    @endphp
    @foreach($users as $user)
        <div class="card">
            <span>{{ $user }}</span>
            <div class="actions">
                <button class="btn-icon edit">✏️</button>
                <button class="btn-icon delete">🗑️</button>
            </div>
        </div>
    @endforeach
</div>
