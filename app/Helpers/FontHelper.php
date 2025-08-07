<?php

namespace App\Helpers;

use App\Services\FontManager;

class FontHelper
{
    /**
     * Procesa HTML para añadir estilos font-family a elementos con clases ql-font-
     *
     * @param string $html El HTML a procesar
     * @return string El HTML procesado con estilos font-family añadidos
     */
    public static function addFontFamilyStyles($html)
    {
        if (empty($html)) {
            return $html;
        }

        // Obtener todas las fuentes disponibles para mapear cssName a familyName
        $fonts = FontManager::getAllFonts();
        $fontMap = [];

        foreach ($fonts as $font) {
            $fontMap[$font['cssName']] = $font['familyName'];
        }

        // Patrón para encontrar elementos con clases ql-font-
        $pattern = '/(<[^>]*class="[^"]*ql-font-([^"\s]+)[^"]*"[^>]*)(>)/i';

        return preg_replace_callback($pattern, function ($matches) use ($fontMap) {
            $elementTag = $matches[1]; // Toda la etiqueta hasta antes del >
            $fontCssName = $matches[2]; // El nombre de la fuente después de ql-font-
            $closingBracket = $matches[3]; // El >

            // Verificar si la fuente existe en nuestro mapa
            if (!isset($fontMap[$fontCssName])) {
                // Si no existe la fuente, devolver sin modificar
                return $matches[0];
            }

            $fontFamilyName = $fontMap[$fontCssName];

            // Verificar si ya tiene un atributo style
            if (preg_match('/style\s*=\s*["\']/', $elementTag)) {
                // Ya tiene style, verificar si ya tiene font-family
                if (preg_match('/font-family\s*:\s*[^;"\']/', $elementTag)) {
                    // Ya tiene font-family, no modificar
                    return $matches[0];
                } else {
                    // Tiene style pero no font-family, añadirlo
                    $elementTag = preg_replace(
                        '/style\s*=\s*(["\'])([^"\']*)\1/',
                        'style="$2; font-family: \'' . $fontFamilyName . '\'"',
                        $elementTag
                    );
                }
            } else {
                // No tiene style, añadir el atributo completo
                $elementTag .= ' style="font-family: \'' . $fontFamilyName . '\'"';
            }

            return $elementTag . $closingBracket;
        }, $html);
    }

    /**
     * Procesa HTML específicamente para PDFs usando DomPDF
     * Similar a addFontFamilyStyles pero optimizado para DomPDF
     *
     * @param string $html El HTML a procesar
     * @return string El HTML procesado
     */
    public static function addFontFamilyStylesForPdf($html)
    {
        if (empty($html)) {
            return $html;
        }

        // Para PDFs, usamos los nombres de fuente directamente como están en los archivos TTF
        $fonts = FontManager::getAllFonts();
        $fontMap = [];

        foreach ($fonts as $font) {
            $fontMap[$font['cssName']] = $font['familyName'];
        }

        // Patrón más específico para DomPDF
        $pattern = '/(<[^>]*class="[^"]*ql-font-([^"\s]+)[^"]*"[^>]*)(>)/i';

        return preg_replace_callback($pattern, function ($matches) use ($fontMap) {
            $elementTag = $matches[1];
            $fontCssName = $matches[2];
            $closingBracket = $matches[3];

            if (!isset($fontMap[$fontCssName])) {
                return $matches[0];
            }

            $fontFamilyName = $fontMap[$fontCssName];

            // Para DomPDF, siempre sobrescribir font-family si existe ql-font-
            if (preg_match('/style\s*=\s*["\']/', $elementTag)) {
                // Remover cualquier font-family existente y añadir el nuevo
                $elementTag = preg_replace('/font-family\s*:\s*[^;"\']+(;|\s*["\'])/i', '', $elementTag);
                $elementTag = preg_replace(
                    '/style\s*=\s*(["\'])([^"\']*)\1/',
                    'style="$2; font-family: \'' . $fontFamilyName . '\'"',
                    $elementTag
                );
                // Limpiar dobles puntos y comas y espacios extra
                $elementTag = preg_replace('/;\s*;/', ';', $elementTag);
                $elementTag = preg_replace('/style=";\s*/', 'style="', $elementTag);
                $elementTag = preg_replace('/;\s*"/', '"', $elementTag);
            } else {
                $elementTag .= ' style="font-family: \'' . $fontFamilyName . '\'"';
            }

            return $elementTag . $closingBracket;
        }, $html);
    }
}
