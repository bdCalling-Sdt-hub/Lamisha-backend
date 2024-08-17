<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
</head>
<body>
    <h1>Title: {{ $title }}</h1>
    <h2>Description:</h2>
    <p>{{ $description }}</p>
    @if(!empty($filePaths))
        <h2>Documents:</h2>
        <ul>
            @foreach($filePaths as $filePath)
                <li><a href="{{ asset('storage/' . $filePath) }}">{{ basename($filePath) }}</a></li>
            @endforeach
        </ul>
    @endif
</body>
</html>
