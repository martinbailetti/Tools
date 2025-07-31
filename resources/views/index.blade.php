<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Directorios Disponibles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h1>Directorios Disponibles</h1>

    <div class="directories-list">
        @foreach($directories as $directory)
            @if(count($directory['subdirectories']) > 0)
                @foreach($directory['subdirectories'] as $subdirectory)
                    <div class="directory-item">
                        <a href="/api/printable/{{ $directory['name'] }}/{{ $subdirectory['name'] }}">
                            {{ $directory['name'] }} - {{ $subdirectory['name'] }}
                        </a>
                    </div>
                @endforeach
            @else
                <div class="directory-item">
                    <a href="/printables/{{ $directory['name'] }}">
                        {{ $directory['name'] }}
                    </a>
                </div>
            @endif
        @endforeach
    </div>

</body>
</html>
