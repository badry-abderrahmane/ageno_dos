<?php

namespace App\Services;

use App\Models\Invoice;
use Elibyy\TCPDF\Facades\TCPDF as PDF;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PdfInvoiceService
{
    /**
     * Generate and save an invoice PDF.
     */
    public function generate(Invoice $invoice): string
    {
        $pdf = new PDF;
        $pdf::SetTitle('Facture #' . $invoice->id);
        $pdf::SetMargins(10, 20, 10);
        $pdf::SetAutoPageBreak(true, 15);
        $pdf::AddPage();

        // Load the Blade view
        $html = view('pdf.invoice', [
            'invoice' => $invoice,
            'date' => Carbon::now()->format('d/m/Y'),
        ])->render();

        $pdf::writeHTML($html, true, false, true, false, '');

        $fileName = 'invoices/facture_' . $invoice->id . '.pdf';
        Storage::disk('public')->put($fileName, $pdf::Output($fileName, 'S'));

        return Storage::disk('public')->path($fileName);
    }

    /**
     * Stream the PDF directly to the browser.
     */
    public function download(Invoice $invoice)
    {
        $pdf = new PDF;
        $pdf::SetTitle('Facture #' . $invoice->id);
        $pdf::AddPage();

        $html = view('pdf.invoice', [
            'invoice' => $invoice,
            'date' => Carbon::now()->format('d/m/Y'),
        ])->render();

        $pdf::writeHTML($html, true, false, true, false, '');

        return $pdf::Output('facture_' . $invoice->id . '.pdf', 'S');
    }
}
