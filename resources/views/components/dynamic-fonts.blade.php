@php
    use App\Services\FontManager;
    $fontCSS = FontManager::generateFontFaceCSS();
@endphp

<style>
    {!! $fontCSS !!}
</style>
