@foreach ($books as $book)
    <tr>
        <td class="center">
            <input type="checkbox" name="selected_books[]">
        </td>

        <td>{{ $book->title }}</td>
        <td>{{ $book->author }}</td>
        <td>{{ $book->isbn }}</td>

        <td>
            <input type="text" class="input-copies" data-copies-input name="exemplares[{{ $book->id }}]"
                placeholder="{{ $book->placeholderCopies() }}" />
        </td>
    </tr>
@endforeach
