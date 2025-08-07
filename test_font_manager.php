<?php

require_once 'vendor/autoload.php';

// Simular la configuración de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\FontManager;

echo "Testando FontManager::generatePdfFontCSS()\n";
echo "==========================================\n\n";

// Test 1: Con archivo JSON válido
echo "Test 1: Con /json/chinese.json\n";
$css = FontManager::generatePdfFontCSS('/json/chinese.json');
echo "Longitud del CSS: " . strlen($css) . " caracteres\n";

// Contar cuántas fuentes aparecen en el CSS
$fontFaceCount = substr_count($css, '@font-face');
$qlFontCount = substr_count($css, '.ql-font-');
echo "Número de @font-face encontrados: $fontFaceCount\n";
echo "Número de .ql-font- encontrados: $qlFontCount\n";

// Extraer nombres de fuentes del CSS
preg_match_all('/\.ql-font-([a-zA-Z0-9]+)/', $css, $matches);
if (!empty($matches[1])) {
    echo "Fuentes en el CSS:\n";
    foreach (array_unique($matches[1]) as $fontName) {
        echo "- $fontName\n";
    }
} else {
    echo "No se encontraron fuentes en el CSS\n";
}

echo "\nPrimeros 800 caracteres del CSS:\n";
echo substr($css, 0, 800) . "\n\n";

// Test 2: Con archivo que no existe
echo "Test 2: Con archivo que no existe\n";
$css = FontManager::generatePdfFontCSS('/json/noexiste.json');
echo "Resultado: '" . $css . "' (longitud: " . strlen($css) . ")\n\n";

// Test 3: Leer el JSON directamente para comparar
echo "Test 3: Contenido del JSON chinese.json\n";
$jsonPath = public_path('json/chinese.json');
if (file_exists($jsonPath)) {
    $config = json_decode(file_get_contents($jsonPath), true);
    echo "Fuentes en el JSON: " . json_encode($config['fonts'] ?? []) . "\n";
} else {
    echo "JSON no encontrado\n";
}

echo "\nFin de las pruebas.\n";
