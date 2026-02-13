<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 40px;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }

        .header-top {
            width: 100%;
        }

        .brand {
            font-size: 24px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .invoice-title {
            text-align: right;
            font-size: 28px;
            font-weight: 900;
            color: #eee;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .info-grid {
            width: 100%;
            margin-bottom: 30px;
        }

        .info-box {
            vertical-align: top;
            width: 50%;
        }

        .info-label {
            font-size: 10px;
            font-weight: bold;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .section-title {
            background: #f8f9fa;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
            border-left: 4px solid #facc15;
            /* brand-yellow */
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table.items th {
            background: #333;
            color: #fff;
            text-align: left;
            padding: 10px;
            font-size: 11px;
            text-transform: uppercase;
        }

        table.items td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            width: 100%;
            margin-top: 20px;
        }

        .total-box {
            float: right;
            width: 250px;
        }

        .total-row {
            padding: 5px 0;
        }

        .total-row-label {
            display: inline-block;
            width: 120px;
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }

        .total-row-value {
            display: inline-block;
            width: 120px;
            text-align: right;
            font-weight: bold;
        }

        .grand-total {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
        }

        .grand-total .total-row-label {
            font-size: 14px;
            font-weight: 900;
            color: #1a1a1a;
        }

        .grand-total .total-row-value {
            font-size: 18px;
            font-weight: 900;
            color: #000;
        }

        .qr-section {
            margin-top: 50px;
            background: #fffbef;
            border: 1px solid #fef3c7;
            padding: 20px;
            border-radius: 10px;
        }

        .qr-code {
            float: left;
            margin-right: 20px;
        }

        .qr-info {
            overflow: hidden;
        }

        .qr-info h4 {
            margin: 0 0 10px 0;
            color: #92400e;
            font-size: 14px;
        }

        .qr-info p {
            margin: 0;
            font-size: 11px;
            color: #b45309;
            line-height: 1.4;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table class="header-top">
                <tr>
                    <td>
                        <div class="brand">TIKETIN</div>
                        <div style="font-size: 10px; color: #666;">by Unovia Creative</div>
                    </td>
                    <td class="invoice-title">INVOICE</td>
                </tr>
            </table>
        </div>

        <table class="info-grid">
            <tr>
                <td class="info-box">
                    <div class="info-label">Order Number</div>
                    <div class="info-value">{{ $order->order_number }}</div>
                    <div style="margin-top: 10px;">
                        <div class="info-label">Order Date</div>
                        <div class="info-value">{{ $order->created_at->format('d M Y, H:i') }} WIB</div>
                    </div>
                </td>
                <td class="info-box" style="text-align: right;">
                    <div class="info-label">Payment Status</div>
                    <div class="info-value" style="color: #059669;">PAID</div>
                    <div style="margin-top: 10px;">
                        <div class="info-label">Payment Method</div>
                        <div class="info-value">{{ strtoupper($order->payment_method ?? '-') }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="section-title">Customer Information</div>
        <table class="info-grid">
            <tr>
                <td class="info-box">
                    <div class="info-label">Name</div>
                    <div class="info-value">{{ $order->consumer_name }}</div>
                </td>
                <td class="info-box">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $order->consumer_email }}</div>
                </td>
            </tr>
            <tr>
                <td class="info-box" style="padding-top: 15px;">
                    <div class="info-label">WhatsApp</div>
                    <div class="info-value">{{ $order->consumer_whatsapp }}</div>
                </td>
                <td class="info-box" style="padding-top: 15px;">
                    <div class="info-label">Identity Number</div>
                    <div class="info-value">{{ $order->consumer_identity_number }}
                        ({{ strtoupper($order->consumer_identity_type) }})</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Event Information</div>
        <table class="info-grid">
            <tr>
                <td colspan="2">
                    <div class="info-label">Event Name</div>
                    <div class="info-value" style="font-size: 16px;">{{ $order->event->name }}</div>
                </td>
            </tr>
            <tr>
                <td class="info-box" style="padding-top: 15px;">
                    <div class="info-label">Date & Time</div>
                    <div class="info-value">{{ $order->event->event_date->format('d M Y, H:i') }} WIB</div>
                </td>
                <td class="info-box" style="padding-top: 15px;">
                    <div class="info-label">Location</div>
                    <div class="info-value">{{ $order->event->venue->name ?? '-' }}</div>
                </td>
            </tr>
        </table>

        <div class="section-title">Order Summary</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-right">Price</th>
                    <th class="text-right" style="width: 60px;">Qty</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderItems as $item)
                    <tr>
                        <td>
                            <div style="font-weight: bold;">{{ $item->ticketCategory->name }}</div>
                            <div style="font-size: 10px; color: #666;">Ticket ID: #{{ $item->id }}</div>
                        </td>
                        <td class="text-right">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-box">
                <div class="total-row">
                    <span class="total-row-label">Subtotal</span>
                    <span class="total-row-value">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if ($order->discount_amount > 0)
                    <div class="total-row" style="color: #059669;">
                        <span class="total-row-label">Discount</span>
                        <span class="total-row-value">- Rp
                            {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="total-row grand-total">
                    <span class="total-row-label">Total Amount</span>
                    <span class="total-row-value">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="qr-section">
            <div class="qr-code">
                <img src="data:image/png;base64,{{ $qrCode }}" width="100" height="100">
            </div>
            <div class="qr-info">
                <h4>EXCHANGE FOR WRISTBAND</h4>
                <p>Scan this QR code at the event entrance to exchange for your physical wristband. Please bring a valid
                    ID matching the details on this invoice.</p>
                <div style="margin-top: 10px; font-weight: bold; color: #92400e; font-size: 12px;">ORDER:
                    {{ $order->order_number }}</div>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer">
            <p>Thank you for your purchase! We hope you enjoy the event.</p>
            <p>Tiketin | unoviacreative@gmail.com | +62 821-4081-7545</p>
            <p style="margin-top: 10px;">Generated on {{ now()->format('d M Y, H:i') }} WIB</p>
        </div>
    </div>
</body>

</html>
