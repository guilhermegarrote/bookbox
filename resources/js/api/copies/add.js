import { route } from 'ziggy-js';

export async function addCopies(bookId, data) {
    const url = route('books.addCopies', { book: bookId });

    const response = await fetch(url, {
        method: 'PATCH',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(data),
    });

    if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Erro ao cadastrar exemplares');
    }

    return await response.json();
}
