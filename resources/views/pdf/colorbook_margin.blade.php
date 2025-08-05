@php
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


$firstPageTitleLine1Top=$height* $layout['page1']['title-line-1-y-percentage'] - $layout['page1']['title-line-1-font-size'];
$firstPageTitleLine2Top=$firstPageTitleLine1Top + $layout['page1']['title-line-1-font-size'] + $layout['page1']['title-line-2-margin'];
$firstPageTitleLine3Top=$firstPageTitleLine2Top + $layout['page1']['title-line-2-font-size'] + $layout['page1']['title-line-3-margin'];
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
                font-family: 'ZenAntiqueRegular';
                src: url('{{ public_path('fonts/ZenAntique-Regular.ttf') }}') format('truetype');
            }

            @font-face {
                font-family: 'NotoSerif';
                src: url('{{ public_path('fonts/NotoSerif-Regular.ttf') }}') format('truetype');
            }

            @font-face {
                font-family: 'NotoSerifBold';
                src: url('{{ public_path('fonts/NotoSerifSC-Bold.ttf') }}') format('truetype');
            }

            @font-face {
                font-family: 'NotoSerifItalic';
                src: url('{{ public_path('fonts/NotoSerif-Italic.ttf') }}') format('truetype');
            }

        </style>


        <style>
            p {
                margin: 0;
                padding: 0;
            }


        </style>
    </head>
    <body style="margin: 0cm 0cm;height:100%;">
        <x-styled-div fontFamily="'NotoSerif'" width="{{ $width }}cm" height="{{ $height }}cm" marginTop="{{ $layout['margin-out'] }}cm" marginLeft="{{ $layout['margin-in'] }}cm"  overflow="hidden" position="relative">

            <x-styled-div fontSize="{{ $layout['page1']['title-line-1-font-size'] }}cm" lineHeight="{{ $layout['page1']['title-line-1-font-size'] }}cm" position="absolute" width="{{ $width }}cm" top="{{ $firstPageTitleLine1Top }}cm" textAlign="center">
                {!! htmlspecialchars_decode($layout['page1']['title-line-1'] ?? '', ENT_QUOTES) !!}
            </x-styled-div>

            <x-styled-div fontSize="{{ $layout['page1']['title-line-2-font-size'] }}cm" lineHeight="{{ $layout['page1']['title-line-2-font-size'] }}cm" position="absolute" width="{{ $width }}cm" top="{{ $firstPageTitleLine2Top }}cm" textAlign="center">
                {!! htmlspecialchars_decode($layout['page1']['title-line-2'] , ENT_QUOTES) !!}
            </x-styled-div>

            <x-styled-div fontSize="{{ $layout['page1']['title-line-3-font-size'] }}cm" lineHeight="{{ $layout['page1']['title-line-3-font-size'] }}cm" position="absolute" width="{{ $width }}cm" top="{{ $firstPageTitleLine3Top }}cm" textAlign="center">
                {!! htmlspecialchars_decode($layout['page1']['title-line-3'] , ENT_QUOTES) !!}
            </x-styled-div>

            <x-styled-div position="absolute" width="{{ $width }}cm" top="{{ $height * $layout['page1']['logo-y-percentage'] }}cm" textAlign="center">
                <img src="{{ $layout['page1']['logo-image'] }}" style="width: auto; height: {{ $layout['page1']['logo-height'] }}cm;" />
            </x-styled-div>
        </x-styled-div>
        <div style="page-break-after: always;"></div>
        <x-styled-div fontFamily="'NotoSerif'" fontSize="{{ $layout['page2']['text-font-size'] }}cm" lineHeight="{{ $layout['page2']['text-font-size'] }}cm" width="{{ $width }}cm" height="{{ $height }}cm" marginTop="{{ $layout['margin-out'] }}cm" marginLeft="{{ $layout['margin-in'] }}cm"  overflow="hidden" position="relative">

            <x-styled-div position="absolute" width="{{ $width - $layout['page2']['margin-x']*2 }}cm" left="{{ $layout['page2']['margin-x'] }}cm" top="{{ $height - $layout['page2']['margin-bottom'] - $layout['margin-out'] }}cm" transform="translateY(-100%)">

                <x-styled-div margin="0 0 {{ $layout['page2']['text-y-space'] }}cm 0">
                    {!! $layout['page2']['text-block-1'] !!}
                </x-styled-div>

                <x-styled-div margin="0 0 {{ $layout['page2']['text-y-space'] }}cm 0">
                    {!! $layout['page2']['text-block-2'] !!}
                </x-styled-div>

                <x-styled-div margin="0 0 {{ $layout['page2']['text-y-space'] }}cm 0">
                    {!! $layout['page2']['text-block-3'] !!}
                </x-styled-div>

                <x-styled-div>
                    {!! $layout['page2']['text-block-4'] !!}
                </x-styled-div>
            </x-styled-div>
        </x-styled-div>
        <div style="page-break-after: always;"></div>
        <x-styled-div fontFamily="'NotoSerif'" width="{{ $width }}cm" height="{{ $height }}cm" marginTop="{{ $layout['margin-out'] }}cm" marginLeft="{{ $layout['margin-in'] }}cm"  overflow="hidden" position="relative">

            <x-styled-div position="absolute" width="{{ $width }}cm" top="{{ $height * $layout['page3']['image-y-percentage'] }}cm" textAlign="center">
                <img src="{{ $layout['page3']['image'] ?? '' }}" style="width: {{ $width - $layout['page3']['margin']*2 }}cm; height:auto;" />
            </x-styled-div>

        </x-styled-div>
        <div style="page-break-after: always;"></div>
        <x-styled-div backgroundImage="{{ $layout['page4']['image'] }}" backgroundSize="cover" backgroundPosition="center" width="{{ $width }}cm" height="{{ $height }}cm" marginTop="{{ $layout['margin-out'] }}cm" marginLeft="{{ $layout['margin-in'] }}cm"  overflow="hidden" position="relative">
        </x-styled-div>

        <div style="page-break-after: always;"></div>
        <x-styled-div fontFamily="'NotoSerif'" backgroundImage="{{ $layout['page5']['image'] }}" backgroundSize="cover" backgroundPosition="center" width="{{ $width }}cm" height="{{ $height }}cm" marginTop="{{ $layout['margin-out'] }}cm" marginLeft="{{ $layout['margin-in'] }}cm"  overflow="hidden" position="relative">

            <x-styled-div fontSize="{{ $layout['page5']['header-font-size'] }}cm" lineHeight="{{ $layout['page5']['header-font-size'] }}cm" fontFamily="'NotoSerifBold'" textAlign="center" position="absolute" width="{{ $width - $layout['page5']['margin']*2 }}cm" height="{{ $height }}cm" top="{{ $layout['page5']['header-y'] }}cm" left="{{ $layout['page5']['margin'] }}cm">
                {!! $layout['page5']['header-text'] !!}
            </x-styled-div>

            <x-styled-div fontSize="{{ $layout['page5']['text-font-size'] }}cm" lineHeight="{{ $layout['page5']['text-font-size'] }}cm" textAlign="center" position="absolute" width="{{ $width - $layout['page5']['margin']*2 }}cm" height="{{ $height }}cm" top="{{ $layout['page5']['text-1-y'] }}cm" left="{{ $layout['page5']['margin'] }}cm">
                {!! $layout['page5']['text-1-content'] !!}
            </x-styled-div>

            <x-styled-div fontSize="{{ $layout['page5']['text-font-size'] }}cm" lineHeight="{{ $layout['page5']['text-font-size'] }}cm" textAlign="center" position="absolute" width="{{ $width - $layout['page5']['margin']*2 }}cm" height="{{ $height }}cm" top="{{ $layout['page5']['text-2-y'] }}cm" left="{{ $layout['page5']['margin'] }}cm">
                {!! $layout['page5']['text-2-content'] !!}
            </x-styled-div>

            <x-styled-div fontSize="{{ $layout['page5']['text-font-size'] }}cm" lineHeight="{{ $layout['page5']['text-font-size'] }}cm" textAlign="center" position="absolute" width="{{ $width - $layout['page5']['margin']*2 }}cm" height="{{ $height }}cm" top="{{ $layout['page5']['text-3-y'] }}cm" left="{{ $layout['page5']['margin'] }}cm">
                {!! $layout['page5']['text-3-content'] !!}
            </x-styled-div>

            <x-styled-div fontSize="{{ $layout['page5']['footer-font-size'] }}cm" lineHeight="{{ $layout['page5']['footer-font-size'] }}cm" fontFamily="'NotoSerifBold'" textAlign="center" position="absolute" width="{{ $width - $layout['page5']['margin']*2 }}cm" height="{{ $height }}cm" top="{{ $layout['page5']['footer-y'] }}cm" left="{{ $layout['page5']['margin'] }}cm">
                {!! $layout['page5']['footer-text'] !!}
            </x-styled-div>
        </x-styled-div>

        <div style="page-break-after: always;"></div>

        @foreach($items as $index => $item)

        <x-styled-div width="{{ $width }}cm" height="{{ $height }}cm" marginTop="{{ $layout['margin-out'] }}cm" marginLeft="{{ $layout['margin-in'] }}cm" backgroundColor="black"  overflow="hidden" position="relative">

            <x-styled-div position="absolute" width="{{ $versoWhiteLayerWidth }}cm" height="{{ $versoWhiteLayerHeight }}cm" top="{{ $layout['verso']['margin'] }}cm" left="{{ $layout['verso']['margin'] }}cm" backgroundColor="white">
            </x-styled-div>

            <x-styled-div position="absolute" width="{{ $versoMainLayerWidth }}cm" height="{{ $versoMainLayerHeight }}cm" top="{{ $versoMainLayerMargin }}cm" left="{{ $versoMainLayerMargin }}cm" backgroundColor="black">
            </x-styled-div>

            <x-styled-div position="absolute" width="{{ $versoImageLayerWidth }}cm" height="{{ $versoImageLayerHeight }}cm" top="{{ $versoImageLayerMargin }}cm" left="{{ $versoImageLayerMargin }}cm" backgroundImage="{{ $item['image']['url'] ?? '' }}" backgroundSize="cover" backgroundPosition="center">
            </x-styled-div>

            <x-styled-div position="absolute" width="{{ $versoMainLayerWidth }}cm" height="{{ $versoMainLayerHeight }}cm" top="{{ $versoMainLayerMargin }}cm" left="{{ $versoMainLayerMargin }}cm" backgroundColor="black" opacity="0.8">
            </x-styled-div>


            <x-styled-div position="absolute" width="{{ $versoMainLayerWidth }}cm" left="{{ $versoMainLayerMargin }}cm" top="{{ $versoText1Top }}cm" lineHeight="{{ $layout['verso']['primary-font-size'] }}cm" fontSize="{{ $layout['verso']['primary-font-size'] }}cm" color="white" fontFamily="NotoSerif" textAlign="center">
                {!! $item['es'] !!}
            </x-styled-div>

            <x-styled-div position="absolute" width="{{ $versoMainLayerWidth }}cm" left="{{ $versoMainLayerMargin }}cm" top="{{ $versoText2Top }}cm" lineHeight="{{ $layout['verso']['secondary-font-size'] }}cm" fontSize="{{ $layout['verso']['secondary-font-size'] }}cm" color="white" fontFamily="NotoSerifItalic" textAlign="center">
                {!! $item['sy2'] !!}
            </x-styled-div>


        </x-styled-div>

        <div style="page-break-after: always;"></div>
        <x-styled-div width="{{ $width }}cm" height="{{ $height }}cm" marginTop="{{ $layout['margin-out'] }}cm" marginLeft="{{ $layout['margin-in'] }}cm"  overflow="hidden" position="relative">

            <x-styled-div position="absolute" width="{{ $rectoBlackLayerWidth }}cm" height="{{ $rectoBlackLayerHeight }}cm" top="{{ $layout['recto']['margin'] }}cm" left="{{ $layout['recto']['margin'] }}cm" backgroundColor="black">
            </x-styled-div>

            <x-styled-div position="absolute" width="{{ $rectoImageLayerWidth }}cm" height="{{ $rectoImageLayerHeight }}cm" top="{{ $rectoImageLayerMargin }}cm" left="{{ $rectoImageLayerMargin }}cm" backgroundImage="{{ $item['image']['url'] ?? '' }}" backgroundSize="cover" backgroundPosition="center">
            </x-styled-div>

            <!-- Texto con efecto de sombra múltiple -->
            <x-styled-div position="absolute" width="{{ $rectoImageLayerWidth }}cm" left="{{ $rectoImageLayerMargin }}cm" top="{{ $rectoTextTop }}cm" fontSize="{{ $layout['recto']['font-size'] }}cm" lineHeight="{{ $layout['recto']['font-size'] }}cm" color="black" fontFamily="ZenAntiqueRegular" textAlign="center" transform="translate(-0.1cm, -0.1cm)">
                {{ $item['sym'] ?? '' }}
            </x-styled-div>

            <x-styled-div position="absolute" width="{{ $rectoImageLayerWidth }}cm" left="{{ $rectoImageLayerMargin }}cm" top="{{ $rectoTextTop }}cm" fontSize="{{ $layout['recto']['font-size'] }}cm" lineHeight="{{ $layout['recto']['font-size'] }}cm" color="black" fontFamily="ZenAntiqueRegular" textAlign="center" transform="scale(1.03, 1.03)">
                {{ $item['sym'] ?? '' }}
            </x-styled-div>

            <x-styled-div position="absolute" width="{{ $rectoImageLayerWidth }}cm" left="{{ $rectoImageLayerMargin }}cm" top="{{ $rectoTextTop }}cm" fontSize="{{ $layout['recto']['font-size'] }}cm" lineHeight="{{ $layout['recto']['font-size'] }}cm" color="black" fontFamily="ZenAntiqueRegular" textAlign="center" transform="scale(0.97, 0.97)">
                {{ $item['sym'] ?? '' }}
            </x-styled-div>

            <x-styled-div position="absolute" width="{{ $rectoImageLayerWidth }}cm" left="{{ $rectoImageLayerMargin }}cm" top="{{ $rectoTextTop }}cm" fontSize="{{ $layout['recto']['font-size'] }}cm" lineHeight="{{ $layout['recto']['font-size'] }}cm" color="white" fontFamily="ZenAntiqueRegular" textAlign="center">
                {{ $item['sym'] ?? '' }}
            </x-styled-div>
        </x-styled-div>

        @endforeach
    </body>
    </html>
