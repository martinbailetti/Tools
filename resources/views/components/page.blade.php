@php


$previewBorder = 0.05;
// Función para construir el estilo CSS
$styleAttributes = [];

// Agregar propiedades de estilo si están definidas
if (isset($width)) $styleAttributes[] = "width: {$width}cm";
if (isset($height)) $styleAttributes[] = "height: {$height}cm";
if (isset($marginTop)) $styleAttributes[] = "margin-top: {$marginTop}cm";
if (isset($marginLeft)) $styleAttributes[] = "margin-left: {$marginLeft}cm";

$styleAttributes[] = "position: relative";
$styleAttributes[] = "overflow: hidden";

// Combinar todos los estilos
$styleString = implode('; ', $styleAttributes);
@endphp

<div style="{{ $styleString }}" {{ $attributes }}>
    {{ $slot }}

    @if($preview)
    <x-styled-div border="{{$previewBorder}}cm dashed black" position="absolute" width="{{ $width-$previewBorder*2 }}cm" height="{{ $height-$previewBorder*2 }}cm" top="0cm" left="0cm">
    </x-styled-div>
    @endif
</div>
