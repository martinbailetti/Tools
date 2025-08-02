<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PdfController extends Controller
{
    public function index()
    {

        $directories = [];
        $basePath = public_path('printables');

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        function getSubdirectories($dir)
        {
            $subdirs = [];
            foreach (scandir($dir) as $item) {
                if ($item === '.' || $item === '..') continue;
                $path = $dir . DIRECTORY_SEPARATOR . $item;
                if (is_dir($path)) {
                    $subdirs[] = [
                        'name' => $item,
                        'path' => $path,
                        'subdirectories' => getSubdirectories($path)
                    ];
                }
            }
            return $subdirs;
        }

        $directories = getSubdirectories($basePath);

        /*
        return response()->json(['directories' => $directories]); */

        return view('index', ['directories' => $directories]);
    }
    public function get($template, $token)
    {
        if ($template == "default") {
            try {
                $filePath = public_path("printables/{$template}/{$token}/header.png");
                if (!file_exists($filePath)) {
                    throw new \Exception("Header image not found.");
                }
                $filePath = public_path("printables/{$template}/{$token}/footer.png");
                if (!file_exists($filePath)) {
                    throw new \Exception("Footer image not found.");
                }
                $filePath = public_path("printables/{$template}/{$token}/content.png");
                if (!file_exists($filePath)) {
                    throw new \Exception("Content image not found.");
                }
                $data = [
                    'headerImage' => public_path("printables/{$template}/{$token}/header.png"),
                    'footerImage' => public_path("printables/{$template}/{$token}/footer.png"),
                    'contentImage' => public_path("printables/{$template}/{$token}/content.png"),
                    'title' => $template,
                ];


                $pdf = Pdf::loadView('pdf.default', $data);
                return $pdf->download("{$token}.pdf");
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        } else if ($template == "full") {

            try {
                $filePath = public_path("printables/{$template}/{$token}/page.png");
                if (!file_exists($filePath)) {
                    throw new \Exception("Page image not found.");
                }

                $data = [
                    'pageImage' => public_path("printables/{$template}/{$token}/page.png"),
                    'title' => $template,
                ];

                $pdf = Pdf::loadView('pdf.full', $data);
                return $pdf->download("{$token}.pdf");
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }





    public function generateColorBookPdf($excelFile = null)
    {
        ini_set('max_execution_time', '1500');
        set_time_limit(60 * 25);
        try {
            // Si no se proporciona archivo, buscar en storage/app/public
            if (!$excelFile) {
                $excelFile = public_path('/excel/test.xlsx'); // Ajusta el nombre según tu archivo
            }

            // Verificar que el archivo existe
            if (!file_exists($excelFile)) {
                throw new \Exception("Excel file not found at: " . $excelFile);
            }

            // Usar el mismo método que ExcelController para leer el Excel
            $hoja = Excel::toArray([], $excelFile, null, \Maatwebsite\Excel\Excel::XLSX)[0];

            $limpio = array_map(function ($fila) {
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

            // Buscar las columnas "EN", "SYM", "SY2" y "ES" en la primera fila (headers)
            $enColumnIndex = null;
            $symColumnIndex = null;
            $sy2ColumnIndex = null;
            $esColumnIndex = null;
            $headerRow = $limpio[0] ?? [];

            foreach ($headerRow as $index => $header) {
                if (strtoupper($header) == 'EN') {
                    $enColumnIndex = $index;
                }
                if (strtoupper($header) == 'SYM') {
                    $symColumnIndex = $index;
                }
                if (strtoupper($header) == 'SY2') {
                    $sy2ColumnIndex = $index;
                }
                if (strtoupper($header) == 'ES') {
                    $esColumnIndex = $index;
                }
            }

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

            // Extraer los valores de las columnas "EN", "SYM", "SY2" y "ES" (saltando la fila de headers)
            $rowData = [];
            for ($row = 1; $row < count($limpio); $row++) {
                $enValue = $limpio[$row][$enColumnIndex] ?? null;
                $symValue = $limpio[$row][$symColumnIndex] ?? null;
                $sy2Value = $limpio[$row][$sy2ColumnIndex] ?? null;
                $esValue = $limpio[$row][$esColumnIndex] ?? null;

                if (!empty($enValue)) {
                    $rowData[] = [
                        'en' => trim($enValue),
                        'sym' => trim($symValue ?? ''),
                        'sy2' => trim($sy2Value ?? ''),
                        'es' => trim($esValue ?? '')
                    ];
                }
            }

            // Construir las URLs de las imágenes y crear los objetos finales
            $items = [];
            $baseUrl = 'https://printables.happycapibara.com/color-books/chinese/';
            // $baseUrl = 'http://localhost:8000/printables/chinese/';
            foreach ($rowData as $row) {
                $enValue = $row['en'];
                $symValue = $row['sym'];
                $sy2Value = $row['sy2'];
                $esValue = $row['es'];
                $enValue = preg_replace('/[^a-zA-Z]/', '_', $enValue);
                $imageUrl = $baseUrl . strtolower($enValue) . '.png';

                // Verificar si la imagen existe (opcional)
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
                    // Opcional: incluir imágenes que no existen con placeholder
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
            $layoutA4 = [
                'width' => 21,
                'height' => 29.7,
                'margin' => 0,
                'margin-in' => 0,
                'margin-out' => 0,
                'verso' => [
                    'margin' => 0.5,
                    'border-margin' => 0.25,
                    'image-margin' => 0.25,
                    'text-y-percentage' => 0.6,
                    'text-margin' => 0.3,
                    'primary-font-size' => 2.1,
                    'secondary-font-size' => 1.5,
                ],
                'recto' => [
                    'margin' => 0.5,
                    'image-margin' => 0.25,
                    'font-size' => 8,
                    'text-y-percentage' => 0.5,
                ]
            ];
            $layoutLetter = [
                'width' => 21.59,
                'height' => 27.94,
                'margin' => 1,
                'margin-in' => 0.95,
                'margin-out' => 0.64,
                'verso' => [
                    'margin' => 0.5,
                    'border-margin' => 0.25,
                    'image-margin' => 0.25,
                    'text-y-percentage' => 0.75,
                    'text-margin' => 0.3,
                    'primary-font-size' => 2.1,
                    'secondary-font-size' => 1.5,
                ],
                'recto' => [
                    'margin' => 0.5,
                    'image-margin' => 0.25,
                    'font-size' => 8,
                    'text-y-percentage' => 0.53,
                ]
            ];

            $data = [
                'items' => $items,
                'title' => 'Color Book PDF',
                'totalWords' => count($rowData),
                'foundImages' => count(array_filter($items, function ($item) {
                    return !isset($item['image']['missing']);
                })),
                'layout' => $layoutLetter
            ];


            $pdf = Pdf::loadView('pdf.colorbook_margin', $data)
                ->setPaper([0, 0, $data["layout"]["width"] / 2.54 * 72, $data["layout"]["height"] / 2.54 * 72]);


            return $pdf->download('color-book-' . date('Y-m-d-H-i-s') . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
