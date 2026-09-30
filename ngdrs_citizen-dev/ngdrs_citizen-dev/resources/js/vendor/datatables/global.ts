import DataTable from "datatables.net-dt";

const tableEl = document.querySelector("#table");

if (tableEl) {
    new DataTable(tableEl, {
        pageLength: 10,
        searching: true,
        ordering: true,
        info: true,
        columnDefs: [
            {
                targets: '_all',
                className: 'dt-left'
            }
        ]
    });
}
