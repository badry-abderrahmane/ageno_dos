<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PdfInvoiceService
{
    /**
     * Stream the PDF directly to the browser.
     */
    public function download(Invoice $invoice, $type)
    {
        $filename = "{$type}-{$invoice->id}-" . date('Y') . ".pdf";

        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->SetAuthor('Ageno - ' . $invoice->user->organization->name);
        $pdf->SetTitle($filename);
        $pdf->SetSubject($invoice->user->organization->name);
        $pdf->SetKeywords($invoice->user->organization->name);

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        // $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);


        $pdf->AddPage();

        $view = 'pdf.quote';

        switch ($type) {
            case 'quote':
                $view = 'pdf.quote';
                break;
            case 'delivery':
                $view = 'pdf.delivery';
                break;
            case 'invoice':
                $view = 'pdf.invoice';
                break;
        }

        $html = view($view, [
            'invoice' => $invoice,
            'date' => Carbon::now()->format('d/m/Y'),
            'organization' => Auth::user()->organization
        ])->render();

        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output($filename, 'I');
    }
}
