<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceService
{
    /**
     * Generate invoice PDF for order
     */
    public function generateInvoice(Order $order): string
    {
        // Generate QR code for order
        $qrCodeData = json_encode([
            'order_number' => $order->order_number,
            'identity_number' => $order->consumer_identity_number,
            'total_amount' => $order->total_amount,
        ]);

        $qrCode = base64_encode(QrCode::format('png')
            ->size(200)
            ->margin(1)
            ->generate($qrCodeData));

        // Render PDF
        $pdf = Pdf::loadView('pdf.invoice', compact('order', 'qrCode'));

        // Use A4 paper and Portrait orientation
        $pdf->setPaper('a4', 'portrait');

        // Save PDF
        $filename = "invoice_{$order->order_number}.pdf";
        $path = "invoices/{$filename}";

        Storage::disk('public')->put($path, $pdf->output());

        // Update order with invoice path
        $order->update(['invoice_path' => $path]);

        Log::info('Invoice generated using dompdf', [
            'order_number' => $order->order_number,
            'path' => $path,
        ]);

        return $path;
    }

    /**
     * Get invoice URL
     */
    public function getInvoiceUrl(Order $order): ?string
    {
        if (! $order->invoice_path) {
            return null;
        }

        return Storage::disk('public')->url($order->invoice_path);
    }

    /**
     * Download or Stream invoice
     */
    public function downloadInvoice(Order $order)
    {
        if (! $order->invoice_path || ! Storage::disk('public')->exists($order->invoice_path)) {
            // Generate if not exists
            $this->generateInvoice($order);
        }

        return Storage::disk('public')->download(
            $order->invoice_path,
            "Invoice_{$order->order_number}.pdf"
        );
    }
}
