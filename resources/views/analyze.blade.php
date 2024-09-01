<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Japanese Text Analyzer</title>
</head>
<body>
    <h1>Japanese Text Analyzer</h1>
    <div>Please input a Japanese sentense</div>
    <form action="{{ route('analyze') }}" method="POST">
        @csrf
        <textarea name="text" rows="5" cols="50">{{ old('text', $text ?? '') }}</textarea><br>
        <button type="submit">Analyze</button>
    </form>

    @if (isset($results))
        <h2>Results:</h2>
        <ul>
            @foreach ($results as $result)
                <li>
                    <strong>{{ $result['word'] }}</strong>:
                    @foreach ($result['meanings'] as $meaning)
                        {{ $meaning['senses'][0]['english_definitions'][0] }},
                    @endforeach
                </li>
            @endforeach
        </ul>
    @endif
</body>
</html>
