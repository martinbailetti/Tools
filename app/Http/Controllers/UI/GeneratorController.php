<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Services\FontManager;
use Barryvdh\DomPDF\Facade\Pdf;
use FontLib\Font;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class GeneratorController extends Controller
{
    public function index()
    {
        // Obtener lista de archivos JSON de la carpeta public/json
        $jsonPath = public_path('json');
        $jsonFiles = [];

        if (is_dir($jsonPath)) {
            $files = scandir($jsonPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                    $jsonFiles[] = [
                        'filename' => $file,
                        'name' => pathinfo($file, PATHINFO_FILENAME), // nombre sin extensión
                        'path' => '/json/' . $file
                    ];
                }
            }
        }

        return view('index', compact('jsonFiles'));
    }

    /**
     * Obtiene las fuentes disponibles de un archivo JSON específico
     */
    public function getFontsFromJson(Request $request)
    {
        try {
            $jsonFile = $request->input('jsonFile');
            if (!$jsonFile) {
                return response()->json(['error' => 'No se especificó archivo JSON'], 400);
            }

            // Leer el archivo JSON
            $jsonPath = public_path($jsonFile);
            if (!file_exists($jsonPath)) {
                return response()->json(['error' => 'Archivo JSON no encontrado'], 404);
            }

            $config = json_decode(file_get_contents($jsonPath), true);
            if (!$config) {
                return response()->json(['error' => 'Error al leer archivo JSON'], 500);
            }

            // Extraer fuentes
            $fonts = $this->extractFontsFromConfig($config);

            return response()->json(['fonts' => $fonts]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }



    public function saveConfig(Request $request)
    {
        try {
            // Obtener el idioma seleccionado (será usado para guardar textos específicos)
            $selectedLanguage = strtolower($request->input('language_selector', 'es'));

            // Obtener el nombre del archivo JSON desde el request, usar 'chinese.json' por defecto
            $selectedJsonFile = $request->input('selected_json_file', '/json/chinese.json');
            $filename = basename($selectedJsonFile);

            // Ruta del archivo JSON
            $jsonPath = public_path('json/' . $filename);

            // Leer configuración existente para preservar textos de otros idiomas
            $existingConfig = [];
            if (file_exists($jsonPath)) {
                $existingContent = file_get_contents($jsonPath);
                $existingConfig = json_decode($existingContent, true) ?: [];
            }

            // Construir el array de configuración desde el request
            $config = [
                'width' => (float) $request->input('width'),
                'height' => (float) $request->input('height'),
                'margin-in' => (float) $request->input('margin_in'),
                'margin-out' => (float) $request->input('margin_out'),
                'spreadsheetId' => $request->input('spreadsheet_id'),
                'spreadsheetSheetName' => $request->input('spreadsheet_sheet_name'),
                'imagesURL' => $request->input('images_url'),
                'verso' => [
                    'margin' => (float) $request->input('verso_margin'),
                    'border-margin' => (float) $request->input('verso_border_margin'),
                    'image-margin' => (float) $request->input('verso_image_margin'),
                    'text-1-top' => (float) $request->input('verso_text_1_top'),
                    'text-2-top' => (float) $request->input('verso_text_2_top'),
                    'primary-font-size' => (float) $request->input('verso_primary_font_size'),
                    'secondary-font-size' => (float) $request->input('verso_secondary_font_size'),
                    'text-1-font-family' => $request->input('verso_text_1_font_family'),
                    'text-2-font-family' => $request->input('verso_text_2_font_family')
                ],
                'recto' => [
                    'margin' => (float) $request->input('recto_margin'),
                    'image-margin' => (float) $request->input('recto_image_margin'),
                    'font-size' => (float) $request->input('recto_font_size'),
                    'text-top' => (float) $request->input('recto_text_top'),
                    'text-font-family' => $request->input('recto_text_font_family')
                ],
                'fonts' => $request->input('selected_fonts', []) // Fuentes seleccionadas
            ];

            // Función helper para construir configuración de página con textos multiidioma
            $buildPageConfig = function($pageNumber, $existingPageConfig = []) use ($request, $selectedLanguage) {
                $pageTextContent = $request->input("page{$pageNumber}_text");

                // Configuración base de la página
                $pageConfig = [
                    'text' => $pageTextContent, // Mantener el texto actual
                    'text-top' => (float) $request->input("page{$pageNumber}_text_top"),
                    'text-margin-x' => (float) $request->input("page{$pageNumber}_text_margin_x"),
                    'background-url' => $request->input("page{$pageNumber}_background_url"),
                    'image-url' => $request->input("page{$pageNumber}_image_url"),
                    'image-top' => (float) $request->input("page{$pageNumber}_image_top"),
                    'image-height' => (float) $request->input("page{$pageNumber}_image_height"),
                    'image-width' => $request->input("page{$pageNumber}_image_width")
                ];

                // Preservar textos existentes de otros idiomas
                foreach ($existingPageConfig as $key => $value) {
                    if (strpos($key, 'text_') === 0 && $key !== "text_{$selectedLanguage}") {
                        $pageConfig[$key] = $value;
                    }
                }

                // Agregar o actualizar el texto para el idioma actual
                if (!empty($pageTextContent)) {
                    $pageConfig["text_{$selectedLanguage}"] = $pageTextContent;
                }

                return $pageConfig;
            };

            // Construir configuraciones de páginas con soporte multiidioma
            $config['page1'] = $buildPageConfig(1, $existingConfig['page1'] ?? []);
            $config['page2'] = $buildPageConfig(2, $existingConfig['page2'] ?? []);
            $config['page3'] = $buildPageConfig(3, $existingConfig['page3'] ?? []);
            $config['page4'] = $buildPageConfig(4, $existingConfig['page4'] ?? []);
            $config['page5'] = $buildPageConfig(5, $existingConfig['page5'] ?? []);

            // Crear backup antes de actualizar
            $this->createBackup($jsonPath, $filename);

            // Crear el directorio si no existe
            $jsonDir = dirname($jsonPath);
            if (!is_dir($jsonDir)) {
                mkdir($jsonDir, 0755, true);
            }

            // Convertir a JSON con formato bonito
            $jsonContent = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            if ($jsonContent === false) {
                throw new \Exception('Error al generar el JSON: ' . json_last_error_msg());
            }

            // Guardar el archivo
            $result = file_put_contents($jsonPath, $jsonContent);

            if ($result === false) {
                throw new \Exception('Error al escribir el archivo JSON');
            }

            return response()->json([
                'success' => true,
                'message' => "Configuración guardada exitosamente para idioma '{$selectedLanguage}'",
                'file_size' => $result,
                'language' => $selectedLanguage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loadConfig(Request $request)
    {
        try {
            $filename = $request->get('filename');
            $selectedLanguage = strtolower($request->get('language', 'es'));

            if (!$filename) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nombre de archivo requerido'
                ], 400);
            }

            // Obtener solo el nombre del archivo sin la ruta
            $filename = basename($filename);

            // Ruta del archivo JSON
            $jsonPath = public_path('json/' . $filename);

            if (!file_exists($jsonPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo no encontrado: ' . $filename
                ], 404);
            }

            // Leer y decodificar el JSON
            $jsonContent = file_get_contents($jsonPath);
            $config = json_decode($jsonContent, true);

            if ($config === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al leer el archivo JSON: ' . json_last_error_msg()
                ], 500);
            }

            // Ajustar textos según el idioma seleccionado
            foreach (['page1', 'page2', 'page3', 'page4', 'page5'] as $page) {
                if (isset($config[$page])) {
                    $languageTextKey = "text_{$selectedLanguage}";

                    // Si existe texto para el idioma específico, usarlo
                    if (isset($config[$page][$languageTextKey]) && !empty($config[$page][$languageTextKey])) {
                        $config[$page]['text'] = $config[$page][$languageTextKey];
                    }
                    // Si no existe pero hay texto genérico, mantenerlo
                    // (no hacer nada, mantener el texto actual)
                }
            }

            return response()->json($config);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar la configuración: ' . $e->getMessage()
            ], 500);
        }
    }


    public function generateColorBookPdf(Request $request)
    {
        ini_set('max_execution_time', '1500');
        set_time_limit(60 * 25);

        try {
            // Obtener el archivo JSON seleccionado desde el request
            $selectedJsonFile = $request->input('selectedJsonFile');
            $filename = basename($selectedJsonFile);

            // Leer configuración desde archivo JSON
            $layoutJsonPath = public_path('json/' . $filename);
            if (!file_exists($layoutJsonPath)) {
                throw new \Exception("Layout configuration file not found at: " . $layoutJsonPath);
            }

            $layoutJson = file_get_contents($layoutJsonPath);
            $config = json_decode($layoutJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Error parsing layout JSON: " . json_last_error_msg());
            }

            $numberOfPages = $request->input('numberOfPages', 0);
            $languageSelector = $request->input('languageSelector', 'ES');

            // Obtener los parámetros desde el JSON
            $spreadsheetId = $config['spreadsheetId'] ?? null;
            $sheetName = $config['spreadsheetSheetName'] ?? null;
            $imagesURL = $config['imagesURL'] ?? null;

            // Validar que los parámetros requeridos estén presentes
            if (!$spreadsheetId) {
                throw new \Exception("spreadsheetId not found in configuration");
            }
            if (!$sheetName) {
                throw new \Exception("spreadsheetSheetName not found in configuration");
            }
            if (!$imagesURL) {
                throw new \Exception("imagesURL not found in configuration");
            }

            // Procesar datos usando la lógica común
            $items = $this->processSpreadsheetData($spreadsheetId, $sheetName, $imagesURL, $numberOfPages, $languageSelector, 'temp_google_excel.xlsx');

            // Usar fuentes seleccionadas por el usuario o detectar automáticamente
            $quillFonts = $config['fonts'] ?? [];

            $data = [
                'items' => $items,
                'title' => 'Color Book PDF',
                'totalWords' => count($items),
                'foundImages' => count(array_filter($items, function ($item) {
                    return !isset($item['image']['missing']);
                })),
                'layout' => $config,
                'quillFonts' => $quillFonts,
                'preview' => $request->has('preview')
            ];

            if ($request->has('preview')) {
                return view('pdf.colorbook_margin', $data);
            }

            $pdf = Pdf::loadView('pdf.colorbook_margin', $data)
                ->setPaper([0, 0, $data["layout"]["width"] / 2.54 * 72, $data["layout"]["height"] / 2.54 * 72]);

            return $pdf->download('color-book-' . date('Y-m-d-H-i-s') . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getFonts()
    {
        try {
            return response()->json(FontManager::getAllFonts());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getFontCSS()
    {
        try {
            $css = FontManager::generateQuillFontCSS();
            return response($css)->header('Content-Type', 'text/css');
        } catch (\Exception $e) {
            return response('/* Error loading fonts */', 500)->header('Content-Type', 'text/css');
        }
    }


    /**
     * Extrae todas las fuentes utilizadas en los textos del JSON de configuración
     * Busca clases CSS con prefijo ql-font- y extrae el nombre de la fuente
     */
    private function extractFontsFromConfig($config)
    {
        $fonts = [];

        // Función recursiva para buscar en arrays/objetos anidados
        $extractFromValue = function ($value) use (&$fonts, &$extractFromValue) {
            if (is_string($value)) {
                // Buscar patrones de clases ql-font- en el texto
                // Busca: class="ql-font-NombreFuente" o class="algo ql-font-NombreFuente otros"
                preg_match_all('/ql-font-([a-zA-Z0-9_-]+)/', $value, $matches);

                if (!empty($matches[1])) {
                    foreach ($matches[1] as $fontName) {
                        $fontName = trim($fontName);
                        if (!empty($fontName) && !in_array($fontName, $fonts)) {
                            $fonts[] = $fontName;
                        }
                    }
                }
            } elseif (is_array($value)) {
                foreach ($value as $item) {
                    $extractFromValue($item);
                }
            }
        };

        // Recorrer toda la configuración buscando textos con clases ql-font-
        $extractFromValue($config);

        return array_unique($fonts);
    }



    /**
     * Elimina todas las fuentes del directorio especificado
     */
    private function clearAllFonts($directory)
    {
        if (!is_dir($directory)) {
            Log::info('Directorio de fuentes no existe: ' . $directory);
            return;
        }

        $deletedCount = 0;
        $errorCount = 0;

        // Obtener todos los archivos del directorio
        $files = glob($directory . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $filename = basename($file);

                // Eliminar archivos de fuentes comunes
                if (in_array($extension, ['ttf', 'otf', 'woff', 'woff2', 'eot', 'svg', 'ufm', 'afm'])) {
                    try {
                        if (unlink($file)) {
                            $deletedCount++;
                            Log::info('Fuente eliminada: ' . $filename);
                        } else {
                            $errorCount++;
                            Log::warning('No se pudo eliminar la fuente: ' . $filename);
                        }
                    } catch (\Exception $e) {
                        $errorCount++;
                        Log::warning('Error eliminando fuente ' . $filename . ': ' . $e->getMessage());
                    }
                }
            }
        }

        Log::info("Limpieza de fuentes completada. Eliminadas: {$deletedCount}, Errores: {$errorCount}");
    }

    public function getPreview(Request $request)
    {
        ini_set('max_execution_time', '600');
        set_time_limit(60 * 10);

        try {
            // Obtener parámetros directamente del request
            $numberOfPages = $request->input('numberOfPages', 5);
            $languageSelector = $request->input('languageSelector', 'ES');
            $spreadsheetId = $request->input('spreadsheetId');
            $sheetName = $request->input('sheetName');
            $imagesURL = $request->input('imagesURL');

            // Validar que los parámetros requeridos estén presentes
            if (!$spreadsheetId) {
                throw new \Exception("spreadsheetId is required");
            }
            if (!$sheetName) {
                throw new \Exception("sheetName is required");
            }
            if (!$imagesURL) {
                throw new \Exception("imagesURL is required");
            }

            // Procesar datos usando la lógica común
            $items = $this->processSpreadsheetData($spreadsheetId, $sheetName, $imagesURL, $numberOfPages, $languageSelector, 'temp_preview_excel.xlsx');

            // Obtener configuración de layout desde request y decodificar JSON
            $layoutRaw = $request->input('layout', '{}');
            $layout = is_string($layoutRaw) ? json_decode($layoutRaw, true) : $layoutRaw;

            // Validar que el layout se decodificó correctamente
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid layout JSON: " . json_last_error_msg());
            }

            // Obtener fuentes seleccionadas desde request
            $quillFonts = $request->input('selectedFonts', []);

            $data = [
                'items' => $items,
                'title' => 'Color Book Preview',
                'totalWords' => count($items),
                'foundImages' => count(array_filter($items, function ($item) {
                    return !isset($item['image']['missing']);
                })),
                'layout' => $layout,
                'quillFonts' => $quillFonts,
                'preview' => true
            ];

            return view('pdf.colorbook_margin', $data);

        } catch (\Exception $e) {
            return response()->view('errors.preview', [
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getPreviewPdf(Request $request)
    {
        ini_set('max_execution_time', '600');
        set_time_limit(60 * 10);

        try {
            // Obtener parámetros directamente del request


            $selectedJsonFile = $request->input('selectedJsonFile');

            $numberOfPages = $request->input('numberOfPages'); // Limitar páginas para preview
            $languageSelector = $request->input('languageSelector', 'ES');
            $spreadsheetId = $request->input('spreadsheetId');
            $sheetName = $request->input('sheetName');
            $imagesURL = $request->input('imagesURL');

            // Validar que los parámetros requeridos estén presentes
            if (!$spreadsheetId) {
                throw new \Exception("spreadsheetId is required");
            }
            if (!$sheetName) {
                throw new \Exception("sheetName is required");
            }
            if (!$imagesURL) {
                throw new \Exception("imagesURL is required");
            }

            // Procesar datos usando la lógica común
            $items = $this->processSpreadsheetData($spreadsheetId, $sheetName, $imagesURL, $numberOfPages, $languageSelector, 'temp_preview_excel.xlsx');

            // Obtener configuración de layout desde request y decodificar JSON
            $layoutRaw = $request->input('layout', '{}');
            $layout = is_string($layoutRaw) ? json_decode($layoutRaw, true) : $layoutRaw;

            // Validar que el layout se decodificó correctamente
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Invalid layout JSON: " . json_last_error_msg());
            }

            // Obtener fuentes seleccionadas desde request
            $quillFonts = FontManager::generatePdfFontCSS($selectedJsonFile);

            $data = [
                'items' => $items,
                'title' => 'Color Book Preview PDF',
                'totalWords' => count($items),
                'foundImages' => count(array_filter($items, function ($item) {
                    return !isset($item['image']['missing']);
                })),
                'layout' => $layout,
                'quillFonts' => $quillFonts,
                'preview' => true // Para PDF usar fuentes locales, no preview mode
            ];

            // Generar PDF usando DOMPDF
            $pdf = Pdf::loadView('pdf.colorbook_margin', $data)
                ->setPaper([0, 0, $layout["width"] / 2.54 * 72, $layout["height"] / 2.54 * 72]);

            // Generar nombre único para el archivo PDF temporal
            $tempPdfName = 'preview_' . uniqid() . '_' . date('Y-m-d-H-i-s') . '.pdf';

            // Crear directorio temporal si no existe
            $tempDir = public_path('temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Ruta completa del archivo temporal
            $tempPdfPath = $tempDir . DIRECTORY_SEPARATOR . $tempPdfName;

            // Guardar PDF en archivo temporal
            $pdfOutput = $pdf->output();
            file_put_contents($tempPdfPath, $pdfOutput);

            // Limpiar archivos temporales antiguos (más de 1 hora)
            $this->cleanOldTempFiles($tempDir);

            // Generar URL del PDF temporal
            $pdfUrl = url('temp/' . $tempPdfName);

            // Retornar JSON con la URL del PDF
            return response()->json([
                'success' => true,
                'pdf_url' => $pdfUrl,
                'filename' => $tempPdfName,
                'message' => 'PDF generado exitosamente'
            ]);

        } catch (\Exception $e) {
            // En caso de error, retornar JSON con error
            return response()->json([
                'success' => false,
                'error' => 'Error generando preview PDF: ' . $e->getMessage()
            ], 500);
        }
    }    /**
     * Procesa los datos del spreadsheet de Google y retorna los items procesados
     */
    private function processSpreadsheetData($spreadsheetId, $sheetName, $imagesURL, $numberOfPages, $languageSelector, $tempFileName)
    {
        // Descargar el archivo completo de Google Spreadsheet como Excel (sin filtro específico de hoja)
        $exportUrl = "https://docs.google.com/spreadsheets/d/$spreadsheetId/export?format=xlsx";

        // Descargar el archivo temporalmente
        $tempExcel = storage_path('app/' . $tempFileName);
        file_put_contents($tempExcel, file_get_contents($exportUrl));

        try {
            // Verificar que el archivo existe
            if (!file_exists($tempExcel)) {
                throw new \Exception("Excel file could not be downloaded");
            }

            // Leer TODAS las hojas del Excel
            $allSheets = Excel::toArray([], $tempExcel, null, \Maatwebsite\Excel\Excel::XLSX);

            Log::info("Procesando Excel - Hojas encontradas:", ['total' => count($allSheets), 'target' => $sheetName]);

            // Obtener los nombres reales de las hojas
            $sheetNames = [];
            try {
                $reader = \Maatwebsite\Excel\Facades\Excel::import(new class implements \Maatwebsite\Excel\Concerns\WithMultipleSheets {
                    public function sheets(): array { return []; }
                }, $tempExcel);

                // Usar PhpSpreadsheet directamente para obtener los nombres de hojas
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempExcel);
                $sheetNames = $spreadsheet->getSheetNames();
                Log::info("Nombres reales de hojas obtenidos:", $sheetNames);
            } catch (\Exception $e) {
                Log::warning("No se pudieron obtener nombres de hojas:", ['error' => $e->getMessage()]);
                // Fallback: usar nombres genéricos
                for ($i = 0; $i < count($allSheets); $i++) {
                    $sheetNames[] = "Sheet_" . $i;
                }
            }

            // Buscar la hoja por nombre exacto primero
            $targetSheetIndex = null;
            foreach ($sheetNames as $index => $realSheetName) {
                if (trim($realSheetName) === trim($sheetName)) {
                    $targetSheetIndex = $index;
                    Log::info("Hoja encontrada por nombre exacto:", ['name' => $realSheetName, 'index' => $index]);
                    break;
                }
            }

            // Si no se encuentra por nombre exacto, buscar por headers
            if ($targetSheetIndex === null) {
                Log::info("Buscando hoja por headers...");
                foreach ($allSheets as $index => $sheetData) {
                    $firstRow = $sheetData[0] ?? [];
                    $headersString = strtoupper(implode('|', $firstRow));

                    // Verificar si tiene los headers que necesitamos
                    if (strpos($headersString, 'EN') !== false &&
                        strpos($headersString, 'SYM') !== false &&
                        strpos($headersString, strtoupper($languageSelector)) !== false) {
                        $targetSheetIndex = $index;
                        $actualSheetName = $sheetNames[$index] ?? "Sheet_$index";
                        Log::info("Hoja encontrada por headers:", [
                            'requested' => $sheetName,
                            'actual' => $actualSheetName,
                            'index' => $index,
                            'headers' => array_slice($firstRow, 0, 10)
                        ]);
                        break;
                    }
                }
            }

            if ($targetSheetIndex === null) {
                $availableSheets = [];
                foreach ($sheetNames as $index => $name) {
                    $firstRow = $allSheets[$index][0] ?? [];
                    $availableSheets[] = [
                        'index' => $index,
                        'name' => $name,
                        'headers' => array_slice($firstRow, 0, 5)
                    ];
                }
                throw new \Exception("No se encontró la hoja '$sheetName'. Hojas disponibles: " . json_encode($availableSheets));
            }

            // Usar la hoja encontrada
            $hoja = $allSheets[$targetSheetIndex];
            $actualSheetName = $sheetNames[$targetSheetIndex] ?? "Sheet_$targetSheetIndex";
            Log::info("Procesando hoja final:", [
                'requested' => $sheetName,
                'actual' => $actualSheetName,
                'index' => $targetSheetIndex,
                'rows' => count($hoja),
                'headers' => array_slice($hoja[0] ?? [], 0, 10)
            ]);
            $limpio = $this->cleanExcelData($hoja);

            // Buscar las columnas requeridas
            $columnIndexes = $this->findRequiredColumns($limpio[0] ?? [], $languageSelector);

            // Extraer los datos de las filas
            $rowData = $this->extractRowData($limpio, $columnIndexes, $numberOfPages);

            // Construir los items finales con URLs de imágenes
            $items = $this->buildItemsWithImages($rowData, $imagesURL);

            return $items;

        } finally {
            // Eliminar el archivo temporal siempre
            if (file_exists($tempExcel)) {
                unlink($tempExcel);
            }
        }
    }

    /**
     * Limpia los datos del Excel procesando fórmulas
     */
    private function cleanExcelData($hoja)
    {
        return array_map(function ($fila) {
            return array_map(function ($celda) {
                // Si contiene una fórmula, intenta extraer el valor de respaldo
                if (is_string($celda) && str_starts_with($celda, '=')) {
                    // Intenta extraer valor entre comillas finales (ejemplo: ,"Start")
                    preg_match('/,"([^"]+)"\)$/', $celda, $matches);
                    return $matches[1] ?? $celda;
                }
                return $celda;
            }, $fila);
        }, $hoja);
    }

    /**
     * Busca las columnas requeridas EN, SYM, SY2, y el idioma seleccionado en la fila de headers
     */
    private function findRequiredColumns($headerRow, $languageSelector)
    {
        $enColumnIndex = null;
        $symColumnIndex = null;
        $sy2ColumnIndex = null;
        $languageColumnIndex = null;

        foreach ($headerRow as $index => $header) {
            switch (strtoupper($header)) {
                case 'EN':
                    if(strtoupper($languageSelector) === 'EN') {
                        $languageColumnIndex = $index;
                    }
                    $enColumnIndex = $index;
                    break;
                case 'SYM':
                    $symColumnIndex = $index;
                    break;
                case 'SY2':
                    $sy2ColumnIndex = $index;
                    break;
                case strtoupper($languageSelector):
                    $languageColumnIndex = $index;
                    break;
            }
        }

        // Validar que todas las columnas requeridas fueron encontradas
        if ($enColumnIndex === null) {
            throw new \Exception("Column 'EN' not found in the Excel file");
        }
        if ($symColumnIndex === null) {
            throw new \Exception("Column 'SYM' not found in the Excel file");
        }
        if ($sy2ColumnIndex === null) {
            throw new \Exception("Column 'SY2' not found in the Excel file");
        }
        if ($languageColumnIndex === null) {
            throw new \Exception("Column '" . strtoupper($languageSelector) . "' not found in the Excel file");
        }

        return [
            'en' => $enColumnIndex,
            'sym' => $symColumnIndex,
            'sy2' => $sy2ColumnIndex,
            'language' => $languageColumnIndex
        ];
    }

    /**
     * Extrae los datos de las filas del Excel
     */
    private function extractRowData($limpio, $columnIndexes, $numberOfPages)
    {
        $rowData = [];
        $totalRows = count($limpio);

        if ($numberOfPages > 0) {
            $totalRows = $numberOfPages + 1; // +1 para incluir la fila de encabezados
        }

        for ($row = 1; $row < $totalRows; $row++) {
            $enValue = $limpio[$row][$columnIndexes['en']] ?? null;
            $symValue = $limpio[$row][$columnIndexes['sym']] ?? null;
            $sy2Value = $limpio[$row][$columnIndexes['sy2']] ?? null;
            $languageValue = $limpio[$row][$columnIndexes['language']] ?? null;

            if (!empty($enValue)) {
                $rowData[] = [
                    'en' => trim($enValue),
                    'sym' => trim($symValue ?? ''),
                    'sy2' => trim($sy2Value ?? ''),
                    'language' => trim($languageValue ?? '')
                ];
            }
        }

        return $rowData;
    }

    /**
     * Construye los items finales con URLs de imágenes
     */
    private function buildItemsWithImages($rowData, $imagesURL)
    {
        $items = [];

        foreach ($rowData as $row) {
            $enValue = $row['en'];
            $symValue = $row['sym'];
            $sy2Value = $row['sy2'];
            $languageValue = $row['language'];

            // Procesar el nombre del archivo de imagen
            $enValue = str_replace(' ', '', $enValue);
            $enValue = preg_replace('/[^a-zA-Z]/', '_', $enValue);
            $imageUrl = $imagesURL . strtolower($enValue) . '.png';

            // Verificar si la imagen existe
            $headers = @get_headers($imageUrl);
            if ($headers && strpos($headers[0], '200') !== false) {
                $items[] = [
                    'image' => [
                        'url' => $imageUrl,
                        'name' => $enValue
                    ],
                    'sym' => $symValue,
                    'sy2' => $sy2Value,
                    'language' => $languageValue
                ];
            } else {
                // Incluir imágenes que no existen con placeholder
                $items[] = [
                    'image' => [
                        'url' => $imageUrl,
                        'name' => $enValue,
                        'missing' => true
                    ],
                    'sym' => $symValue,
                    'sy2' => $sy2Value,
                    'language' => $languageValue
                ];
            }
        }

        return $items;
    }

    /**
     * Método de prueba para diagnosticar la lectura de hojas de Excel
     */
    public function testExcelReading(Request $request)
    {
        try {
            // Parámetros de prueba - usar los del JSON actual
            $spreadsheetId = '1nYdFCcD5hLjPmz1xmddfNupjItjOI4riFzgpKX9Bq7k';
            $targetSheetName = $request->get('sheet', 'Chino'); // Permitir especificar la hoja
            $testPages = 3; // Solo procesar 3 páginas para prueba

            Log::info('=== INICIO TEST EXCEL READING ===');
            Log::info('Spreadsheet ID:', ['id' => $spreadsheetId]);
            Log::info('Target Sheet:', ['name' => $targetSheetName]);

            // Probar el método processSpreadsheetData directamente
            $items = $this->processSpreadsheetData(
                $spreadsheetId,
                $targetSheetName,
                'https://example.com/images/', // URL de prueba
                $testPages,
                'ES', // Idioma de prueba
                'test_process_' . time() . '.xlsx'
            );

            $result = [
                'success' => true,
                'spreadsheetId' => $spreadsheetId,
                'targetSheetName' => $targetSheetName,
                'itemsProcessed' => count($items),
                'sampleItems' => array_slice($items, 0, 3), // Primeros 3 items como muestra
                'method' => 'processSpreadsheetData',
                'timestamp' => now()->toDateTimeString()
            ];

            Log::info('Test completado exitosamente:', $result);
            Log::info('=== FIN TEST EXCEL READING ===');

            return response()->json([
                'success' => true,
                'message' => "Test de procesamiento de Excel completado exitosamente para hoja '$targetSheetName'",
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Error en test de Excel:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error en test de Excel: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'targetSheet' => $targetSheetName
                ]
            ], 500);
        }
    }

    /**
     * Método para listar todas las hojas disponibles en el Excel
     */
    public function listExcelSheets(Request $request)
    {
        try {
            $spreadsheetId = $request->get('spreadsheetId', '1nYdFCcD5hLjPmz1xmddfNupjItjOI4riFzgpKX9Bq7k');

            // Descargar el archivo Excel
            $exportUrl = "https://docs.google.com/spreadsheets/d/$spreadsheetId/export?format=xlsx";
            $tempExcel = storage_path('app/list_sheets_' . time() . '.xlsx');
            file_put_contents($tempExcel, file_get_contents($exportUrl));

            try {
                // Leer todas las hojas
                $allSheets = Excel::toArray([], $tempExcel, null, \Maatwebsite\Excel\Excel::XLSX);

                // Obtener nombres reales de hojas
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tempExcel);
                $sheetNames = $spreadsheet->getSheetNames();

                $sheetDetails = [];
                foreach ($sheetNames as $index => $sheetName) {
                    $sheetData = $allSheets[$index] ?? [];
                    $firstRow = $sheetData[0] ?? [];

                    $hasRequiredHeaders = (
                        strpos(strtoupper(implode('|', $firstRow)), 'EN') !== false &&
                        strpos(strtoupper(implode('|', $firstRow)), 'SYM') !== false &&
                        (strpos(strtoupper(implode('|', $firstRow)), 'ES') !== false ||
                         strpos(strtoupper(implode('|', $firstRow)), 'EN') !== false)
                    );

                    $sheetDetails[] = [
                        'index' => $index,
                        'name' => $sheetName,
                        'totalRows' => count($sheetData),
                        'headers' => array_slice($firstRow, 0, 10),
                        'hasRequiredHeaders' => $hasRequiredHeaders
                    ];
                }

                return response()->json([
                    'success' => true,
                    'spreadsheetId' => $spreadsheetId,
                    'totalSheets' => count($sheetNames),
                    'sheets' => $sheetDetails
                ]);

            } finally {
                if (file_exists($tempExcel)) {
                    unlink($tempExcel);
                }
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error listando hojas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea un backup del archivo JSON antes de actualizarlo
     */
    private function createBackup($jsonPath, $filename)
    {
        try {
            // Verificar si el archivo original existe
            if (!file_exists($jsonPath)) {
                // Si no existe el archivo original, no hay nada que respaldar
                return;
            }

            // Crear la carpeta de backups si no existe
            $backupDir = public_path('backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            // Generar nombre del backup con timestamp
            $timestamp = date('Y-m-d_H-i-s');
            $backupFilename = pathinfo($filename, PATHINFO_FILENAME) . '_backup_' . $timestamp . '.json';
            $backupPath = $backupDir . '/' . $backupFilename;

            // Copiar el archivo original al backup
            if (copy($jsonPath, $backupPath)) {
                Log::info("Backup creado exitosamente: " . $backupFilename);
            } else {
                Log::warning("Error al crear backup para: " . $filename);
            }

        } catch (\Exception $e) {
            Log::error("Error creando backup: " . $e->getMessage());
            // No lanzamos excepción para no interrumpir el guardado principal
        }
    }

    /**
     * Limpia archivos temporales antiguos
     */
    private function cleanOldTempFiles($tempDir, $maxAge = 3600)
    {
        try {
            if (!is_dir($tempDir)) {
                return;
            }

            $files = glob($tempDir . '/*');
            $currentTime = time();
            $deletedCount = 0;

            foreach ($files as $file) {
                if (is_file($file)) {
                    $fileAge = $currentTime - filemtime($file);

                    // Eliminar archivos más antiguos que $maxAge segundos (por defecto 1 hora)
                    if ($fileAge > $maxAge) {
                        if (unlink($file)) {
                            $deletedCount++;
                        }
                    }
                }
            }

            if ($deletedCount > 0) {
                Log::info("Limpieza de archivos temporales: eliminados {$deletedCount} archivos");
            }

        } catch (\Exception $e) {
            Log::error("Error limpiando archivos temporales: " . $e->getMessage());
        }
    }

    /**
     * Obtiene la lista de backups disponibles
     */
    public function getBackups(Request $request)
    {
        try {
            $backupDir = public_path('backups');
            $backups = [];

            if (is_dir($backupDir)) {
                $files = scandir($backupDir);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                        $filePath = $backupDir . '/' . $file;
                        $backups[] = [
                            'filename' => $file,
                            'size' => filesize($filePath),
                            'created' => date('Y-m-d H:i:s', filemtime($filePath)),
                            'path' => '/backups/' . $file
                        ];
                    }
                }

                // Ordenar por fecha de creación (más reciente primero)
                usort($backups, function ($a, $b) {
                    return strtotime($b['created']) - strtotime($a['created']);
                });
            }

            return response()->json([
                'success' => true,
                'backups' => $backups,
                'total' => count($backups)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error obteniendo backups: ' . $e->getMessage()
            ], 500);
        }
    }
}
