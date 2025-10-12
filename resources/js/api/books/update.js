import { route } from 'ziggy-js';

export async function updateBooks(bookId, data) {
    const url = route('books.update', { book: bookId });

    let response;
    try {
        response = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(data),
        });
    } catch (err) {
        throw new Error('Erro de conexão ao atualizar livro');
    }

    if (!response.ok) {
        const errorText = await response.text();
        throw new Error(errorText.message || 'Erro ao atualizar livro');
    }

    if (response.status === 204) return null;

    return await response.json();
}
