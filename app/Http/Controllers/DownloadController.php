<?php

namespace App\Http\Controllers;

use setasign\Fpdi\Tcpdf\Fpdi;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    public function downloadPdf($file)
    {
        $user = Auth::user();
        $pin = $user->pin;

        $sourceFile = storage_path("app/files/" . $file);

        $pdf = new Fpdi();

        $pageCount = $pdf->setSourceFile($sourceFile);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $template = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($template);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($template);
        }

        // password dari PIN user
        $pdf->SetProtection([], $pin);

        return response($pdf->Output('memo.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="memo.pdf"');
    }
}