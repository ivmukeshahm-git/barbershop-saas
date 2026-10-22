@extends('platform.layout')

@section('title', 'Payments')

@section('content')
<div class="pa-page-head">
    <div><p class="pa-eyebrow">Subscription records</p><h1>Payments</h1><p>Payment and renewal data recorded against shop subscriptions.</p></div>
</div>
<div class="pa-notice"><i class="bi bi-info-circle"></i> These rows come from subscription records, including payment reference and method where supplied. A separate payment gateway transaction ledger is not configured in this app.</div>
<section class="pa-panel">
    <div class="pa-table-wrap">
        <table id="paPaymentsTable" class="pa-table">
            <thead><tr><th>Shop</th><th>Plan</th><th>Amount</th><th>Cycle</th><th>Payment method / reference</th><th>Paid</th><th>Subscription</th><th>Receipt</th></tr></thead>
            <tbody></tbody>
        </table>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#paPaymentsTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            order: [[5, 'desc']],
            ajax: @json(route('platform.payments.data')),
            columns: [
                { data: 'shop', name: 'shop_id', orderable: false, searchable: false },
                { data: 'plan_name', name: 'plan_id', orderable: false, searchable: false },
                { data: 'amount_display', name: 'amount', orderable: true, searchable: false },
                { data: 'cycle_display', name: 'billing_cycle', orderable: false, searchable: false },
                { data: 'payment_details', name: 'payment_reference', orderable: false, searchable: false },
                { data: 'paid_date', name: 'paid_at', orderable: true, searchable: false },
                { data: 'subscription_status', name: 'status', orderable: true, searchable: false },
                { data: 'receipt', name: 'receipt', orderable: false, searchable: false }
            ],
            createdRow: function (row) {
                $(row).children('td').each(function (index) {
                    var heading = $('#paPaymentsTable thead th').eq(index).text().trim();
                    $(this).attr('data-label', heading);
                });
            },
            language: { emptyTable: 'No subscription payment records yet.', processing: 'Loading payment records…' }
        });
    });
</script>
@endpush
