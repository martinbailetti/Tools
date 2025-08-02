<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GeneratorController extends Controller
{
    public function index()
    {
        return view('index');
    }




    public function generateColorBookPdf(Request $request)
    {
        ini_set('max_execution_time', '1500');
        set_time_limit(60 * 25);

        // Obtener los parámetros 'spreadsheetId' y 'sheetName' del request POST
        $spreadsheetId = $request->input('spreadsheetId');
        $sheetName = $request->input('sheetName');
        $numberOfPages = $request->input('numberOfPages', 0);
        $imagesURL = $request->input('imagesURL'); // Si es = se toman todas las páginas


        try {
            // Si no se proporciona archivo, buscar en storage/app/public

            // Descargar el archivo de Google Spreadsheet como Excel
            // Reemplaza 'SPREADSHEET_ID' y 'SHEET_NAME' según corresponda
         /*    $spreadsheetId = '1nYdFCcD5hLjPmz1xmddfNupjItjOI4riFzgpKX9Bq7k';
            $sheetName = 'Chino'; */
            $exportUrl = "https://docs.google.com/spreadsheets/d/$spreadsheetId/export?format=xlsx&sheet=$sheetName";

            // Descargar el archivo temporalmente
            $tempExcel = storage_path('app/temp_google_excel.xlsx');
            file_put_contents($tempExcel, file_get_contents($exportUrl));
            $excelFile = $tempExcel;


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
            $totalRows = count($limpio);
            if ($numberOfPages > 0) {
                $totalRows = $numberOfPages + 1; // +1 para incluir la fila de encabezados
            }

            for ($row = 1; $row < $totalRows; $row++) {
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
            // $baseUrl = 'http://localhost:8000/printables/chinese/';
            foreach ($rowData as $row) {
                $enValue = $row['en'];
                $symValue = $row['sym'];
                $sy2Value = $row['sy2'];
                $esValue = $row['es'];

                $enValue = str_replace(' ', '', $enValue);

                $enValue = preg_replace('/[^a-zA-Z]/', '_', $enValue);
                $imageUrl = $imagesURL . strtolower($enValue) . '.png';

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

            $layoutLetter = [
                'width' => 21.59,
                'height' => 27.94,
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
                ],
                'page1'=> [
                    'title-line-1' => 'El secreto',
                    'title-line-1-y-percentage' => 0.4,
                    'title-line-1-font-size' => 1.7,
                    'title-line-2' => 'de los',
                    'title-line-2-font-size' => 1,
                    'title-line-2-margin' =>  0.7,
                    'title-line-3' => 'Ideogramas Chinos',
                    'title-line-3-margin' => 0.15,
                    'title-line-3-font-size' => 1.7,
                    'logo-image' => 'https://printables.happycapibara.com/logo.png',
                    'logo-y-percentage' => 0.9,
                    'logo-height' => 3
                ],
                'page2'=> [
                    'text-y-percentage' => 0.3,
                    'text-y-space' => 0.3,
                    'text-font-size' => 0.3,
                    'margin-x' => 0.3,
                    'margin-bottom' => 0.2
                ],
                'page3'=> [
                    'image' => 'https://printables.happycapibara.com/color-books/birds.png',
                    'image-y-percentage' => 0.3,
                    'margin' => 0.2
                ],
                'page4'=> [
                    'image' => 'https://printables.happycapibara.com/color-books/chinese_landscape.png',

                ],
                'page5'=> [
                    'image' => 'https://printables.happycapibara.com/color-books/chinese_background.png',
                    'header-y' => 1.5,
                    'header-font-size' => 0.5,
                    'footer-y' => 24,
                    'footer-font-size' => 0.5,
                    'text-font-size' => 0.45,
                    'text-1-y' => 6,
                    'text-2-y' => 10.2,
                    'text-3-y' => 17.5,
                    'margin' => 1

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

            // Eliminar el archivo temporal de Excel
            if (file_exists($tempExcel)) {
                unlink($tempExcel);
            }

            return $pdf->download('color-book-' . date('Y-m-d-H-i-s') . '.pdf');
        } catch (\Exception $e) {
            // Asegurar que se elimina el archivo temporal incluso si hay error
            if (isset($tempExcel) && file_exists($tempExcel)) {
                unlink($tempExcel);
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
