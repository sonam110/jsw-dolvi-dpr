@foreach ($data as $row)
    @foreach ($row as $cell)
        {{ $cell }},
    @endforeach
    <br>
@endforeach
