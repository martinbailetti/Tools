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

    <div style="width:21cm;height:29.7cm;background-color:black;margin:0cm 0cm;overflow:hidden;position:relative; ">
        <div style="position:absolute;width:20cm;height:28.7cm;top:0.5cm;left:0.5cm;background-color:white;"> </div>

        <div style="position:absolute; width:19.5cm;height:28.2cm;top:0.75cm;left:0.75cm; background-color: black;">
        </div>
        <div style="position:absolute; width:19cm;height:27.7cm;top:1cm;left:1cm; background-image: url('{{ $item['image']['url'] }}'); background-size: cover; background-position: center;">
        </div>

        <div style="position:absolute; width:19.5cm;height:28.2cm;top:0.75cm;left:0.75cm; background-color: black; opacity:0.8;">
        </div>
        <div style="position:absolute; width:19.5cm;left:0.75cm;top:15cm;font-size:2.5cm; color:white; font-family: 'NotoSerif'; text-align:center;">
            {!! htmlspecialchars_decode($item['es'], ENT_QUOTES) !!}
        </div>
        <div style="position:absolute; width:19.5cm;left:0.75cm;top:18cm;font-size:2cm; color:white; font-family: 'NotoSerifItalic'; text-align:center;">
            {!! htmlspecialchars_decode($item['sy2'], ENT_QUOTES) !!}
        </div>
    </div>

    <div style="width:21cm;height:29.7cm;margin:0cm 0cm;overflow:hidden;position:relative; ">
        <div style="position:absolute;width:20cm;height:28.7cm;top:0.5cm;left:0.5cm;background-color:black;"> </div>
        <div style="position:absolute; width:19.5cm;height:28.2cm;top:0.757cm;left:0.75cm; background-image: url('{{ $item['image']['url'] }}'); background-size: cover; background-position: center;">
        </div>
        <div style="position:absolute; width:19.5cm;left:0.75cm;top:5.9cm;font-size:8cm; color:black; font-family: 'Zen'; text-align:center;transform: translate(-0.1cm, -0.1cm);">
            {{ $item['sym'] }}
        </div>


        <div style="position:absolute; width:19.5cm;left:0.75cm;top:5.9cm;font-size:8cm; color:black; font-family: 'Zen'; text-align:center;transform: scale(1.03, 1.03);">
            {{ $item['sym'] }}
        </div>
        <div style="position:absolute; width:19.5cm;left:0.75cm;top:5.9cm;font-size:8cm; color:black; font-family: 'Zen'; text-align:center;transform: scale(0.97, 0.97);">
            {{ $item['sym'] }}
        </div>
        <div style="position:absolute; width:19.5cm;left:0.75cm;top:5.9cm;font-size:8cm; color:white; font-family: 'Zen'; text-align:center;">
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
