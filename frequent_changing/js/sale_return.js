$(function () {
    "use strict";


    let base_url = $("#base_url_").val();
    let get_csrf_hash = $(".get_csrf_hash").val();


    let copy_db = $('#copy_db_exp').val();
    let print_db = $('#print_db_exp').val();
    let excel_db = $('#excel_db_exp').val();
    let csv_db = $('#csv_db_exp').val();
    let pdf_db = $('#pdf_db_exp').val();

    //get data using ajax datatable
    $("#datatable").DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        paging: true,
        pageLength: 10, // Default number of records to display
        lengthMenu: [
            [10, 20, 50, 100, 1000, 2000, -1], // Add '-1' for "All" option
            [10, 20, 50, 100, 1000, 2000, "All"] // Label for the options
        ],
        responsive: true, // Enable responsiveness
        ajax: {
        url: base_url + "Sale_return/getAjaxData",
        type: "POST",
        dataType: "json",
        data: {
            get_csrf_token_name: get_csrf_hash
        },
        },
        columnDefs: [
        { orderable: true, targets: [ 4, 6, 7 ] }
        ],

        dom: '<"top-left-item col-sm-12 col-md-6"lf> <"top-right-item col-sm-12 col-md-6"B> t <"bottom-left-item col-sm-12 col-md-6 "i><"bottom-right-item col-sm-12 col-md-6 "p>',
        buttons: [{
            extend: "print",
            text: '<span style="display: flex; align-items-center; gap: 8px;"><iconify-icon icon="solar:printer-broken" width="16"></iconify-icon> '+print_db+'</span>',
            titleAttr: "print",
        },
        {
            extend: "copyHtml5",
            text: '<span style="display: flex; align-items-center; gap: 8px;"><iconify-icon icon="solar:copy-broken" width="16"></iconify-icon> '+copy_db+'</span>',
            titleAttr: "Copy",
        },
        {
            extend: "excelHtml5",
            text: '<span style="display: flex; align-items-center; gap: 8px;"><iconify-icon icon="icon-park-solid:excel" width="16"></iconify-icon> '+excel_db+'</span>',
            titleAttr: "Excel",
        },
        {
            extend: "csvHtml5",
            text: '<span style="display: flex; align-items-center; gap: 8px;"><iconify-icon icon="teenyicons:csv-outline" width="16"></iconify-icon> '+csv_db+'</span>',
            titleAttr: "CSV",
        },
        {
            extend: "pdfHtml5",
            text: '<span style="display: flex; align-items-center; gap: 8px;"><iconify-icon icon="teenyicons:pdf-outline" width="16"></iconify-icon> '+pdf_db+'</span>',
            titleAttr: "PDF",
        },

        
    ],


        language: {
            paginate: {
              previous: "Previous",
              next: "Next",
            }
        },
        // Use drawCallback to initialize tooltips after each draw
        // Use initComplete to initialize tooltips after DataTable initialization is complete
        initComplete: function() {
            $('#datatable [data-bs-toggle="tooltip"]').tooltip();
        },
    });



    $(document).on('click', '.view_invoice', function() {
        viewInvoice($(this).attr('sale_id'));
    });
    function  viewInvoice(id) {
        let newWindow = open("print_invoice/" + id, 'Print Invoice', 'width=1600,height=550');
        newWindow.focus();
        newWindow.onload = function() {
          newWindow.document.body.insertAdjacentHTML('afterbegin');
        };
    }
	
});