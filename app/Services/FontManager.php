<?php

namespace App\Services;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Log;

class FontManager
{
    /**
     * Obtiene todas las fuentes disponibles con información completa
     */
    public static function getAllFonts()
    {
        $fontsPath = public_path('fonts');
        $allFontFiles = [];
        $uniqueFonts = [];

        if (is_dir($fontsPath)) {
            $files = scandir($fontsPath);
            foreach ($files as $file) {
                // Solo procesar archivos TTF, excluir archivos cache (.ufm, .afm)
                if (pathinfo($file, PATHINFO_EXTENSION) === 'ttf') {
                    $fontName = pathinfo($file, PATHINFO_FILENAME);

                    // Saltar archivos temporales o cache de DomPDF
                    if (strpos($fontName, 'temp_') === 0 || strpos($fontName, 'cache_') === 0) {
                        continue;
                    }

                    $fontData = [
                        'filename' => $file,
                        'fontName' => $fontName,
                        'displayName' => self::cleanFontName($fontName),
                        'cssName' => self::generateCssFontName($fontName),
                        'familyName' => self::generateFontFamily($fontName),
                        'baseName' => self::extractBaseFontName($fontName),
                        'isVariation' => self::isVariationFont($fontName),
                        'path' => 'fonts/' . $file,
                        'absolutePath' => $fontsPath . DIRECTORY_SEPARATOR . $file
                    ];

                    $allFontFiles[] = $fontData;
                }
            }
        }

        // En lugar de filtrar por grupos, retornar todas las fuentes únicas
        $uniqueFonts = [];

        foreach ($allFontFiles as $font) {
            $uniqueFonts[] = [
                'filename' => $font['filename'],
                'displayName' => $font['displayName'],
                'cssName' => $font['cssName'],
                'familyName' => $font['familyName'],
                'path' => $font['path'],
                'absolutePath' => $font['absolutePath']
            ];
        }

        // Ordenar alfabéticamente por nombre de display
        usort($uniqueFonts, function ($a, $b) {
            return strcmp($a['displayName'], $b['displayName']);
        });

        return $uniqueFonts;
    }

    /**
     * Genera CSS @font-face para todas las fuentes (para uso en PDF)
     */
    public static function generateFontFaceCSS()
    {
        $fonts = self::getAllFonts();
        $css = '';

        foreach ($fonts as $font) {
            $css .= "@font-face {\n";
            $css .= "    font-family: '{$font['familyName']}';\n";
            $css .= "    src: url('" . public_path($font['path']) . "') format('truetype');\n";
            $css .= "}\n\n";
        }

        return $css;
    }

    /**
     * Genera CSS dinámico para Quill.js
     */
    public static function generateQuillFontCSS()
    {
        $fonts = self::getAllFonts();
        $css = '';

        foreach ($fonts as $font) {
            // CSS para Quill editor
            $css .= "@font-face {\n";
            $css .= "    font-family: '{$font['familyName']}';\n";
            $css .= "    src: url('/fonts/{$font['filename']}') format('truetype');\n";
            $css .= "}\n\n";

            // Clase Quill específica
            $css .= ".ql-font-{$font['cssName']} {\n";
            $css .= "    font-family: '{$font['familyName']}';\n";
            $css .= "}\n\n";
        }

        return $css;
    }

    /**
     * Genera CSS dinámico para Quill.js
     */
    public static function generatePdfFontCSS($configFile)
    {
        if (!$configFile) {
            return '';
        }

        // Obtener todas las fuentes disponibles en el directorio
        $fonts = self::getAllFonts();

        // Leer el archivo JSON de configuración
        // Si el configFile ya incluye 'json/', usarlo directamente, sino construir el path
        if (strpos($configFile, 'json/') === 0 || strpos($configFile, '/json/') === 0) {
            $configPath = public_path(ltrim($configFile, '/'));
        } else {
            $configPath = public_path('json/' . $configFile);
        }

        $allowedFonts = [];

        if (file_exists($configPath)) {
            $json = file_get_contents($configPath);
            $config = json_decode($json, true);

            if (isset($config['fonts']) && is_array($config['fonts'])) {
                $allowedFonts = $config['fonts']; // No convertir a minúsculas
            }
        } else {
            // Si no se encuentra el archivo JSON, no mostrar ninguna fuente
            Log::warning('FontManager::generatePdfFontCSS - Archivo JSON no encontrado', [
                'configFile' => $configFile,
                'configPath' => $configPath
            ]);
            return '';
        }

        // Si no hay fuentes definidas en el JSON, no mostrar ninguna fuente
        if (empty($allowedFonts)) {
            Log::info('FontManager::generatePdfFontCSS - No hay fuentes definidas en el JSON', [
                'configFile' => $configFile,
                'configPath' => $configPath
            ]);
            return '';
        }

        // Filtrar las fuentes: solo incluir las que están en el JSON
        $fonts = array_filter($fonts, function ($font) use ($allowedFonts) {
            return in_array($font['cssName'], $allowedFonts); // Comparación exacta por cssName
        });

        // Debug: Verificar qué fuentes se están filtrando
        Log::info('FontManager::generatePdfFontCSS Debug', [
            'configFile' => $configFile,
            'configPath' => $configPath,
            'configExists' => file_exists($configPath),
            'allowedFonts' => $allowedFonts,
            'totalFontsAvailable' => count(self::getAllFonts()),
            'filteredFontsCount' => count($fonts),
            'filteredFontNames' => array_column($fonts, 'cssName')
        ]);

        $css = '';

        foreach ($fonts as $font) {
            // CSS para @font-face
            $css .= "@font-face {\n";
            $css .= "    font-family: '{$font['familyName']}';\n";
            $css .= "    src: url('/fonts/{$font['filename']}') format('truetype');\n";
            $css .= "}\n\n";

            // Clase Quill específica
            $css .= ".ql-font-{$font['cssName']} {\n";
            $css .= "    font-family: '{$font['familyName']}';\n";
            $css .= "}\n\n";
        }

        return $css;
    }

    /**
     * Obtiene la lista de fuentes para el selector de Quill
     */
    public static function getQuillFontOptions()
    {
        $fonts = self::getAllFonts();
        $options = [];

        foreach ($fonts as $font) {
            $options[$font['cssName']] = $font['displayName'];
        }

        return $options;
    }

    /**
     * Obtiene fuente por nombre CSS
     */
    public static function getFontByCSS($cssName)
    {
        $fonts = self::getAllFonts();

        foreach ($fonts as $font) {
            if ($font['cssName'] === $cssName) {
                return $font;
            }
        }

        return null;
    }

    private static function cleanFontName($fontName)
    {
        // Remover sufijos hash y caracteres extraños
        $name = preg_replace('/_[a-f0-9]{32}$/', '', $fontName);

        // Convertir guiones y underscores a espacios
        $name = str_replace(['_', '-'], ' ', $name);

        // Capitalizar palabras
        $name = ucwords(strtolower($name));

        // Limpiar nombres específicos conocidos
        $replacements = [
            'Notoserif' => 'Noto Serif',
            'Notoserifbold' => 'Noto Serif Bold',
            'Notoserifitalic' => 'Noto Serif Italic',
            'Ebgaramond' => 'EB Garamond',
            'Hanwangoutlinekanten' => 'HanWang Outline',
            'Zenantique' => 'Zen Antique',
            'Dejavu' => 'DejaVu',
            'Garamonditalic' => 'Garamond Italic'
        ];

        foreach ($replacements as $search => $replace) {
            $name = str_ireplace($search, $replace, $name);
        }

        return $name;
    }

    private static function generateCssFontName($fontName)
    {
        // Generar un nombre CSS válido
        $cssName = preg_replace('/_[a-f0-9]{32}$/', '', $fontName);
        $cssName = preg_replace('/[^a-zA-Z0-9]/', '', $cssName);
        return $cssName;
    }

    private static function generateFontFamily($fontName)
    {
        // Generar nombre de familia de fuente limpio
        $familyName = preg_replace('/_[a-f0-9]{32}$/', '', $fontName);
        $familyName = str_replace(['_', '-'], '', $familyName);
        return $familyName;
    }

    /**
     * Extrae el nombre base de la fuente sin variaciones de estilo
     */
    private static function extractBaseFontName($fontName)
    {
        // Remover hash MD5/SHA si existe
        $baseName = preg_replace('/_[a-f0-9]{32}$/', '', $fontName);

        // Remover sufijos comunes de variación (case insensitive)
        $variations = [
            'bold',
            'italic',
            'regular',
            'normal',
            'light',
            'medium',
            'heavy',
            'black',
            'thin',
            'extralight',
            'semibold',
            'extrabold',
            'condensed',
            'extended',
            'narrow',
            'wide',
            'oblique',
            'slanted'
        ];

        foreach ($variations as $variation) {
            $baseName = preg_replace('/[-_]?' . $variation . '$/i', '', $baseName);
            $baseName = preg_replace('/^' . $variation . '[-_]?/i', '', $baseName);
        }

        // Limpiar caracteres especiales al final
        $baseName = rtrim($baseName, '-_');

        return strtolower($baseName);
    }

    /**
     * Determina si una fuente es una variación (bold, italic, etc.)
     */
    private static function isVariationFont($fontName)
    {
        $variations = [
            'bold',
            'italic',
            'light',
            'medium',
            'heavy',
            'black',
            'thin',
            'extralight',
            'semibold',
            'extrabold',
            'condensed',
            'extended',
            'narrow',
            'wide',
            'oblique',
            'slanted'
        ];

        $fontNameLower = strtolower($fontName);

        foreach ($variations as $variation) {
            if (strpos($fontNameLower, $variation) !== false) {
                return true;
            }
        }

        return false;
    }
}
