import { route } from 'ziggy-js';

export async function deleteBooks(bookId) {
    const url = route('books.destroy', { book: bookId });

    const response = await fetch(url, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    });

    if (!response.ok) {
        const contentType = response.headers.get('content-type');
        let errorMessage = 'Erro ao deletar livro';
        if (contentType && contentType.includes('application/json')) {
            const errorData = await response.json();
            errorMessage = errorData.message || errorMessage;
        } else {
            const text = await response.text();
            if (text) errorMessage = text;
        }
        throw new Error(errorMessage);
    }

    const contentLength = response.headers.get('content-length');
    if (contentLength && parseInt(contentLength) > 0) {
        return await response.json();
    }

    return null;
}
