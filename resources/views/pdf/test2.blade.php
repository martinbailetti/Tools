@php
$test = 'simple test';
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>Test 2</title>
    <x-dynamic-fonts />
</head>
<body>
    <div>{{ $test }}</div>
    @foreach([] as $item)
        <div>Empty loop</div>
    @endforeach
</body>
</html>
