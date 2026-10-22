@push('css')
    <style>
        .job-type-select {
            border: 1px solid #e0e0e0;
            border-radius: 13px;
            box-shadow: 12px 12px 5px #eee;
            cursor: pointer;
        }

        .select-job {
            border: 1px solid #242a62;
        }

        #InvoiceDatatable th:last-child,
        #InvoiceDatatable td:last-child {
            min-width: 220px;
            width: 220px;
        }
    </style>
@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Invoices";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>Invoices</h4>
                <p>View, edit, and send customer invoices</p>
            </div>
        </div>

        <!-- Product List Table -->
        <div class="card rounded-3 shadow-sm">
            <div class="card-header p-4">
                <div class="row">
                    <div class="col-lg-6"></div>
                    @can('Pos_invoice-Add')
                        <div class="col-lg-6" style="text-align: right;">
                            <a href="{{ route('admin.invoice.create') }}" class="btn btn-sm btn-primary"><i
                                    class="bi bi-plus-lg"></i> Add Invoice</a>
                        </div>
                    @endcan
                </div>
            </div>
            <hr class="my-1">
            <div class="card-body table-responsive p-4">
                <table id="InvoiceDatatable" class="table table-hover">
                    <thead class="border-top">
                        <tr>
                            <th>Sr. NO.</th>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Staff</th>
                            <th>Date</th>
                            {{--                        <th>Payment</th> --}}
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $invoice)
                            {{--                        @dd($invoice) --}}
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>Customer</td>
                                <td>{{ $invoice->staff_names ?? '-' }}</td>
                                <td>{{ $invoice->updated_at }}</td>

                                <td>Paid</td>
                                <td>{{ shop_money($invoice->total_amt) }}</td>
                                {{--                        <td>2</td> --}}
                                {{--                        <td>2</td> --}}
                                <td>
                                    <div class="bc-actions">
                                        <a class="btn btn-sm btn-primary bc-btn-icon" href="javascript:void(0)"
                                            title="View Invoice"
                                            data-url="{{ route('admin.invoice.details', ['id' => $invoice->appointment_id]) }}"
                                            onclick="viewInvoice(this.dataset.url)">
                                            <i class="bi bi-eye" aria-hidden="true"></i>
                                        </a>
                                        @can('Pos_invoice-Edit')
                                            <a class="btn btn-sm btn-warning bc-btn-icon"
                                                href="{{ route('admin.invoice.edit', ['shop' => \App\Support\CurrentShop::slug(), 'id' => $invoice->appointment_id]) }}"
                                                title="Edit">
                                                <i class="bi bi-pencil-square" aria-hidden="true"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger bc-btn-icon void-invoice-btn"
                                                data-url="{{ route('admin.invoice.destroy', ['id' => $invoice->appointment_id]) }}"
                                                title="Void invoice">
                                                <i class="bi bi-archive" aria-hidden="true"></i>
                                            </button>
                                        @endcan
                                        <a class="btn btn-sm btn-success bc-btn-icon" href="javascript:void(0)"
                                            title="Send Invoice Mail"
                                            onclick="SendInvoiceMail({{ $invoice->appointment_id }})">
                                            <i class="bi bi-envelope" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if ($voidedInvoices->isNotEmpty())
            <div class="card rounded-3 shadow-sm mt-4">
                <div class="card-header p-4">
                    <h5 class="mb-0">Voided Invoices</h5>
                    <p class="text-muted mb-0">Voided invoices retain their history and can be restored.</p>
                </div>
                <div class="card-body table-responsive p-4">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($voidedInvoices as $voidedInvoice)
                                <tr>
                                    <td>{{ $voidedInvoice->invoice_number }}</td>
                                    <td>{{ $voidedInvoice->customer->customer_name ?? $voidedInvoice->name }}</td>
                                    <td>{{ $voidedInvoice->updated_at }}</td>
                                    <td>{{ shop_money($voidedInvoice->total_amt) }}</td>
                                    <td>
                                        @can('Pos_invoice-Edit')
                                            <button type="button" class="btn btn-sm btn-success restore-invoice-btn"
                                                data-url="{{ route('admin.invoice.restore', $voidedInvoice->appointment_id) }}">
                                                Restore
                                            </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
    <!-- / Content -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="addAppointmentLabel"><i class="bi bi-receipt me-2"></i>Invoice Preview</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    {{--                    <div class="invoice-container"> --}}
                    {{--                        <!-- Header --> --}}
                    {{--                        <div class="header"> --}}
                    {{--                            <img src="{{ ($shopLogo ?? (isset($currentShop) ? $currentShop->logoUrl() : asset('assets/img/logo.webp'))) }}" alt="Rosenthal Barbershop Logo"> --}}
                    {{--                            <div class="company-details"> --}}
                    {{--                                <h2>Rosenthal Barbershop</h2> --}}
                    {{--                                info@bluecrownbarbers.com<br> --}}
                    {{--                                +91 1234567890<br> --}}
                    {{--                                www.bluecrownbarbers.com --}}
                    {{--                            </div> --}}
                    {{--                        </div> --}}

                    {{--                        <!-- Customer & Invoice Info --> --}}
                    {{--                        <div class="customer-details"> --}}
                    {{--                            <div> --}}
                    {{--                                <strong>Customer:</strong><br> --}}
                    {{--                                Mukesh Suthar<br> --}}
                    {{--                                mukeshsthr690@gmail.com<br> --}}
                    {{--                                7296930519 --}}
                    {{--                            </div> --}}
                    {{--                            <div class="invoice-info"> --}}
                    {{--                                <strong>Invoice No:</strong> GEN-12345<br> --}}
                    {{--                                <strong>Date:</strong> 11 Sep 2025 --}}
                    {{--                            </div> --}}
                    {{--                        </div> --}}

                    {{--                        <!-- Service Table --> --}}
                    {{--                        <table class="invoice-table"> --}}
                    {{--                            <thead> --}}
                    {{--                            <tr> --}}
                    {{--                                <th>Service Name</th> --}}
                    {{--                                <th class="text-center">Qty</th> --}}
                    {{--                                <th class="text-center">Price</th> --}}
                    {{--                                <th class="text-center">Discount</th> --}}
                    {{--                                <th class="text-center">Tax</th> --}}
                    {{--                                <th class="text-center">Total</th> --}}
                    {{--                            </tr> --}}
                    {{--                            </thead> --}}
                    {{--                            <tbody> --}}
                    {{--                            <tr> --}}
                    {{--                                <td>Haircut & Styling</td> --}}
                    {{--                                <td class="text-center">1</td> --}}
                    {{--                                <td class="text-center">₹ 500.00</td> --}}
                    {{--                                <td class="text-center">₹ 50.00</td> --}}
                    {{--                                <td class="text-center">₹ 45.00</td> --}}
                    {{--                                <td class="text-center">₹ 495.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            <tr> --}}
                    {{--                                <td>Shampoo</td> --}}
                    {{--                                <td class="text-center">2</td> --}}
                    {{--                                <td class="text-center">₹ 150.00</td> --}}
                    {{--                                <td class="text-center">₹ 0.00</td> --}}
                    {{--                                <td class="text-center">₹ 27.00</td> --}}
                    {{--                                <td class="text-center">₹ 327.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            </tbody> --}}
                    {{--                        </table> --}}

                    {{--                        <!-- Totals --> --}}
                    {{--                        <table class="totals"> --}}
                    {{--                            <tr> --}}
                    {{--                                <td><strong>Subtotal:</strong></td> --}}
                    {{--                                <td>₹ 822.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            <tr> --}}
                    {{--                                <td><strong>Total Discount:</strong></td> --}}
                    {{--                                <td>-₹ 50.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            <tr> --}}
                    {{--                                <td><strong>Tax:</strong></td> --}}
                    {{--                                <td>₹ 72.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            <tr> --}}
                    {{--                                <td><strong>Tip:</strong></td> --}}
                    {{--                                <td>₹ 10.00</td> --}}
                    {{--                            </tr> --}}
                    {{--                            <tr> --}}
                    {{--                                <td><strong>Total Amount:</strong></td> --}}
                    {{--                                <td><strong>₹ 854.00</strong></td> --}}
                    {{--                            </tr> --}}
                    {{--                        </table> --}}

                    {{--                        <!-- Footer --> --}}
                    {{--                        <div class="footer"> --}}
                    {{--                            Rosenthal Barbershop – For Style & Elegance<br> --}}
                    {{--                            Need help? Contact us at info@bluecrownbarbers.com --}}
                    {{--                        </div> --}}
                    {{--                    </div> --}}
                    <div class="invoice-container" id="invoice">
                        <!-- Header -->
                        <div class="header">
                            <img src="{{ ($shopLogo ?? (isset($currentShop) ? $currentShop->logoUrl() : asset('assets/img/logo.webp'))) }}" alt="Rosenthal Barbershop Logo">
                            <div class="company-details">
                                <h2>Rosenthal Barbershop</h2>
                                {{ $siteSetting->contact_email ?? 'info@bluecrownbarbers.com' }}<br>
                                {{ $siteSetting->contact_phone ?? '+1 (587) 555-2489' }}<br>
                                {{ optional($siteSetting)->websiteHost() ?: 'www.bluecrownbarbers.com' }}
                            </div>
                        </div>

                        <!-- Customer & Invoice Info -->
                        <div class="customer-details">
                            <div id="customer-details"></div>
                            <div class="invoice-info" id="invoice-info"></div>
                        </div>

                        <!-- Service Table -->
                        <table class="invoice-table">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody id="service-rows"></tbody>
                        </table>

                        <!-- Totals -->
                        <table class="totals" id="totals"></table>

                        <!-- Footer -->
                        <div class="page-break"></div>
                        <div class="footer">
                            Rosenthal Barbershop – For Style & Elegance<br>
                            Need help? Contact us at {{ $siteSetting->contact_email ?? 'info@bluecrownbarbers.com' }}
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i> Close
                    </button>
                    <button type="button" class="btn btn-purple"
                        onclick="printInvoice()" title="Print">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                    <button type="button" class="btn btn-dark export-pdf"
                        onclick="generatePDF()" title="Download PDF">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                    </button>
                    {{--                    <button type="submit" form="appointmentForm" class="btn btn-primary sav-appointment">Save Appointment</button> --}}
                </div>

            </div>
        </div>
    </div>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        .invoice-container {
            max-width: 800px;
            margin: 11px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 20px 30px;
            color: #333;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #bc1e2d;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            width: 60px;
        }

        .header .company-details {
            text-align: right;
            font-size: 12px;
            font-weight: 600;
        }

        .header .company-details h2 {
            margin: 0;
            color: #bc1e2d;
            font-size: 14px;
        }

        .customer-details {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }

        .customer-details div {
            font-size: 14px;
        }

        .invoice-info {
            text-align: right;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .invoice-table thead tr {
            background: #bc1e2d;
            color: #fff;
        }

        .invoice-table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }

        .invoice-table td.text-center {
            text-align: center;
        }

        .invoice-table td.text-right {
            text-align: right;
        }

        .totals {
            margin-top: 10px;
            width: 100%;
        }

        .totals tr td {
            font-size: 14px;
            padding: 8px;
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
        }

        .page-break {
            page-break-before: auto;
        }

        @media print {
            body {
                background: #fff;
            }

            .invoice-container {
                box-shadow: none;
                margin: 0;
            }

            .invoice-table thead tr {
                background: #bc1e2d;
                color: #fff;
            }
        }
    </style>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    $(document).on('click', '.void-invoice-btn, .restore-invoice-btn', function() {
        const button = $(this);
        const restore = button.hasClass('restore-invoice-btn');
        Swal.fire({
            icon: 'warning',
            title: restore ? 'Restore invoice?' : 'Void invoice?',
            text: restore
                ? 'The invoice will return to the active list and any tracked inventory deduction will be applied again.'
                : 'The invoice will leave active reports. Any tracked product stock will be returned, and its history will remain available.',
            showCancelButton: true,
            confirmButtonText: restore ? 'Restore' : 'Void invoice'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: button.data('url'),
                type: restore ? 'POST' : 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') }
            }).done(function(response) {
                Swal.fire('Success', response.message, 'success').then(function() {
                    location.reload();
                });
            }).fail(function(xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'The invoice could not be updated.';
                Swal.fire('Action failed', message, 'error');
            });
        });
    });

function SendInvoiceMail(appointment_id) {
        Swal.fire({
            icon: 'question',
            title: 'Send invoice?',
            text: 'The invoice will be emailed to the customer.',
            showCancelButton: true,
            confirmButtonText: 'Send'
        }).then(function(result) {
            if (!result.isConfirmed) return;

        var data = {
            'appointment_id': appointment_id
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        LoaderShow()

        $.ajax({
            type: "POST",
            url: "{{ route('admin.mail.send-invoice-mail') }}",
            data: data,
            success: function(response) {
                LoaderHide()
                $("#PaymentMethod").modal('hide');
                if (response.status === 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    });

                    setTimeout(() => {
                        location.reload();
                    }, 1000);


                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            },
            error: function(xhr) {
                LoaderHide()
                const message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'The invoice email could not be sent.';
                Swal.fire({ icon: 'error', title: 'Email failed', text: message });
            }
        });
        });
    }
    
    
    var CURRENCY_SYMBOL = window.SHOP_CURRENCY_SYMBOL || '₹';
    function escapeInvoiceHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (character) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[character];
        });
    }

    function viewInvoice(url) {
        LoaderShow();
        $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function (appointment) {
                LoaderHide();
                renderInvoice(appointment);
            },
            error: function () {
                LoaderHide();
                Swal.fire({
                    icon: 'error',
                    title: 'Unable to load invoice',
                    text: 'Please refresh the page and try again.'
                });
            }
        });
    }

    function renderInvoice(appointment) {
        const invoiceCurrencySymbol = escapeInvoiceHtml(CURRENCY_SYMBOL);
        const customerName = escapeInvoiceHtml(appointment.customer_name || 'Customer');
        const customerEmail = escapeInvoiceHtml(appointment.customer_email || '');
        const customerPhone = escapeInvoiceHtml(appointment.customer_phone || '');

        document.getElementById("customer-details").innerHTML = "";
        document.getElementById("customer-details").innerHTML = `
    <strong>Customer:</strong><br>
    ${customerName}<br>
    ${customerName == "Guest Customer" ? '' : customerEmail}<br>
    ${customerName == "Guest Customer" ? '' : customerPhone}
`;
        document.getElementById("invoice-info").innerHTML = "";

        document.getElementById("invoice-info").innerHTML = `
    <strong>Invoice No:</strong> ${escapeInvoiceHtml(appointment.invoice_number)}<br>
    <strong>Date:</strong> ${new Date(appointment.updated_at).toLocaleDateString()}<br>
    ${appointment.gstin ? `<strong>GSTIN:</strong> ${escapeInvoiceHtml(appointment.gstin)}<br>` : ''}
    ${appointment.sac_code ? `<strong>SAC:</strong> ${escapeInvoiceHtml(appointment.sac_code)}` : ''}
`;

        // ✅ Render services
        document.getElementById("service-rows").innerHTML = "";
        const serviceRows = document.getElementById("service-rows");
        let subtotal = 0;
        let servicesubtotal = 0;
        (appointment.services || []).forEach(service => {
            // subtotal= parseFloat(subtotal)+parseFloat(service.total_amt);
            servicesubtotal = parseFloat(servicesubtotal) + parseFloat(service.total_amt);
            serviceRows.innerHTML += `
        <tr>
            <td>Service #${escapeInvoiceHtml(service.service_name)}</td>
            <td class="text-center">1</td>
            <td class="text-center">${invoiceCurrencySymbol}${parseFloat(service.total_amt).toFixed(2)}</td>
            <td class="text-right">${invoiceCurrencySymbol}${parseFloat(service.total_amt).toFixed(2)}</td>
        </tr>
    `;
        });

        let serviceSubtotalWithDiscount = servicesubtotal;
        let serviceDiscount = 0;

        if (parseInt(appointment.service_discount) > 0) {
            // console.log('dsdsd');
            serviceDiscount = servicesubtotal * appointment.service_discount / 100;
            serviceSubtotalWithDiscount = servicesubtotal - (servicesubtotal * appointment.service_discount / 100);
        }

        var service_html = ``;


        if(servicesubtotal){
service_html += `<tr style="color: red">
            <td colspan="3" >Total Services Amount</td>
            <td class="text-right">${invoiceCurrencySymbol}${parseFloat(servicesubtotal||0).toFixed(2)} </td>
        </tr>`;
        }


        if(serviceDiscount){
service_html += `<tr style="color: red">
            <td colspan="3" >Service Discount</td>
            <td class="text-right">(${invoiceCurrencySymbol}${parseFloat(serviceDiscount||0).toFixed(2)})${parseFloat(appointment.service_discount||0).toFixed(2)}% </td>
        </tr>`;
        }


        if(serviceSubtotalWithDiscount){
service_html += `<tr style="color: red">
            <td colspan="3">Total Services Payable</td>
            <td class="text-right">${invoiceCurrencySymbol}${parseFloat(serviceSubtotalWithDiscount).toFixed(2)}</td>
        </tr>`;
        }

        serviceRows.innerHTML += service_html;

        let productsubtotal = 0;

        (appointment.products || []).forEach(product => {
            productsubtotal = parseFloat(productsubtotal) + parseFloat(product.amount);
            serviceRows.innerHTML += `
        <tr>
            <td>Product #${escapeInvoiceHtml(product.product_name)}</td>
            <td class="text-center">1</td>
            <td class="text-center">${invoiceCurrencySymbol}${parseFloat(product.amount).toFixed(2)}</td>
            <td class="text-right">${invoiceCurrencySymbol}${parseFloat(product.amount).toFixed(2)}</td>
        </tr>
    `;
        });


        let productSubtotalWithDiscount = productsubtotal;
        let productDiscount = 0;
        if (parseInt(appointment.product_discount) > 0) {
            console.log('dsdsd');
            productDiscount = (productsubtotal * appointment.product_discount / 100);
            productSubtotalWithDiscount = productsubtotal - (productsubtotal * appointment.product_discount / 100);
        }
        let html = "";

        if (productsubtotal) {
            html += `
        <tr style="color: red">
            <td colspan="3">Total Product Amount</td>
            <td class="text-right">${invoiceCurrencySymbol}${parseFloat(productsubtotal || 0).toFixed(2)}</td>
        </tr>
    `;
        }

        if (productDiscount) {
            html += `
    <tr style="color: red">
        <td colspan="3">Product Discount</td>
        <td class="text-right">(${invoiceCurrencySymbol}${parseFloat(productDiscount || 0).toFixed(2)}) ${parseFloat(appointment.product_discount || 0).toFixed(2)}%</td>
    </tr>
`;
        }


        if (productSubtotalWithDiscount) {
            html += `<tr style="color: red">
        <td colspan="3">Total Product Payable</td>
        <td class="text-right">${invoiceCurrencySymbol}${parseFloat(productSubtotalWithDiscount).toFixed(2)}</td>
    </tr>`
        }



        serviceRows.innerHTML += html;

        // ✅ Render totals
        const totals = document.getElementById("totals");
        subtotal = productSubtotalWithDiscount + serviceSubtotalWithDiscount;
        if (String(appointment.tax_type || 'include').toLowerCase() !== 'exclude') {
            subtotal = Math.max(0, subtotal - parseFloat(appointment.total_tax || 0));
        }
        const taxLabel = String(appointment.tax_type || 'include').toLowerCase() === 'exclude'
            ? 'GST (excluded)'
            : 'GST (included)';
        totals.innerHTML = `
    <tr><td><strong>Subtotal:</strong></td><td class="text-right">${invoiceCurrencySymbol}${subtotal.toFixed(2)}</td></tr>
    <tr class="d-none"><td><strong>Total Discount:</strong></td><td>-${invoiceCurrencySymbol}${appointment.total_discount.toFixed(2)}</td></tr>
    <tr><td><strong>${taxLabel} ${appointment.tax_rate ? `(${parseFloat(appointment.tax_rate).toFixed(2)}%)` : ''}:</strong></td><td>${invoiceCurrencySymbol}${parseFloat(appointment.total_tax || 0).toFixed(2)}</td></tr>
    <tr><td><strong>Tip:</strong></td><td>${invoiceCurrencySymbol}${appointment.total_tip.toFixed(2)}</td></tr>
    <tr><td><strong>Total Amount:</strong></td><td><strong>${invoiceCurrencySymbol}${appointment.total_amt.toFixed(2)}</strong></td></tr>
`;

        $('#addAppointmentModal').modal('show');
    }

    function printInvoice() {
        var printContents = document.getElementById('invoice').innerHTML;

        // Open new window
        var printWindow = window.open('', '', 'height=800,width=1200');

        printWindow.document.write('<html><head><title>Invoice</title>');

        // Add all stylesheets from the main page
        document.querySelectorAll('link[rel="stylesheet"], style').forEach(function(node) {
            printWindow.document.write(node.outerHTML);
        });

        printWindow.document.write('</head><body>');
        printWindow.document.write(printContents);
        printWindow.document.write('</body></html>');

        printWindow.document.close();

        // Wait for content and styles to load before printing
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    }


    function generatePDF() {
        var oldHtml = $('.export-pdf').text();
        $('.export-pdf').prop('disabled', true).html(
            `<span class="spinner-border spinner-border-sm me-2"></span>Proccessing..`)
        const element = document.getElementById('invoice');

        const opt = {
            // 0,
            filename: 'Blue-Crown-Barbers-invoice.pdf',
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 2,
                useCORS: true,
                scrollY: 0
            },
            pagebreak: {
                mode: ['css', 'legacy']
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            }
        };

        html2pdf().set(opt).from(element).save().then(() => {
                // ✅ Only reset after PDF generation completes
                $('.export-pdf').prop('disabled', false).html(oldHtml);
            })
            .catch((err) => {
                console.error("PDF generation failed", err);
                $('.export-pdf').prop('disabled', false).html(oldHtml);
            });
        // $('.export-pdf').prop('disabled',false).html(oldHtml);
    }

    function closeInvoice() {
        // In a real implementation, this would close the modal/popup
        alert("Invoice closed. In a real application, this would close the popup.");
    }
</script>
