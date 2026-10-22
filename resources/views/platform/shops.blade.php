@extends('platform.layout')

@section('title', 'Shop accounts')

@section('content')
<div class="pa-page-head">
    <div><p class="pa-eyebrow">Tenant management</p><h1>Shop accounts</h1><p>Search accounts, review subscription dates, and manage shop access.</p></div>
</div>
<div class="pa-toolbar">
    <label for="paShopStatus" class="pa-nav-label" style="margin:0">Account status</label>
    <select id="paShopStatus" class="pa-filter-select" aria-label="Filter shop accounts by status">
        <option value="">All statuses</option>
        @foreach ($statuses as $status)
            <option value="{{ $status }}">{{ ucfirst($status) }}</option>
        @endforeach
    </select>
</div>
<section class="pa-panel">
    <div class="pa-table-wrap">
        <table id="paShopsTable" class="pa-table">
            <thead>
                <tr>
                    <th>Shop / owner</th>
                    <th>Contact</th>
                    <th>Team</th>
                    <th>Plan</th>
                    <th>Plan start / expiry</th>
                    <th>Created</th>
                    <th>Account status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</section>

<dialog id="paPlanHistoryDialog" class="pa-dialog">
    <div class="pa-dialog-head">
        <div><p class="pa-eyebrow">Subscription timeline</p><h2 id="paPlanHistoryTitle">Plan history</h2></div>
        <button type="button" class="pa-mini-button" id="paPlanHistoryClose" aria-label="Close plan history"><i class="bi bi-x-lg"></i></button>
    </div>
    <div id="paPlanHistoryContent" class="pa-table-wrap">
        <p>Loading plan history…</p>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
    $(function () {
        var table = $('#paShopsTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            order: [[5, 'desc']],
            ajax: {
                url: @json(route('platform.shops.data')),
                data: function (data) {
                    data.status = $('#paShopStatus').val();
                }
            },
            columns: [
                { data: 'shop_owner', name: 'name', orderable: true, searchable: false },
                { data: 'contact', name: 'email', orderable: false, searchable: false },
                { data: 'team', name: 'users_count', orderable: false, searchable: false },
                { data: 'plan', name: 'plan', orderable: false, searchable: false },
                { data: 'plan_dates', name: 'plan_dates', orderable: false, searchable: false },
                { data: 'created', name: 'created_at', orderable: true, searchable: false },
                { data: 'status_control', name: 'status', orderable: true, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            createdRow: function (row) {
                $(row).children('td').each(function (index) {
                    var heading = $('#paShopsTable thead th').eq(index).text().trim();
                    $(this).attr('data-label', heading);
                });
            },
            language: { emptyTable: 'No shop accounts found.', processing: 'Loading shop accounts…' }
        });

        $('#paShopStatus').on('change', function () {
            table.ajax.reload();
        });

        $('#paShopsTable').on('click', '.js-plan-history', function () {
            var button = $(this);
            var dialog = document.getElementById('paPlanHistoryDialog');
            var title = document.getElementById('paPlanHistoryTitle');
            var content = document.getElementById('paPlanHistoryContent');
            title.textContent = button.data('shop') + ' plan history';
            content.innerHTML = '<p>Loading plan history…</p>';
            dialog.showModal();

            fetch(button.data('url'), { headers: { 'Accept': 'application/json' } })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Unable to load plan history.');
                    }
                    return response.json();
                })
                .then(function (payload) {
                    var table = document.createElement('table');
                    table.className = 'pa-table';
                    table.innerHTML = '<thead><tr><th>Plan</th><th>Amount</th><th>Cycle</th><th>Start date</th><th>Expiry date</th><th>Status</th></tr></thead>';
                    var body = document.createElement('tbody');

                    if (!payload.data.length) {
                        var empty = document.createElement('tr');
                        var emptyCell = document.createElement('td');
                        emptyCell.colSpan = 6;
                        emptyCell.textContent = 'No plan history for this shop.';
                        empty.appendChild(emptyCell);
                        body.appendChild(empty);
                    }

                    payload.data.forEach(function (item) {
                        var row = document.createElement('tr');
                        [item.plan, item.amount, item.cycle, item.start, item.end, item.status].forEach(function (value) {
                            var cell = document.createElement('td');
                            cell.textContent = value;
                            row.appendChild(cell);
                        });
                        body.appendChild(row);
                    });

                    table.appendChild(body);
                    content.replaceChildren(table);
                })
                .catch(function (error) {
                    content.textContent = error.message;
                });
        });

        $('#paPlanHistoryClose').on('click', function () {
            document.getElementById('paPlanHistoryDialog').close();
        });
    });
</script>
@endpush
