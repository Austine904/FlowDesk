<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    public function generateFromHtml(string $html, string $filename = 'document.pdf', bool $download = true): void
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', FCPATH);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        if ($download) {
            $dompdf->stream($filename, ['Attachment' => true]);
        } else {
            $dompdf->stream($filename, ['Attachment' => false]);
        }
    }

    public function generateFromView(string $view, array $data = [], string $filename = 'document.pdf', bool $download = true): void
    {
        $html = view($view, $data);
        $this->generateFromHtml($html, $filename, $download);
    }

    /**
     * Generate PDF and return content as string (for email attachment)
     */
    public function generateFromViewOutput(string $view, array $data = []): string
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', FCPATH);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view($view, $data));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
