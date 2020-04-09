jQuery( document ).ready(function($) {
    $('.sales-by-customer').DataTable({
        dom: 'Bfrtip',
        "pageLength": 200,
        "pagingType": "full_numbers",
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
        ]
    });
});
