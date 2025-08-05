<?php

namespace App\Services;

class FontMappingService
{
    /**
     * Mapea las fuentes hardcodeadas a las fuentes dinámicas
     */
    private static $fontMappings = [
        "'Zen'" => 'ZenAntiqueRegular',
        "'NotoSerif'" => 'NotoSerifRegular',
        "'NotoSerifBold'" => 'NotoSerifSCBold',
        "'NotoSerifItalic'" => 'NotoSerifItalic'
    ];

    /**
     * Convierte una referencia de fuente hardcodeada a nombre de familia dinámico
     */
    public static function mapHardcodedToFamily($hardcodedFont)
    {
        if (isset(self::$fontMappings[$hardcodedFont])) {
            $cssName = self::$fontMappings[$hardcodedFont];
            $font = FontManager::getFontByCSS($cssName);
            return $font ? "'{$font['familyName']}'" : $hardcodedFont;
        }

        return $hardcodedFont;
    }

    /**
     * Obtiene el mapeo de todas las fuentes para JavaScript
     */
    public static function getAllMappings()
    {
        $mappings = [];
        foreach (self::$fontMappings as $hardcoded => $cssName) {
            $font = FontManager::getFontByCSS($cssName);
            if ($font) {
                $mappings[$hardcoded] = "'{$font['familyName']}'";
            }
        }
        return $mappings;
    }

    /**
     * Convierte contenido HTML de Quill a formato compatible con PDF
     */
    public static function convertQuillToPDF($html)
    {
        // Convertir clases ql-font- a font-family inline
        $fonts = FontManager::getAllFonts();

        foreach ($fonts as $font) {
            $quillClass = "ql-font-{$font['cssName']}";
            $familyName = $font['familyName'];

            // Reemplazar clase por estilo inline
            $html = preg_replace(
                '/class="([^"]*)'.$quillClass.'([^"]*)"/',
                'class="$1$2" style="font-family: \''.$familyName.'\'"',
                $html
            );
        }

        return $html;
    }
}
