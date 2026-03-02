@foreach ($books as $book)
    <tr style="cursor: default;">
        <td class="center">
            <input type="checkbox" name="selected_books[]" style="cursor: pointer; width: 18px; height: 18px;">
        </td>

        <td>{{ $book->title }}</td>
        <td>{{ $book->author }}</td>
        <td>{{ $book->isbn }}</td>

        <td>
            <input type="text" class="input-copies" data-copies-input placeholder="{{ $book->placeholderCopies() }}" />
        </td>
    </tr>
@endforeach
