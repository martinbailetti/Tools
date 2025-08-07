@php

// Función helper para obtener el texto correcto según el idioma
function getTextForPage($pageData, $selectedLanguage) {
$textKey = 'text_' . $selectedLanguage;

// Si existe la clave específica del idioma, usarla
if (isset($pageData[$textKey]) && !empty($pageData[$textKey])) {
return $pageData[$textKey];
}

// Si no, usar la clave genérica 'text' como fallback
return $pageData['text'] ?? '';
}

$width=$layout['width']-($layout['margin-in']+$layout['margin-out']);
$height=$layout['height']-2*$layout['margin-out'];
$versoWhiteLayerWidth=$width - $layout['verso']['margin'] * 2;
$versoWhiteLayerHeight=$height - $layout['verso']['margin'] * 2;
$versoMainLayerMargin=$layout['verso']['margin']+ $layout['verso']['border-margin'];
$versoMainLayerWidth=$versoWhiteLayerWidth - $layout['verso']['border-margin']*2;
$versoMainLayerHeight=$versoWhiteLayerHeight - $layout['verso']['border-margin']*2;
$versoImageLayerMargin=$versoMainLayerMargin+ $layout['verso']['image-margin'];
$versoImageLayerWidth=$versoMainLayerWidth - $layout['verso']['image-margin']*2;
$versoImageLayerHeight=$versoMainLayerHeight - $layout['verso']['image-margin']*2;

$versoText1Top=$layout['verso']['text-1-top'];
$versoText2Top=$layout['verso']['text-2-top'];


$rectoBlackLayerWidth=$width - $layout['recto']['margin'] * 2;
$rectoBlackLayerHeight=$height - $layout['recto']['margin'] * 2;
$rectoImageLayerWidth=$width - $layout['recto']['margin']*2 - $layout['recto']['image-margin']*2;
$rectoImageLayerHeight=$height - $layout['recto']['margin']*2 - $layout['recto']['image-margin']*2;
$rectoImageLayerMargin=$layout['recto']['margin']+ $layout['recto']['image-margin'];
$rectoTextTop=$layout['recto']['text-top'];


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

        p {
            margin: 0;
            padding: 0;
        }

    </style>



    <style>
        p {
            margin: 0;
            padding: 0;
        }

        .ql-align-center {
            text-align: center;
        }

        .ql-align-left {
            text-align: left;
        }

        .ql-align-right {
            text-align: right;
        }

    </style>
</head>
<body style="margin: 0cm 0cm;height:100%;">

    @if(!$preview || (isset($previewPages) && in_array('page1', $previewPages)))
    <x-page preview="{{$preview}}" width="{{ $width }}" height="{{ $height }}" marginTop="{{ $layout['margin-out'] }}" marginLeft="{{ $layout['margin-in'] }}">

        @if(!empty($layout['page1']['background-url']))
        <x-styled-div backgroundImage="{{ $layout['page1']['background-url'] }}" backgroundSize="cover" backgroundPosition="center" width="{{ $width }}cm" height="{{ $height }}cm" top="0cm" left="0cm" position="absolute">
        </x-styled-div>
        @endif

        <x-styled-div position="absolute" width="{{ $width - $layout['page1']['text-margin-x']*2 }}cm" left="{{ $layout['page1']['text-margin-x'] }}cm" top="{{ $layout['page1']['text-top'] }}cm" textAlign="center">
            @processFontsForPdf(getTextForPage($layout['page1'], $selectedLanguage))
        </x-styled-div>


        @if(!empty($layout['page1']['image-url']) && ($layout['page1']['image-height'] > 0 || $layout['page1']['image-width'] > 0))
        @php
        $imgHeight = ($layout['page1']['image-height'] > 0) ? $layout['page1']['image-height'].'cm' : 'auto';
        $imgWidth = ($layout['page1']['image-width'] > 0) ? $layout['page1']['image-width'].'cm' : 'auto';
        @endphp
        <x-styled-div position="absolute" width="{{ $width }}cm" top="{{ $layout['page1']['image-top'] }}cm" textAlign="center">
            <img src="{{ $layout['page1']['image-url'] }}" style="width: {{ $imgWidth }}; height: {{ $imgHeight }};" />
        </x-styled-div>
        @endif


    </x-page>

    <x-page-break></x-page-break>
    @endif


    @if(!$preview || (isset($previewPages) && in_array('page2', $previewPages)))
    <x-page preview="{{$preview}}" width="{{ $width }}" height="{{ $height }}" marginTop="{{ $layout['margin-out'] }}" marginLeft="{{ $layout['margin-in'] }}">

        @if(!empty($layout['page2']['background-url']))
        <x-styled-div backgroundImage="{{ $layout['page2']['background-url'] }}" backgroundSize="cover" backgroundPosition="center" width="{{ $width }}cm" height="{{ $height }}cm" top="0cm" left="0cm" position="absolute">
        </x-styled-div>
        @endif

        <x-styled-div position="absolute" width="{{ $width - $layout['page2']['text-margin-x']*2 }}cm" left="{{ $layout['page2']['text-margin-x'] }}cm" top="{{ $layout['page2']['text-top'] }}cm" textAlign="left">
            @processFontsForPdf(getTextForPage($layout['page2'], $selectedLanguage))
        </x-styled-div>

        @if(!empty($layout['page2']['image-url']) && ($layout['page2']['image-height'] > 0 || $layout['page2']['image-width'] > 0))
        @php
        $imgHeight = ($layout['page2']['image-height'] > 0) ? $layout['page2']['image-height'].'cm' : 'auto';
        $imgWidth = ($layout['page2']['image-width'] > 0) ? $layout['page2']['image-width'].'cm' : 'auto';
        @endphp
        <x-styled-div position="absolute" width="{{ $width }}cm" top="{{ $layout['page2']['image-top'] }}cm" textAlign="center">
            <img src="{{ $layout['page2']['image-url'] }}" style="width: {{ $imgWidth }}; height: {{ $imgHeight }};" />
        </x-styled-div>
        @endif

    </x-page>
    <x-page-break></x-page-break>
    @endif

    @if(!$preview || (isset($previewPages) && in_array('page3', $previewPages)))
    <x-page preview="{{$preview}}" width="{{ $width }}" height="{{ $height }}" marginTop="{{ $layout['margin-out'] }}" marginLeft="{{ $layout['margin-in'] }}">

        @if(!empty($layout['page3']['background-url']))
        <x-styled-div backgroundImage="{{ $layout['page3']['background-url'] }}" backgroundSize="cover" backgroundPosition="center" width="{{ $width }}cm" height="{{ $height }}cm" top="0cm" left="0cm" position="absolute">
        </x-styled-div>
        @endif

        <x-styled-div position="absolute" width="{{ $width - $layout['page3']['text-margin-x']*2 }}cm" left="{{ $layout['page3']['text-margin-x'] }}cm" top="{{ $layout['page3']['text-top'] }}cm" textAlign="left">
            @processFontsForPdf(getTextForPage($layout['page3'], $selectedLanguage))
        </x-styled-div>
        @if(!empty($layout['page3']['image-url']) && ($layout['page3']['image-height'] > 0 || $layout['page3']['image-width'] > 0))
        @php
        $imgHeight = ($layout['page3']['image-height'] > 0) ? $layout['page3']['image-height'].'cm' : 'auto';
        $imgWidth = ($layout['page3']['image-width'] > 0) ? $layout['page3']['image-width'].'cm' : 'auto';
        @endphp
        <x-styled-div position="absolute" width="{{ $width }}cm" top="{{ $layout['page3']['image-top'] }}cm" textAlign="center">
            <img src="{{ $layout['page3']['image-url'] }}" style="width: {{ $imgWidth }}; height: {{ $imgHeight }};" />
        </x-styled-div>
        @endif
        @if(!empty($layout['page3']['image-url']) && $layout['page3']['image-height'] > 0)
        <x-styled-div position="absolute" width="{{ $width }}cm" top="{{ $layout['page3']['image-top'] }}cm" textAlign="center">
            <img src="{{ $layout['page3']['image-url'] }}" style="width: {{ $layout['page3']['image-width'] }}; height: {{ $layout['page3']['image-height'] }}cm;" />
        </x-styled-div>
        @endif

    </x-page>
    <x-page-break></x-page-break>
    @endif


    @if(!$preview || (isset($previewPages) && in_array('page4', $previewPages)))
    <x-page preview="{{$preview}}" width="{{ $width }}" height="{{ $height }}" marginTop="{{ $layout['margin-out'] }}" marginLeft="{{ $layout['margin-in'] }}">
        @if(!empty($layout['page4']['background-url']))
        <x-styled-div backgroundImage="{{ $layout['page4']['background-url'] }}" backgroundSize="cover" backgroundPosition="center" width="{{ $width }}cm" height="{{ $height }}cm" top="0cm" left="0cm" position="absolute">
        </x-styled-div>
        @endif

        <x-styled-div position="absolute" width="{{ $width - $layout['page4']['text-margin-x']*2 }}cm" left="{{ $layout['page4']['text-margin-x'] }}cm" top="{{ $layout['page4']['text-top'] }}cm" textAlign="left">
            @processFontsForPdf(getTextForPage($layout['page4'], $selectedLanguage))
        </x-styled-div>

        @if(!empty($layout['page4']['image-url']) && ($layout['page4']['image-height'] > 0 || $layout['page4']['image-width'] > 0))
        @php
        $imgHeight = ($layout['page4']['image-height'] > 0) ? $layout['page4']['image-height'].'cm' : 'auto';
        $imgWidth = ($layout['page4']['image-width'] > 0) ? $layout['page4']['image-width'].'cm' : 'auto';
        @endphp
        <x-styled-div position="absolute" width="{{ $width }}cm" top="{{ $layout['page4']['image-top'] }}cm" textAlign="center">
            <img src="{{ $layout['page4']['image-url'] }}" style="width: {{ $imgWidth }}; height: {{ $imgHeight }};" />
        </x-styled-div>
        @endif

    </x-page>

    <x-page-break></x-page-break>
    @endif



    @if(!$preview || (isset($previewPages) && in_array('page5', $previewPages)))
    <x-page preview="{{$preview}}" width="{{ $width }}" height="{{ $height }}" marginTop="{{ $layout['margin-out'] }}" marginLeft="{{ $layout['margin-in'] }}">

        @if(!empty($layout['page5']['background-url']))
        <x-styled-div backgroundImage="{{ $layout['page5']['background-url'] }}" backgroundSize="cover" backgroundPosition="center" width="{{ $width }}cm" height="{{ $height }}cm" top="0cm" left="0cm" position="absolute">
        </x-styled-div>
        @endif

        <x-styled-div position="absolute" width="{{ $width - $layout['page5']['text-margin-x']*2 }}cm" left="{{ $layout['page5']['text-margin-x'] }}cm" top="{{ $layout['page5']['text-top'] }}cm" textAlign="left">
            @processFontsForPdf(getTextForPage($layout['page5'], $selectedLanguage))
        </x-styled-div>

        @if(!empty($layout['page5']['image-url']) && ($layout['page5']['image-height'] > 0 || $layout['page5']['image-width'] > 0))
        @php
        $imgHeight = ($layout['page5']['image-height'] > 0) ? $layout['page5']['image-height'].'cm' : 'auto';
        $imgWidth = ($layout['page5']['image-width'] > 0) ? $layout['page5']['image-width'].'cm' : 'auto';
        @endphp
        <x-styled-div position="absolute" width="{{ $width }}cm" top="{{ $layout['page5']['image-top'] }}cm" textAlign="center">
            <img src="{{ $layout['page5']['image-url'] }}" style="width: {{ $imgWidth }}; height: {{ $imgHeight }};" />
        </x-styled-div>
        @endif

    </x-page>

    <x-page-break></x-page-break>
    @endif

    @if(!$preview || (isset($previewPages) && in_array('content', $previewPages)))
    @foreach($items as $index => $item)

    <x-page preview="{{$preview}}" width="{{ $width }}" height="{{ $height }}" marginTop="{{ $layout['margin-out'] }}" marginLeft="{{ $layout['margin-in'] }}">

        <x-styled-div position="absolute" width="{{ $versoWhiteLayerWidth }}cm" height="{{ $versoWhiteLayerHeight }}cm" top="{{ $layout['verso']['margin'] }}cm" left="{{ $layout['verso']['margin'] }}cm" backgroundColor="white">
        </x-styled-div>

        <x-styled-div position="absolute" width="{{ $versoMainLayerWidth }}cm" height="{{ $versoMainLayerHeight }}cm" top="{{ $versoMainLayerMargin }}cm" left="{{ $versoMainLayerMargin }}cm" backgroundColor="black">
        </x-styled-div>

        <x-styled-div position="absolute" width="{{ $versoImageLayerWidth }}cm" height="{{ $versoImageLayerHeight }}cm" top="{{ $versoImageLayerMargin }}cm" left="{{ $versoImageLayerMargin }}cm" backgroundImage="{{ $item['image']['url'] ?? '' }}" backgroundSize="cover" backgroundPosition="center">
        </x-styled-div>

        <x-styled-div position="absolute" width="{{ $versoMainLayerWidth }}cm" height="{{ $versoMainLayerHeight }}cm" top="{{ $versoMainLayerMargin }}cm" left="{{ $versoMainLayerMargin }}cm" backgroundColor="black" opacity="0.8">
        </x-styled-div>


        <x-styled-div position="absolute" width="{{ $versoMainLayerWidth }}cm" left="{{ $versoMainLayerMargin }}cm" top="{{ $versoText1Top }}cm" lineHeight="{{ $layout['verso']['primary-font-size'] }}cm" fontSize="{{ $layout['verso']['primary-font-size'] }}cm" color="white" fontFamily="{{ $layout['verso']['text-1-font-family'] }}" textAlign="center">
            {!! $item['language'] !!}
        </x-styled-div>

        <x-styled-div position="absolute" width="{{ $versoMainLayerWidth }}cm" left="{{ $versoMainLayerMargin }}cm" top="{{ $versoText2Top }}cm" lineHeight="{{ $layout['verso']['secondary-font-size'] }}cm" fontSize="{{ $layout['verso']['secondary-font-size'] }}cm" color="white" fontFamily="{{ $layout['verso']['text-2-font-family'] }}" textAlign="center">
            {!! $item['sy2'] !!}
        </x-styled-div>


    </x-page>


    <x-page-break></x-page-break>
    <x-page preview="{{$preview}}" width="{{ $width }}" height="{{ $height }}" marginTop="{{ $layout['margin-out'] }}" marginLeft="{{ $layout['margin-in'] }}">

        <x-styled-div position="absolute" width="{{ $rectoBlackLayerWidth }}cm" height="{{ $rectoBlackLayerHeight }}cm" top="{{ $layout['recto']['margin'] }}cm" left="{{ $layout['recto']['margin'] }}cm" backgroundColor="black">
        </x-styled-div>

        <x-styled-div position="absolute" width="{{ $rectoImageLayerWidth }}cm" height="{{ $rectoImageLayerHeight }}cm" top="{{ $rectoImageLayerMargin }}cm" left="{{ $rectoImageLayerMargin }}cm" backgroundImage="{{ $item['image']['url'] ?? '' }}" backgroundSize="cover" backgroundPosition="center">
        </x-styled-div>

        <!-- Texto con efecto de sombra múltiple -->
        <x-styled-div position="absolute" width="{{ $rectoImageLayerWidth }}cm" left="{{ $rectoImageLayerMargin }}cm" top="{{ $rectoTextTop }}cm" fontSize="{{ $layout['recto']['font-size'] }}cm" lineHeight="{{ $layout['recto']['font-size'] }}cm" color="black" fontFamily="{{ $layout['recto']['text-font-family'] }}" textAlign="center" transform="translate(-0.1cm, -0.1cm)">
            {{ $item['sym'] ?? '' }}
        </x-styled-div>

        <x-styled-div position="absolute" width="{{ $rectoImageLayerWidth }}cm" left="{{ $rectoImageLayerMargin }}cm" top="{{ $rectoTextTop }}cm" fontSize="{{ $layout['recto']['font-size'] }}cm" lineHeight="{{ $layout['recto']['font-size'] }}cm" color="black" fontFamily="{{ $layout['recto']['text-font-family'] }}" textAlign="center" transform="scale(1.03, 1.03)">
            {{ $item['sym'] ?? '' }}
        </x-styled-div>

        <x-styled-div position="absolute" width="{{ $rectoImageLayerWidth }}cm" left="{{ $rectoImageLayerMargin }}cm" top="{{ $rectoTextTop }}cm" fontSize="{{ $layout['recto']['font-size'] }}cm" lineHeight="{{ $layout['recto']['font-size'] }}cm" color="black" fontFamily="{{ $layout['recto']['text-font-family'] }}" textAlign="center" transform="scale(0.97, 0.97)">
            {{ $item['sym'] ?? '' }}
        </x-styled-div>

        <x-styled-div position="absolute" width="{{ $rectoImageLayerWidth }}cm" left="{{ $rectoImageLayerMargin }}cm" top="{{ $rectoTextTop }}cm" fontSize="{{ $layout['recto']['font-size'] }}cm" lineHeight="{{ $layout['recto']['font-size'] }}cm" color="white" fontFamily="{{ $layout['recto']['text-font-family'] }}" textAlign="center">
            {{ $item['sym'] ?? '' }}
        </x-styled-div>

    </x-page>

    @endforeach
    @endif
</body>
</html>
