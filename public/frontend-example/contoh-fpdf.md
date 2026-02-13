<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Codedge\Fpdf\Fpdf\Fpdf;

class InvoiceService
{
    /**
     * Generate invoice PDF for order
     */
    public function generateInvoice(Order $order): string
    {
        // Create PDF instance
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        // Add content
        $this->addHeader($pdf, $order);
        $this->addCustomerInfo($pdf, $order);
        $this->addEventInfo($pdf, $order);
        $this->addOrderItems($pdf, $order);
        $this->addQRCode($pdf, $order);
        $this->addFooter($pdf, $order);

        // Save PDF
        $filename = "invoice_{$order->order_number}.pdf";
        $path = "invoices/{$filename}";

        $pdfOutput = $pdf->Output('S'); // Output as string
        Storage::disk('public')->put($path, $pdfOutput);

        // Update order with invoice path
        $order->update(['invoice_path' => $path]);

        Log::info('Invoice generated', [
            'order_number' => $order->order_number,
            'path' => $path,
        ]);

        return $path;
    }

    /**
     * Add header to PDF
     */
    private function addHeader($pdf, Order $order)
    {
        // Logo (optional - add your logo path)
        $pdf->Image('logo.png', 90, 6, 30);

        // Add space between logo and company name
        $pdf->Ln(6);

        // Company name
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 10, 'INVOICE', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, 'Untix By Unovia Creative', 0, 1, 'C');
        // $pdf->Cell(0, 5, 'Address Line 1, City, Postal Code', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Phone: +62 821-4081-7545 | Email: unoviacreative@gmail.com', 0, 1, 'C');

        $pdf->Ln(5);

        // Line separator
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(8);

        // Invoice details
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(95, 6, 'Invoice Number:', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $order->order_number, 0, 1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(95, 6, 'Invoice Date:', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $order->created_at->format('d M Y, H:i'), 0, 1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(95, 6, 'Payment Date:', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $order->paid_at ? $order->paid_at->format('d M Y, H:i') : '-', 0, 1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(95, 6, 'Status:', 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(0, 6, 'PAID', 0, 1);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->Ln(5);
    }

    /**
     * Add customer information
     */
    private function addCustomerInfo($pdf, Order $order)
    {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, 'CUSTOMER INFORMATION', 0, 1, 'L', true);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 6, 'Name', 0, 0);
        $pdf->Cell(0, 6, ': ' . $order->customer->full_name, 0, 1);

        $pdf->Cell(50, 6, 'Email', 0, 0);
        $pdf->Cell(0, 6, ': ' . $order->customer->email, 0, 1);

        $pdf->Cell(50, 6, 'Phone', 0, 0);
        $pdf->Cell(0, 6, ': ' . $order->customer->phone_number, 0, 1);

        $pdf->Cell(50, 6, 'Identity Type', 0, 0);
        $pdf->Cell(0, 6, ': ' . strtoupper($order->customer->identity_type), 0, 1);

        $pdf->Cell(50, 6, 'Identity Number', 0, 0);
        $pdf->Cell(0, 6, ': ' . $order->customer->identity_number, 0, 1);

        $pdf->Ln(5);
    }

    /**
     * Add event information
     */
    private function addEventInfo($pdf, Order $order)
    {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, 'EVENT INFORMATION', 0, 1, 'L', true);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 6, 'Event Name', 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, ': ' . $order->event->name, 0, 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 6, 'Date', 0, 0);
        $pdf->Cell(0, 6, ': ' . \Carbon\Carbon::parse($order->event->event_date)->format('d M Y, H:i') . ' WIB', 0, 1);

        $pdf->Cell(50, 6, 'Location', 0, 0);
        $pdf->MultiCell(0, 6, ': ' . ($order->event->venue ?? '-'));

        $pdf->Ln(5);
    }

    /**
     * Add order items table
     */
    private function addOrderItems($pdf, Order $order)
    {
        $pdf->SetFillColor(52, 58, 64);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);

        // Table header
        $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(85, 8, 'Ticket Type', 1, 0, 'L', true);
        $pdf->Cell(25, 8, 'Quantity', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Price', 1, 0, 'R', true);
        $pdf->Cell(35, 8, 'Subtotal', 1, 1, 'R', true);

        // Reset text color
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);

        // Table rows
        $no = 1;
        foreach ($order->orderItems as $item) {
            $pdf->Cell(10, 7, $no++, 1, 0, 'C');
            $pdf->Cell(85, 7, $item->ticketType->name, 1, 0, 'L');
            $pdf->Cell(25, 7, $item->quantity, 1, 0, 'C');
            $pdf->Cell(35, 7, 'Rp ' . number_format($item->price, 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell(35, 7, 'Rp ' . number_format($item->subtotal, 0, ',', '.'), 1, 1, 'R');
        }

        // Total
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(120, 8, '', 0, 0);
        $pdf->Cell(35, 8, 'TOTAL', 1, 0, 'L');
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(35, 8, 'Rp ' . number_format($order->total_amount, 0, ',', '.'), 1, 1, 'R');
        $pdf->SetTextColor(0, 0, 0);

        $pdf->Ln(8);
    }

    /**
     * Add QR code for wristband exchange
     */
    private function addQRCode($pdf, Order $order)
    {
        // Generate QR code
        $qrCodePath = $this->generateOrderQRCode($order);

        // Add QR code section
        $pdf->SetFillColor(255, 245, 230);
        $pdf->Rect(10, $pdf->GetY(), 190, 50, 'F');

        $startY = $pdf->GetY();

        // Add QR code image
        if ($qrCodePath && Storage::disk('public')->exists($qrCodePath)) {
            $fullPath = Storage::disk('public')->path($qrCodePath);
            $pdf->Image($fullPath, 15, $startY + 5, 40, 40);
        }

        // Add text
        $pdf->SetXY(60, $startY + 5);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 7, 'EXCHANGE THIS QR CODE FOR WRISTBAND', 0, 1);

        $pdf->SetX(60);
        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(
            135,
            5,
            "Scan this QR code at the event entrance to exchange for your wristband. " .
                "Please bring a valid ID matching the identity number on this invoice. " .
                "This QR code is unique and can only be used once."
        );

        $pdf->Ln(55);
    }

    /**
     * Generate QR code for order
     */
    private function generateOrderQRCode(Order $order): string
    {
        $qrData = json_encode([
            'order_number' => $order->order_number,
            'identity_number' => $order->customer->identity_number,
            'total_tickets' => $order->orderItems->sum('quantity'),
        ]);

        // Generate QR code using SimpleSoftwareIO\QrCode
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(400)
            ->margin(1)
            ->generate($qrData);

        $path = "qrcodes/order_{$order->order_number}.png";
        Storage::disk('public')->put($path, $qrCode);

        return $path;
    }

    /**
     * Add footer
     */
    private function addFooter($pdf, Order $order)
    {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, 'IMPORTANT NOTES:', 0, 1);

        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(
            0,
            4,
            "1. Please bring a valid ID (KTP/SIM/Passport) matching the identity number on this invoice\n" .
                "2. Present this invoice and scan the QR code at the registration desk to receive your wristband\n" .
                "3. The wristband is your ticket to enter the event venue\n" .
                "4. Wristband exchange is available from [TIME] to [TIME] on event day\n" .
                "5. This invoice is non-transferable and cannot be refunded\n" .
                "6. For questions, contact us at: support@example.com or +62 xxx-xxxx-xxxx"
        );

        $pdf->Ln(5);

        // Footer line
        $pdf->SetY(-20);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());

        $pdf->SetY(-15);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 5, 'Thank you for your purchase! See you at the event!', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Generated on ' . now()->format('d M Y H:i'), 0, 1, 'C');
    }

    /**
     * Get invoice URL
     */
    public function getInvoiceUrl(Order $order): ?string
    {
        if (!$order->invoice_path) {
            return null;
        }

        return Storage::disk('public')->url($order->invoice_path);
    }

    /**
     * Download invoice
     */
    public function downloadInvoice(Order $order)
    {
        if (!$order->invoice_path || !Storage::disk('public')->exists($order->invoice_path)) {
            // Generate if not exists
            $this->generateInvoice($order);
        }

        return Storage::disk('public')->download(
            $order->invoice_path,
            "Invoice_{$order->order_number}.pdf"
        );
    }
}
