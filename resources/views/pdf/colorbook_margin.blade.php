@php
$width = $layout['width']-($layout['margin-in']+$layout['margin-out']);
$height = $layout['height']-($layout['margin-in']+$layout['margin-out']);

$versoWhiteLayerWidth = $width - $layout['verso']['margin'] * 2;
$versoWhiteLayerHeight = $height - $layout['verso']['margin'] * 2;


$versoMainLayerMargin = $layout['verso']['margin']+ $layout['verso']['border-margin'];

$versoMainLayerWidth = $versoWhiteLayerWidth - $layout['verso']['border-margin']*2;
$versoMainLayerHeight = $versoWhiteLayerHeight - $layout['verso']['border-margin']*2;

$versoImageLayerMargin = $versoMainLayerMargin+ $layout['verso']['image-margin'];

$versoImageLayerWidth = $versoMainLayerWidth - $layout['verso']['image-margin']*2;
$versoImageLayerHeight = $versoMainLayerHeight - $layout['verso']['image-margin']*2;


$versoText1Top = $height* $layout['verso']['text-y-percentage'] - $layout['verso']['primary-font-size'];
$versoText2Top = $versoText1Top+ $layout['verso']['primary-font-size']+ $layout['verso']['text-margin'];

$rectoBlackLayerWidth = $width - $layout['recto']['margin'] * 2;
$rectoBlackLayerHeight = $height - $layout['recto']['margin'] * 2;

$rectoImageLayerWidth = $width - $layout['recto']['margin']*2  - $layout['recto']['image-margin']*2;
$rectoImageLayerHeight = $height - $layout['recto']['margin']*2  - $layout['recto']['image-margin']*2;

$rectoImageLayerMargin = $layout['recto']['margin']+ $layout['recto']['image-margin'];
$rectoTextTop = $height* $layout['recto']['text-y-percentage'] - $layout['recto']['font-size'];


$firstPageTitleLine1Top = $height* $layout['page1']['title-line-1-percentage'] - $layout['page1']['title-line-1-font-size'];
$firstPageTitleLine2Top = $firstPageTitleLine1Top + $layout['page1']['title-line-1-font-size'] + $layout['page1']['title-line-2-margin'];
$firstPageTitleLine3Top = $firstPageTitleLine2Top + $layout['page1']['title-line-2-font-size'] + $layout['page1']['title-line-3-margin'];

@endphp
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
    <div style="font-family: 'NotoSerif';width:{{ $width }}cm;height:{{ $height }}cm;top:{{ $layout['margin-out'] }}cm;left:{{ $layout['margin-in'] }}cm;margin:0cm 0cm;overflow:hidden;position:relative; ">

        <div style="font-size:{{ $layout['page1']['title-line-1-font-size'] }}cm;line-height:{{ $layout['page1']['title-line-1-font-size'] }}cm; position:absolute; width:{{ $width }}cm;top:{{ $firstPageTitleLine1Top }}cm; text-align: center;">
        {!! htmlspecialchars_decode($layout['page1']['title-line-1'], ENT_QUOTES) !!}
        </div>
        <div style="font-size:{{ $layout['page1']['title-line-2-font-size'] }}cm;line-height:{{ $layout['page1']['title-line-2-font-size'] }}cm; position:absolute; width:{{ $width }}cm;top:{{ $firstPageTitleLine2Top }}cm; text-align: center;">
        {!! htmlspecialchars_decode($layout['page1']['title-line-2'], ENT_QUOTES) !!}
        </div>
        <div style="font-size:{{ $layout['page1']['title-line-3-font-size'] }}cm;line-height:{{ $layout['page1']['title-line-3-font-size'] }}cm; position:absolute; width:{{ $width }}cm;top:{{ $firstPageTitleLine3Top }}cm; text-align: center;">
        {!! htmlspecialchars_decode($layout['page1']['title-line-3'], ENT_QUOTES) !!}
        </div>

    </div>

    @foreach($items as $index => $item)

    <div style="width:{{ $width }}cm;height:{{ $height }}cm;top:{{ $layout['margin-out'] }}cm;left:{{ $layout['margin-in'] }}cm;background-color:black;margin:0cm 0cm;overflow:hidden;position:relative; ">
        <div style="position:absolute;width:{{ $versoWhiteLayerWidth }}cm;height:{{ $versoWhiteLayerHeight }}cm;top:{{ $layout['verso']['margin'] }}cm;left:{{ $layout['verso']['margin'] }}cm;background-color:white;"> </div>

        <div style="position:absolute; width:{{ $versoMainLayerWidth }}cm;height:{{ $versoMainLayerHeight  }}cm;top:{{ $versoMainLayerMargin }}cm;left:{{ $versoMainLayerMargin }}cm; background-color: black;">
        </div>
        <div style="position:absolute; width:{{  $versoImageLayerWidth }}cm;height:{{ $versoImageLayerHeight}}cm;top:{{ $versoImageLayerMargin }}cm;left:{{ $versoImageLayerMargin }}cm; background-image: url('{{ $item['image']['url'] }}'); background-size: cover; background-position: center;">
        </div>

        <div style="position:absolute;  width:{{ $versoMainLayerWidth }}cm;height:{{ $versoMainLayerHeight  }}cm;top:{{ $versoMainLayerMargin }}cm;left:{{ $versoMainLayerMargin }}cm; background-color: black; opacity:0.8;">
        </div>
        <div style="position:absolute; width:{{ $versoMainLayerWidth }}cm;left:{{  $versoMainLayerMargin }}cm;top:{{ $versoText1Top }}cm;line-height:{{ $layout['verso']['primary-font-size'] }}cm;font-size:{{ $layout['verso']['primary-font-size'] }}cm; color:white; font-family: 'NotoSerif'; text-align:center;">
            {!! htmlspecialchars_decode($item['es'], ENT_QUOTES) !!}
        </div>
        <div style="position:absolute; width:{{ $versoMainLayerWidth }}cm;left:{{ $versoMainLayerMargin }}cm;top:{{ $versoText2Top }}cm;line-height:{{ $layout['verso']['secondary-font-size'] }}cm;font-size:{{ $layout['verso']['secondary-font-size'] }}cm; color:white; font-family: 'NotoSerifItalic'; text-align:center;">
            {!! htmlspecialchars_decode($item['sy2'], ENT_QUOTES) !!}
        </div>
    </div>

    <div style="width:{{ $width }}cm;height:{{ $height }}cm;top:{{ $layout['margin-out'] }}cm;left:{{ $layout['margin-in'] }}cm;margin:0cm 0cm;overflow:hidden;position:relative; ">
        <div style="position:absolute;width:{{ $rectoBlackLayerWidth }}cm;height:{{ $rectoBlackLayerHeight }}cm;top:{{ $layout['recto']['margin'] }}cm;left:{{ $layout['recto']['margin'] }}cm;background-color:black;"> </div>
        <div style="position:absolute; width:{{ $rectoImageLayerWidth }}cm;height:{{ $rectoImageLayerHeight }}cm;top:{{ $rectoImageLayerMargin }}cm;left:{{ $rectoImageLayerMargin }}cm; background-image: url('{{ $item['image']['url'] }}'); background-size: cover; background-position: center;">
        </div>
        <div style="position:absolute; width:{{$rectoImageLayerWidth }}cm;left:{{ $rectoImageLayerMargin }}cm;top:{{ $rectoTextTop  }}cm;font-size:{{ $layout['recto']['font-size'] }}cm;line-height:{{ $layout['recto']['font-size'] }}cm; color:black; font-family: 'Zen'; text-align:center;transform: translate(-0.1cm, -0.1cm);">
            {{ $item['sym'] }}
        </div>
        <div style="position:absolute; width:{{ $rectoImageLayerWidth }}cm;left:{{ $rectoImageLayerMargin }}cm;top:{{ $rectoTextTop }}cm;font-size:{{ $layout['recto']['font-size'] }}cm;line-height:{{ $layout['recto']['font-size'] }}cm; color:black; font-family: 'Zen'; text-align:center;transform: scale(1.03, 1.03);">
            {{ $item['sym'] }}
        </div>
        <div style="position:absolute; width:{{ $rectoImageLayerWidth }}cm;left:{{ $rectoImageLayerMargin }}cm;top:{{ $rectoTextTop }}cm;font-size:{{ $layout['recto']['font-size'] }}cm;line-height:{{ $layout['recto']['font-size'] }}cm; color:black; font-family: 'Zen'; text-align:center;transform: scale(0.97, 0.97);">
            {{ $item['sym'] }}
        </div>
        <div style="position:absolute; width:{{ $rectoImageLayerWidth }}cm;left:{{ $rectoImageLayerMargin }}cm;top:{{ $rectoTextTop }}cm;font-size:{{ $layout['recto']['font-size'] }}cm;line-height:{{ $layout['recto']['font-size'] }}cm; color:white; font-family: 'Zen'; text-align:center;">
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
