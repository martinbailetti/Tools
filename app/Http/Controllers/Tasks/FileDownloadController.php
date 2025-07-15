<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class FileDownloadController extends Controller
{


    public function download(Request $request)
    {
        $filePath = public_path($request->file);

        if (!file_exists($filePath)) {
            abort(404, "El archivo no existe.");
        }

        return response()->download($filePath);
    }
}
