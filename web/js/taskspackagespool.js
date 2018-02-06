/*var uuidArray = [];

$('[type=checkbox]').click(function () {
    var checkbox_this = $(this);

    if (checkbox_this.is(":checked") === true) {
        uuidArray.push($(this).val());
    } else {
        uuidArray.splice($.inArray($(this).val(), uuidArray), 1);
    }

    if (uuidArray.length > 0) {
        $('.btn-success').addClass('disabled');
        $('.btn-primary').removeClass('disabled');
    } else {
        $('.btn-success').removeClass('disabled');
        $('.btn-primary').addClass('disabled');
    }
});*/

$(document).on("click", ".task-delete", function () {
    $('#submitDelete').attr('href', $(this).data('delete-link'));
});

////////////////////////////////
$(document).ready(function () {

    $('#taskspackages-table').DataTable({
        // Einträge anzahl und default anzahl
        "lengthMenu": [[5, 10, 20, 50, -1], [5, 10, 20, 50, "All"]],
        "iDisplayLength": 10,

        responsive: true,
        columnDefs: [
            {orderable: false, targets: 3},
            {responsivePriority: 1, targets: 0},
            {responsivePriority: 2, targets: -1},
            {responsivePriority: 2, targets: 0},
            {responsivePriority: 3, targets: 2}
        ],
        columns: [
            { width: "30%" },
            { width: "47%" },
            { width: "14%" },
            { width: "9%" }
        ],
        fixedColumns: {
            heightMatch: 'none'
        },
        order: [],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
        },
        "search": {
            "smart": false
        }
    });

    $('.tasksdatatable td').css('white-space','normal');
});

