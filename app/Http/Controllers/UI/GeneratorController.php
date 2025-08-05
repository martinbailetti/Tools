<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Services\FontManager;
use Barryvdh\DomPDF\Facade\Pdf;
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
                    'secondary-font-size' => (float) $request->input('verso_secondary_font_size')
                ],
                'recto' => [
                    'margin' => (float) $request->input('recto_margin'),
                    'image-margin' => (float) $request->input('recto_image_margin'),
                    'font-size' => (float) $request->input('recto_font_size'),
                    'text-top' => (float) $request->input('recto_text_top')
                ],
                'page1' => [
                    'title-line-1' => $request->input('page1_title_line_1'),
                    'title-line-1-y-percentage' => (float) $request->input('page1_title_line_1_y_percentage'),
                    'title-line-1-font-size' => (float) $request->input('page1_title_line_1_font_size'),
                    'title-line-2' => $request->input('page1_title_line_2'),
                    'title-line-2-font-size' => (float) $request->input('page1_title_line_2_font_size'),
                    'title-line-2-margin' => (float) $request->input('page1_title_line_2_margin'),
                    'title-line-3' => $request->input('page1_title_line_3'),
                    'title-line-3-margin' => (float) $request->input('page1_title_line_3_margin'),
                    'title-line-3-font-size' => (float) $request->input('page1_title_line_3_font_size'),
                    'logo-image' => $request->input('page1_logo_image'),
                    'logo-y-percentage' => (float) $request->input('page1_logo_y_percentage'),
                    'logo-height' => (float) $request->input('page1_logo_height')
                ],
                'page2' => [
                    'text-top' => (float) $request->input('page2_text_top'),
                    'text-y-space' => (float) $request->input('page2_text_y_space'),
                    'text-font-size' => (float) $request->input('page2_text_font_size'),
                    'margin-x' => (float) $request->input('page2_margin_x'),
                    'margin-bottom' => (float) $request->input('page2_margin_bottom'),
                    'text-block-1' => $request->input('page2_text_block_1'),
                    'text-block-2' => $request->input('page2_text_block_2'),
                    'text-block-3' => $request->input('page2_text_block_3'),
                    'text-block-4' => $request->input('page2_text_block_4')
                ],
                'page3' => [
                    'image' => $request->input('page3_image'),
                    'image-y-percentage' => (float) $request->input('page3_image_y_percentage'),
                    'margin' => (float) $request->input('page3_margin')
                ],
                'page4' => [
                    'image' => $request->input('page4_image')
                ],
                'page5' => [
                    'image' => $request->input('page5_image'),
                    'header-y' => (float) $request->input('page5_header_y'),
                    'header-font-size' => (float) $request->input('page5_header_font_size'),
                    'header-text' => $request->input('page5_header_text'),
                    'footer-y' => (float) $request->input('page5_footer_y'),
                    'footer-font-size' => (float) $request->input('page5_footer_font_size'),
                    'footer-text' => $request->input('page5_footer_text'),
                    'text-font-size' => (float) $request->input('page5_text_font_size'),
                    'text-1-y' => (float) $request->input('page5_text_1_y'),
                    'text-1-content' => $request->input('page5_text_1_content'),
                    'text-2-y' => (float) $request->input('page5_text_2_y'),
                    'text-2-content' => $request->input('page5_text_2_content'),
                    'text-3-y' => (float) $request->input('page5_text_3_y'),
                    'text-3-content' => $request->input('page5_text_3_content'),
                    'margin' => (float) $request->input('page5_margin')
                ],
                'fonts' => $request->input('selected_fonts', []) // Fuentes seleccionadas
            ];

            // Obtener el nombre del archivo JSON desde el request, usar 'chinese.json' por defecto
            $selectedJsonFile = $request->input('selected_json_file', '/json/chinese.json');
            $filename = basename($selectedJsonFile);

            // Ruta del archivo JSON
            $jsonPath = public_path('json/' . $filename);

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
                'message' => 'Configuración guardada exitosamente',
                'file_size' => $result
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
            $selectedJsonFile = $request->input('selectedJsonFile', '/json/chinese.json');
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
            $items = $this->processSpreadsheetData($spreadsheetId, $sheetName, $imagesURL, $numberOfPages, 'temp_google_excel.xlsx');

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
     * Limpia el cache de DomPDF para evitar duplicación de fuentes
     */
    private function clearDomPDFCache()
    {
        try {
            // Rutas donde DomPDF puede almacenar cache de fuentes
            $fontPaths = [
                storage_path('fonts'),
                base_path('storage/fonts'),
                resource_path('fonts'),
            ];

            foreach ($fontPaths as $fontPath) {
                if (is_dir($fontPath)) {
                    $this->cleanFontCache($fontPath);
                }
            }

            // También limpiar cache específico de DomPDF
            $dompdfCachePath = storage_path('app/dompdf');
            if (is_dir($dompdfCachePath)) {
                $this->cleanFontCache($dompdfCachePath);
            }
        } catch (\Exception $e) {
            // Log error pero no fallar la generación
            Log::warning('Error limpiando cache de DomPDF: ' . $e->getMessage());
        }
    }

    /**
     * Limpia archivos de cache de fuentes en un directorio específico
     */
    private function cleanFontCache($directory)
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = glob($directory . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $filename = pathinfo($file, PATHINFO_FILENAME);

                // Eliminar archivos cache (.ufm, .afm) que tengan hash duplicados
                if (in_array($extension, ['ufm', 'afm'])) {
                    // Si el archivo tiene un hash MD5/SHA al final y ya existe una versión sin hash
                    if (preg_match('/_[a-f0-9]{32}$/', $filename)) {
                        $cleanName = preg_replace('/_[a-f0-9]{32}$/', '', $filename);
                        $cleanFile = $directory . '/' . $cleanName . '.' . $extension;

                        // Si existe la versión limpia, eliminar la duplicada
                        if (file_exists($cleanFile)) {
                            unlink($file);
                        }
                    }
                }
            }
        }
    }

    public function getPreview(Request $request)
    {
        ini_set('max_execution_time', '600');
        set_time_limit(60 * 10);

        try {
            // Obtener parámetros directamente del request
            $numberOfPages = $request->input('numberOfPages', 5);
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
            $items = $this->processSpreadsheetData($spreadsheetId, $sheetName, $imagesURL, $numberOfPages, 'temp_preview_excel.xlsx');

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
            $numberOfPages = $request->input('numberOfPages', 5); // Limitar páginas para preview
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
            $items = $this->processSpreadsheetData($spreadsheetId, $sheetName, $imagesURL, $numberOfPages, 'temp_preview_excel.xlsx');

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
                'title' => 'Color Book Preview PDF',
                'totalWords' => count($items),
                'foundImages' => count(array_filter($items, function ($item) {
                    return !isset($item['image']['missing']);
                })),
                'layout' => $layout,
                'quillFonts' => $quillFonts,
                'preview' => false // Para PDF usar fuentes locales, no preview mode
            ];

            // Generar PDF usando DOMPDF
            $pdf = Pdf::loadView('pdf.colorbook_margin', $data)->setPaper([0, 0, $layout["width"] / 2.54 * 72, $layout["height"] / 2.54 * 72]);



            // Retornar el PDF como respuesta inline para mostrar en iframe
            return $pdf->stream('preview.pdf', ['Attachment' => false]);

        } catch (\Exception $e) {
            // En caso de error, crear un PDF con el mensaje de error
            $errorData = [
                'error' => true,
                'message' => $e->getMessage(),
                'title' => 'Error de Preview'
            ];

            try {
                $errorPdf = Pdf::loadView('errors.preview-pdf', $errorData);
                return $errorPdf->stream('error.pdf', ['Attachment' => false]);
            } catch (\Exception $pdfError) {
                // Si no se puede generar ni siquiera el PDF de error, devolver respuesta de texto
                return response('Error generando preview PDF: ' . $e->getMessage(), 500)
                    ->header('Content-Type', 'text/plain');
            }
        }
    }    /**
     * Procesa los datos del spreadsheet de Google y retorna los items procesados
     */
    private function processSpreadsheetData($spreadsheetId, $sheetName, $imagesURL, $numberOfPages, $tempFileName)
    {
        // Descargar el archivo de Google Spreadsheet como Excel
        $exportUrl = "https://docs.google.com/spreadsheets/d/$spreadsheetId/export?format=xlsx&sheet=$sheetName";

        // Descargar el archivo temporalmente
        $tempExcel = storage_path('app/' . $tempFileName);
        file_put_contents($tempExcel, file_get_contents($exportUrl));

        try {
            // Verificar que el archivo existe
            if (!file_exists($tempExcel)) {
                throw new \Exception("Excel file could not be downloaded");
            }

            // Leer y procesar el Excel
            $hoja = Excel::toArray([], $tempExcel, null, \Maatwebsite\Excel\Excel::XLSX)[0];
            $limpio = $this->cleanExcelData($hoja);

            // Buscar las columnas requeridas
            $columnIndexes = $this->findRequiredColumns($limpio[0] ?? []);

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
     * Busca las columnas requeridas EN, SYM, SY2, ES en la fila de headers
     */
    private function findRequiredColumns($headerRow)
    {
        $enColumnIndex = null;
        $symColumnIndex = null;
        $sy2ColumnIndex = null;
        $esColumnIndex = null;

        foreach ($headerRow as $index => $header) {
            switch (strtoupper($header)) {
                case 'EN':
                    $enColumnIndex = $index;
                    break;
                case 'SYM':
                    $symColumnIndex = $index;
                    break;
                case 'SY2':
                    $sy2ColumnIndex = $index;
                    break;
                case 'ES':
                    $esColumnIndex = $index;
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
        if ($esColumnIndex === null) {
            throw new \Exception("Column 'ES' not found in the Excel file");
        }

        return [
            'en' => $enColumnIndex,
            'sym' => $symColumnIndex,
            'sy2' => $sy2ColumnIndex,
            'es' => $esColumnIndex
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
            $esValue = $limpio[$row][$columnIndexes['es']] ?? null;

            if (!empty($enValue)) {
                $rowData[] = [
                    'en' => trim($enValue),
                    'sym' => trim($symValue ?? ''),
                    'sy2' => trim($sy2Value ?? ''),
                    'es' => trim($esValue ?? '')
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
            $esValue = $row['es'];

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
                    'es' => $esValue
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
                    'es' => $esValue
                ];
            }
        }

        return $items;
    }
}
