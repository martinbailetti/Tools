<?php

require_once 'vendor/autoload.php';

// Simular la configuración de Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\FontHelper;

echo "Testando FontHelper::addFontFamilyStylesForPdf()\n";
echo "===============================================\n\n";

// Test 1: HTML con clase ql-font- sin estilo
$html1 = '<span class="ql-font-NotoSerifRegular">Este libro te invita a explorar no solo la estética visual de las palabras como "belleza" (</span>';
echo "Test 1: HTML con clase ql-font- sin estilo\n";
echo "Original: $html1\n";
$processed1 = FontHelper::addFontFamilyStylesForPdf($html1);
echo "Procesado: $processed1\n\n";

// Test 2: HTML con clase ql-font- y estilo existente
$html2 = '<span class="ql-font-ZenAntiqueRegular" style="color: red;">美</span>';
echo "Test 2: HTML con clase ql-font- y estilo existente\n";
echo "Original: $html2\n";
$processed2 = FontHelper::addFontFamilyStylesForPdf($html2);
echo "Procesado: $processed2\n\n";

// Test 3: HTML con múltiples elementos
$html3 = '<p><span class="ql-font-NotoSerifRegular">Los caracteres chinos, conocidos como Hànzì, son mucho más que meras letras; son ideogramas que, en su origen eran representaciones pictóricas de objetos, conceptos y sentimientos.</span></p>';
echo "Test 3: HTML con múltiples elementos\n";
echo "Original: $html3\n";
$processed3 = FontHelper::addFontFamilyStylesForPdf($html3);
echo "Procesado: $processed3\n\n";

// Test 4: HTML mezclado como en el JSON
$html4 = '<p><span class="ql-font-NotoSerifRegular">Este libro te invita a explorar no solo la estética visual de las palabras como "belleza" (</span><span class="ql-font-ZenAntiqueRegular">美</span><span class="ql-font-NotoSerifRegular">, měi) o "espíritu" (</span><span class="ql-font-ZenAntiqueRegular">心</span><span class="ql-font-NotoSerifRegular">, xīn), sino también el significado profundo que subyace en cada una.</span></p>';
echo "Test 4: HTML mezclado como en el JSON\n";
echo "Original: $html4\n";
$processed4 = FontHelper::addFontFamilyStylesForPdf($html4);
echo "Procesado: $processed4\n\n";

// Test 5: HTML sin clases ql-font-
$html5 = '<p>Texto normal sin fuentes especiales</p>';
echo "Test 5: HTML sin clases ql-font-\n";
echo "Original: $html5\n";
$processed5 = FontHelper::addFontFamilyStylesForPdf($html5);
echo "Procesado: $processed5\n\n";

echo "Fin de las pruebas.\n";
