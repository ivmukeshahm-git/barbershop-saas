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
    </style>

@endpush


@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Invoices";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Product List Table -->
        <div class="card">
            <div class="card-header p-4">
                <div class="row">
                    <div class="col-lg-6">
                        <h5 class="card-title">Invoice</h5>
                    </div>
                      @can('Pos_invoice-Add')
                    <div class="col-lg-6" style="text-align: right;">
                        <a href="{{route('admin.invoice.create')}}" class="btn btn-sm btn-primary"><i
                                class='bx bx-plus'></i> Add Invoice</a>
                    </div>
                     @endcan
                </div>
            </div>
            <hr class="my-1">
            <div class="card-body table-responsive p-4">
                <table id="InvoiceDatatable" class="table table-sm table-striped table-hover">
                    <thead class="border-top">
                    <tr>
                        <th>Sr. NO.</th>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Staff</th>
                        <th>Date</th>
                        {{--                        <th>Payment</th>--}}
                        <th>Status</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoices as $invoice)
{{--                        @dd($invoice)--}}
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$invoice->invoice_number}}</td>
                            <td>{{$invoice->customer->customer_name??"-"}}</td>
                            <td>{{$invoice->staff_names??"-"}}</td>
                            <td>{{$invoice->updated_at}}</td>

                            <td>Paid</td>
                            <td>{{$invoice->total_amt}}</td>
                            {{--                        <td>2</td>--}}
                            {{--                        <td>2</td>--}}
                            <td>
                                 @can('Pos_invoice-Edit')
                                <a class="btn btn-sm btn-warning p-2 mx-2" href="{{route('admin.invoice.edit',$invoice->appointment_id)}}"><i class="fa fa-pencil-square-o"
                                                                                   aria-hidden="true"></i></a>
                                                                                    @endcan
                                <a class="btn btn-sm btn-danger p-2" href="javascript:void(0)" onclick="viewInvoice({{$invoice}})"><i
                                        class="fa fa-file" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header text-white">
                    <h5 class="modal-title" id="addAppointmentLabel">Add Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
{{--                    <div class="invoice-container">--}}
{{--                        <!-- Header -->--}}
{{--                        <div class="header">--}}
{{--                            <img src="{{ asset('assets/img/logo.png') }}" alt="Rosenthal Barbershop Logo">--}}
{{--                            <div class="company-details">--}}
{{--                                <h2>Rosenthal Barbershop</h2>--}}
{{--                                info@bluecrownbarbers.com<br>--}}
{{--                                +91 1234567890<br>--}}
{{--                                www.bluecrownbarbers.com--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <!-- Customer & Invoice Info -->--}}
{{--                        <div class="customer-details">--}}
{{--                            <div>--}}
{{--                                <strong>Customer:</strong><br>--}}
{{--                                Mukesh Suthar<br>--}}
{{--                                mukeshsthr690@gmail.com<br>--}}
{{--                                7296930519--}}
{{--                            </div>--}}
{{--                            <div class="invoice-info">--}}
{{--                                <strong>Invoice No:</strong> GEN-12345<br>--}}
{{--                                <strong>Date:</strong> 11 Sep 2025--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <!-- Service Table -->--}}
{{--                        <table class="invoice-table">--}}
{{--                            <thead>--}}
{{--                            <tr>--}}
{{--                                <th>Service Name</th>--}}
{{--                                <th class="text-center">Qty</th>--}}
{{--                                <th class="text-center">Price</th>--}}
{{--                                <th class="text-center">Discount</th>--}}
{{--                                <th class="text-center">Tax</th>--}}
{{--                                <th class="text-center">Total</th>--}}
{{--                            </tr>--}}
{{--                            </thead>--}}
{{--                            <tbody>--}}
{{--                            <tr>--}}
{{--                                <td>Haircut & Styling</td>--}}
{{--                                <td class="text-center">1</td>--}}
{{--                                <td class="text-center">₹ 500.00</td>--}}
{{--                                <td class="text-center">₹ 50.00</td>--}}
{{--                                <td class="text-center">₹ 45.00</td>--}}
{{--                                <td class="text-center">₹ 495.00</td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td>Shampoo</td>--}}
{{--                                <td class="text-center">2</td>--}}
{{--                                <td class="text-center">₹ 150.00</td>--}}
{{--                                <td class="text-center">₹ 0.00</td>--}}
{{--                                <td class="text-center">₹ 27.00</td>--}}
{{--                                <td class="text-center">₹ 327.00</td>--}}
{{--                            </tr>--}}
{{--                            </tbody>--}}
{{--                        </table>--}}

{{--                        <!-- Totals -->--}}
{{--                        <table class="totals">--}}
{{--                            <tr>--}}
{{--                                <td><strong>Subtotal:</strong></td>--}}
{{--                                <td>₹ 822.00</td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td><strong>Total Discount:</strong></td>--}}
{{--                                <td>-₹ 50.00</td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td><strong>Tax:</strong></td>--}}
{{--                                <td>₹ 72.00</td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td><strong>Tip:</strong></td>--}}
{{--                                <td>₹ 10.00</td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td><strong>Total Amount:</strong></td>--}}
{{--                                <td><strong>₹ 854.00</strong></td>--}}
{{--                            </tr>--}}
{{--                        </table>--}}

{{--                        <!-- Footer -->--}}
{{--                        <div class="footer">--}}
{{--                            Rosenthal Barbershop – For Style & Elegance<br>--}}
{{--                            Need help? Contact us at info@bluecrownbarbers.com--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <div class="invoice-container" id="invoice">
                        <!-- Header -->
                        <div class="header">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="Rosenthal Barbershop Logo">
                            <div class="company-details">
                                <h2>Rosenthal Barbershop</h2>
                                info@bluecrownbarbers.com<br>
                                (555) 123-4567<br>
                                www.bluecrownbarbers.com
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
                                <th class="text-center">Discount</th>
                                <th class="text-center">Tax</th>
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
                            Need help? Contact us at info@bluecrownbarbers.com
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" href="javascript:void(0)" class="btn btn-success export-pdf" onclick="generatePDF()">Export</button>
                    <button type="button" href="javascript:void(0)" class="btn btn-primary" onclick="printInvoice()">Print</button>
{{--                    <button type="submit" form="appointmentForm" class="btn btn-primary sav-appointment">Save Appointment</button>--}}
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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

        .invoice-table th, table td {
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
.page-break{
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

    function viewInvoice(invoice) {
        // console.log(JSON.parse(invoice));
        var appointment =invoice;

        document.getElementById("customer-details").innerHTML="";
        document.getElementById("customer-details").innerHTML = `
    <strong>Customer:</strong><br>
    ${appointment.customer.customer_name}<br>
    ${appointment.customer.customer_email}<br>
    ${appointment.customer.customer_phone}
`;
        document.getElementById("invoice-info").innerHTML ="";

        document.getElementById("invoice-info").innerHTML = `
    <strong>Invoice No:</strong> ${appointment.invoice_number}<br>
    <strong>Date:</strong> ${new Date(appointment.updated_at).toLocaleDateString()}
`;

// ✅ Render services
        document.getElementById("service-rows").innerHTML="";
        const serviceRows = document.getElementById("service-rows");
        let subtotal=0;
        appointment.services.forEach(service => {
            subtotal= parseFloat(subtotal)+parseFloat(service.total_amt);
            serviceRows.innerHTML += `
        <tr>
            <td>Service #${service.service.service_name}</td>
            <td class="text-center">1</td>
            <td class="text-center">$ ${parseFloat(service.total_amt).toFixed(2)}</td>
            <td class="text-center">$ 0.00</td>
            <td class="text-center">$ 0.00</td>
            <td class="text-center">$ ${parseFloat(service.total_amt).toFixed(2)}</td>
        </tr>
    `;
        });

        appointment.products.forEach(product => {
            subtotal= parseFloat(subtotal)+parseFloat(product.amount);
            serviceRows.innerHTML += `
        <tr>
            <td>Product #${product.product.product_name}</td>
            <td class="text-center">1</td>
            <td class="text-center">$ ${parseFloat(product.amount).toFixed(2)}</td>
            <td class="text-center">$ 0.00</td>
            <td class="text-center">$ 0.00</td>
            <td class="text-center">$ ${parseFloat(product.amount).toFixed(2)}</td>
        </tr>
    `;
        });

// ✅ Render totals
        const totals = document.getElementById("totals");
        const grandTotal = appointment.total_amt + appointment.total_tax + appointment.total_tip - appointment.total_discount;

        totals.innerHTML = `
    <tr><td><strong>Subtotal:</strong></td><td>$ ${subtotal.toFixed(2)}</td></tr>
    <tr><td><strong>Total Discount:</strong></td><td>-$ ${appointment.total_discount.toFixed(2)}</td></tr>
    <tr><td><strong>Tax:</strong></td><td>$ ${appointment.total_tax.toFixed(2)}</td></tr>
    <tr><td><strong>Tip:</strong></td><td>$ ${appointment.total_tip.toFixed(2)}</td></tr>
    <tr><td><strong>Total Amount:</strong></td><td><strong>$ ${appointment.total_amt.toFixed(2)}</strong></td></tr>
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
        printWindow.onload = function () {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    }


    function generatePDF() {
        var oldHtml= $('.export-pdf').text();
        $('.export-pdf').prop('disabled',true).html(`<span class="spinner-border spinner-border-sm me-2"></span>Proccessing..`)
        const element = document.getElementById('invoice');

        const opt = {
            // 0,
            filename:     'Rosenthal Barbershop-invoice.pdf',
            image:        { type: 'jpeg', quality: 1 },
            html2canvas:  { scale: 2, useCORS: true, scrollY: 0 },
            pagebreak:    { mode: ['css', 'legacy'] },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
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
