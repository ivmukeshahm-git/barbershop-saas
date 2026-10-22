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
                            <a href="{{ route('admin.invoice.create') }}" class="btn btn-sm btn-primary"><i
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
                                <td>{{ $invoice->customer->customer_name ?? '-' }}</td>
                                <td>{{ $invoice->staff_names ?? '-' }}</td>
                                <td>{{ $invoice->updated_at }}</td>

                                <td>Paid</td>
                                <td>{{ $invoice->total_amt }}</td>
                                {{--                        <td>2</td> --}}
                                {{--                        <td>2</td> --}}
                                <td>
                                    @can('Pos_invoice-Edit')
                                        <a class="btn btn-sm btn-warning p-2 mx-2"
                                            href="{{ route('admin.invoice.edit', $invoice->appointment_id) }}"><i
                                                class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                    @endcan
                                    <a class="btn btn-sm btn-danger p-2" href="javascript:void(0)"
                                        onclick="viewInvoice({{ $invoice }})"><i class="fa fa-file"
                                            aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
   
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>


