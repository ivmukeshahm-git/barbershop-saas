<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>Rosenthal Barbershop Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }

        .invoice-container {
            max-width: 800px;
            margin: 11px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 20px 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table {
            border-bottom: 2px solid #bc1e2d;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
            padding: 5px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #bc1e2d;
            margin: 0;
        }

        .customer-invoice {
            margin: 20px 0;
        }

        .customer-invoice td {
            padding: 5px 0;
            font-size: 14px;
        }

        .invoice-table thead tr {
            background: #bc1e2d;
            color: #fff;
        }

        .invoice-table th,
        .invoice-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .totals td {
            padding: 8px;
            font-size: 14px;
        }

        .totals tr td:first-child {
            text-align: left;
        }

        .totals tr td:last-child {
            text-align: right;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 2px solid #bc1e2d;
            font-size: 12px;
            color: #555;
            margin-top: 20px;
        }

        .text-right {
            text-align: right;
        }

        @media print {
            body {
                background: #fff;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container" id="invoice">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    <img src="{{ url('/assets/img/logo.png') }}" style="width: 60px;"
                        alt="Rosenthal Barbershop Logo" />
                </td>
                <td style="width: 50%; text-align: right;">
                    <h2 class="company-name">Rosenthal Barbershop</h2>
                    {{ $siteSetting->contact_email ?? 'info@bluecrownbarbers.com' }}<br />
                    {{ $siteSetting->contact_phone ?? '+1 (587) 555-2489' }}<br />
                    <a href="{{ $siteSetting->website_url ?? 'https://www.bluecrownbarbers.com' }}" target="_blank"
                        style="color:#bc1e2d; text-decoration:none;">{{ optional($siteSetting)->websiteHost() ?: 'www.bluecrownbarbers.com' }}</a>
                </td>
            </tr>
        </table>

        @php
            $invoice = $invoices->first();
            $customerName = $invoice->customer->customer_name ?? null;
            $customerEmail = $invoice->customer->customer_email ?? null;
            $customerPhone = $invoice->customer->customer_phone ?? null;
            $invoice_number = $invoice->invoice_number ?? null;
            $CreateDate = $invoice->updated_at ?? null;
            $taxType = strtolower((string) ($invoice->tax_type ?? 'include'));

            // Totals
            $serviceSubtotal = $invoice->services->sum('total_amt');
            $serviceDiscount =
                $invoice->service_discount > 0 ? ($serviceSubtotal * $invoice->service_discount) / 100 : 0;
            $serviceSubtotalWithDiscount = $serviceSubtotal - $serviceDiscount;

            $productSubtotal = $invoice->products->sum('amount');
            $productDiscount =
                $invoice->product_discount > 0 ? ($productSubtotal * $invoice->product_discount) / 100 : 0;
            $productSubtotalWithDiscount = $productSubtotal - $productDiscount;

            $total_amt = $invoice->total_amt;
            $subtotal = $serviceSubtotalWithDiscount + $productSubtotalWithDiscount;
            if ($taxType !== 'exclude') {
                $subtotal = max(0, $subtotal - (float) $invoice->total_tax);
            }
        @endphp

        <!-- Customer & Invoice Info -->
        <table class="customer-invoice">
            <tr>
                <td style="width: 60%;">
                    <strong>Customer:</strong><br />
                    {{ $customerName }}<br />
                    @if ($customerName !== 'Guest Customer')
                        {{ $customerEmail }}<br />
                        {{ $customerPhone }}
                    @endif
                </td>
                <td style="width: 40%; text-align: right;">
                    <strong>Invoice No:</strong> {{ $invoice_number }}<br />
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($CreateDate)->format('d/m/Y') }}
                    @if (optional($siteSetting)->gstin)
                        <br><strong>GSTIN:</strong> {{ optional($siteSetting)->gstin }}
                    @endif
                    @if (optional($siteSetting)->sac_code)
                        <br><strong>SAC:</strong> {{ optional($siteSetting)->sac_code }}
                    @endif
                </td>
            </tr>
        </table>

        <!-- Service & Product Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                {{-- Services --}}
                @foreach ($invoice->services as $service)
                    <tr>
                        <td>Service #{{ $service->service->service_name }}@if (optional($siteSetting)->sac_code) <small>(SAC: {{ optional($siteSetting)->sac_code }})</small>@endif</td>
                        <td class="text-center">1</td>
                        <td class="text-center">$ {{ number_format($service->total_amt, 2) }}</td>
                        <td class="text-right">$ {{ number_format($service->total_amt, 2) }}</td>
                    </tr>
                @endforeach
                @if ($serviceSubtotal)
                    <tr style="color: red">
                        <td colspan="3">Total Services Amount</td>
                        <td class="text-right">$ {{ number_format($serviceSubtotal, 2) }}</td>
                    </tr>
                @endif
                @if ($serviceDiscount)
                    <tr style="color: red">
                        <td colspan="3">Service Discount</td>
                        <td class="text-right">($ {{ number_format($serviceDiscount, 2) }})
                            {{ $invoice->service_discount }}%</td>
                    </tr>
                @endif
                @if ($serviceSubtotalWithDiscount)
                    <tr style="color: red">
                        <td colspan="3">Total Services Payable</td>
                        <td class="text-right">$ {{ number_format($serviceSubtotalWithDiscount, 2) }}</td>
                    </tr>
                @endif

                {{-- Products --}}
                @foreach ($invoice->products as $product)
                   <tr>
                        <td>Product #{{ $product->product->product_name }}</td>
                        <td class="text-center">1</td>
                        <td class="text-center">$ {{ number_format($product->amount, 2) }}</td>
                        <td class="text-right">$ {{ number_format($product->amount, 2) }}</td>
                    </tr>
                @endforeach 
                @if ($productSubtotal)
                    <tr style="color: red">
                        <td colspan="3">Total Product Amount</td>
                        <td class="text-right">$ {{ number_format($productSubtotal, 2) }}</td>
                    </tr>
                @endif
                @if ($productDiscount)
                    <tr style="color: red">
                        <td colspan="3">Product Discount</td>
                        <td class="text-right">($ {{ number_format($productDiscount, 2) }})
                            {{ $invoice->product_discount }}%</td>
                    </tr>
                @endif
                @if ($productSubtotalWithDiscount)
                    <tr style="color: red">
                        <td colspan="3">Total Product Payable</td>
                        <td class="text-right">$ {{ number_format($productSubtotalWithDiscount, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Totals -->
        <table class="totals" style="width:100%; border-collapse: collapse;">
            <tr>
                <td style="border-bottom: 1px solid #ddd; padding: 8px;"><strong>Subtotal:</strong></td>
                <td class="text-right" style="border-bottom: 1px solid #ddd; padding: 8px;">
                    $ {{ number_format($subtotal, 2) }}
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #ddd; padding: 8px;"><strong>GST ({{ $taxType === 'exclude' ? 'excluded' : 'included' }}{{ $invoice->tax_rate ? ', ' . number_format($invoice->tax_rate, 2) . '%' : '' }}):</strong></td>
                <td class="text-right" style="border-bottom: 1px solid #ddd; padding: 8px;">
                    $ {{ number_format($invoice->total_tax, 2) }}
                </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #ddd; padding: 8px;"><strong>Tip:</strong></td>
                <td class="text-right" style="border-bottom: 1px solid #ddd; padding: 8px;">
                    $ {{ number_format($invoice->total_tip, 2) }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px;"><strong>Total Amount:</strong></td>
                <td class="text-right" style="padding: 8px;">
                    <strong>$ {{ number_format($total_amt, 2) }}</strong>
                </td>
            </tr>
        </table>



        <!-- Footer -->
        <div class="footer">
            Rosenthal Barbershop – For Style &amp; Elegance<br />
            Need help? Contact us at <a href="mailto:{{ $siteSetting->contact_email ?? 'info@bluecrownbarbers.com' }}"
                style="color:#bc1e2d;">{{ $siteSetting->contact_email ?? 'info@bluecrownbarbers.com' }}</a>
        </div>
    </div>
</body>

</html>
