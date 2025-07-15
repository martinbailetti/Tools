<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
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
}
