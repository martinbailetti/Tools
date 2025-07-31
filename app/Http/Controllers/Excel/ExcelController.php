<?php

namespace App\Http\Controllers\Excel;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function index()
    {

        $ruta = public_path('excel/test.xlsx');

        $hoja = Excel::toArray([], $ruta, null, \Maatwebsite\Excel\Excel::XLSX)[0];

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

        return response()->json($limpio);
    }
}
