@php
$width = 20;
$height = 30;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test FontMapping</title>
    <x-dynamic-fonts />
</head>
<body>
    <div>{{ \App\Services\FontMappingService::mapHardcodedToFamily("'NotoSerif'") }}</div>
</body>
</html>
