<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 0cm 0cm;
        }

        @font-face {
            font-family: 'Zen';
            src: url('{{ resource_path('fonts/ZenAntique-Regular.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'NotoSerif';
            src: url('{{ resource_path('fonts/NotoSerif-Regular.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'NotoSerifItalic';
            src: url('{{ resource_path('fonts/NotoSerif-Italic.ttf') }}') format('truetype');
        }



        .page-break {
            page-break-after: always;
        }

    </style>
</head>
<body style="margin: 0cm 0cm;height:100%;">


    @foreach($items as $index => $item)

    <div style="width:{{ $layout['width'] }}cm;height:{{ $layout['height'] }}cm;background-color:black;margin:0cm 0cm;overflow:hidden;position:relative; ">
        <div style="position:absolute;width:{{ $layout['width']  - $layout['verso']['margin']*2 }}cm;height:{{ $layout['height']  - $layout['verso']['margin']*2 }}cm;top:{{ $layout['verso']['margin'] }}cm;left:{{ $layout['verso']['margin'] }}cm;background-color:white;"> </div>

        <div style="position:absolute; width:{{ $layout['width']  - $layout['verso']['margin']*2  - $layout['verso']['border-margin']*2 }}cm;height:{{ $layout['height']  - $layout['verso']['margin']*2  - $layout['verso']['border-margin']*2 }}cm;top:{{ $layout['verso']['margin']+ $layout['verso']['border-margin'] }}cm;left:{{ $layout['verso']['margin']+ $layout['verso']['border-margin'] }}cm; background-color: black;">
        </div>
        <div style="position:absolute; width:{{ $layout['width']  - $layout['verso']['margin']*2  - $layout['verso']['border-margin']*2 - $layout['verso']['image-margin']*2 }}cm;height:{{ $layout['height']  - $layout['verso']['margin']*2  - $layout['verso']['border-margin']*2 - $layout['verso']['image-margin']*2 }}cm;top:{{$layout['verso']['margin']+ $layout['verso']['border-margin']+ $layout['verso']['image-margin'] }}cm;left:{{$layout['verso']['margin']+ $layout['verso']['border-margin']+ $layout['verso']['image-margin'] }}cm; background-image: url('{{ $item['image']['url'] }}'); background-size: cover; background-position: center;">
        </div>

        <div style="position:absolute;  width:{{ $layout['width']  - $layout['verso']['margin']*2  - $layout['verso']['border-margin']*2 }}cm;height:{{ $layout['height']  - $layout['verso']['margin']*2  - $layout['verso']['border-margin']*2 }}cm;top:{{ $layout['verso']['margin']+ $layout['verso']['border-margin'] }}cm;left:{{ $layout['verso']['margin']+ $layout['verso']['border-margin'] }}cm; background-color: black; opacity:0.8;">
        </div>
        <div style="position:absolute; width:{{ $layout['width']  - $layout['verso']['margin']*2  - $layout['verso']['border-margin']*2 }}cm;left:{{ $layout['verso']['margin']+ $layout['verso']['border-margin'] }}cm;top:{{ $layout['height']* $layout['verso']['text-y-percentage'] }}cm;font-size:{{ $layout['verso']['primary-font-size'] }}cm; color:white; font-family: 'NotoSerif'; text-align:center;">
            {!! htmlspecialchars_decode($item['es'], ENT_QUOTES) !!}
        </div>
        <div style="position:absolute; width:{{ $layout['width']  - $layout['verso']['margin']*2  - $layout['verso']['border-margin']*2 }}cm;left:{{ $layout['verso']['margin']+ $layout['verso']['border-margin'] }}cm;top:{{ $layout['height']* $layout['verso']['text-y-percentage']+ $layout['verso']['primary-font-size']+ $layout['verso']['text-margin'] }}cm;font-size:{{ $layout['verso']['secondary-font-size'] }}cm; color:white; font-family: 'NotoSerifItalic'; text-align:center;">
            {!! htmlspecialchars_decode($item['sy2'], ENT_QUOTES) !!}
        </div>
    </div>

    <div style="width:{{ $layout['width'] }}cm;height:{{ $layout['height'] }}cm;margin:0cm 0cm;overflow:hidden;position:relative; ">
        <div style="position:absolute;width:{{ $layout['width']  - $layout['recto']['margin']*2 }}cm;height:{{ $layout['height']  - $layout['recto']['margin']*2 }}cm;top:{{ $layout['recto']['margin'] }}cm;left:{{ $layout['recto']['margin'] }}cm;background-color:black;"> </div>
        <div style="position:absolute; width:{{ $layout['width']  - $layout['recto']['margin']*2  - $layout['recto']['image-margin']*2 }}cm;height:{{ $layout['height']  - $layout['recto']['margin']*2  - $layout['recto']['image-margin']*2 }}cm;top:{{ $layout['recto']['margin']+ $layout['recto']['image-margin'] }}cm;left:{{ $layout['recto']['margin']+ $layout['recto']['image-margin'] }}cm; background-image: url('{{ $item['image']['url'] }}'); background-size: cover; background-position: center;">
        </div>
        <div style="position:absolute; width:{{ $layout['width']  - $layout['recto']['margin']*2  - $layout['recto']['image-margin']*2 }}cm;left:{{ $layout['recto']['margin']+ $layout['recto']['image-margin'] }}cm;top:{{ $layout['recto']['text-y-percentage']* $layout['height']  }}cm;font-size:{{ $layout['recto']['font-size'] }}cm; color:black; font-family: 'Zen'; text-align:center;transform: translate(-0.1cm, -0.1cm);">
            {{ $item['sym'] }}
        </div>


        <div style="position:absolute; width:{{ $layout['width']  - $layout['recto']['margin']*2  - $layout['recto']['image-margin']*2 }}cm;left:{{ $layout['recto']['margin']+ $layout['recto']['image-margin'] }}cm;top:{{ $layout['recto']['text-y-percentage']* $layout['height'] }}cm;font-size:{{ $layout['recto']['font-size'] }}cm; color:black; font-family: 'Zen'; text-align:center;transform: scale(1.03, 1.03);">
            {{ $item['sym'] }}
        </div>
        <div style="position:absolute; width:{{ $layout['width']  - $layout['recto']['margin']*2  - $layout['recto']['image-margin']*2 }}cm;left:{{ $layout['recto']['margin']+ $layout['recto']['image-margin'] }}cm;top:{{ $layout['recto']['text-y-percentage']* $layout['height'] }}cm;font-size:{{ $layout['recto']['font-size'] }}cm; color:black; font-family: 'Zen'; text-align:center;transform: scale(0.97, 0.97);">
            {{ $item['sym'] }}
        </div>
        <div style="position:absolute; width:{{ $layout['width']  - $layout['recto']['margin']*2  - $layout['recto']['image-margin']*2 }}cm;left:{{ $layout['recto']['margin']+ $layout['recto']['image-margin'] }}cm;top:{{ $layout['recto']['text-y-percentage']* $layout['height'] }}cm;font-size:{{ $layout['recto']['font-size'] }}cm; color:white; font-family: 'Zen'; text-align:center;">
            {{ $item['sym'] }}
        </div>
    </div>
    {{--
    @if($index < count($items) - 1) <div class="page-break">
        </div>
        @endif --}}
    @endforeach
</body>
</html>
