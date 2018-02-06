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
});


$(document).ready(function () {
    $(".amls-task-delete").click(function () {

        var params = "";
        for (var i = 0; i < uuidArray.length; i++) {
            if (uuidArray.length === 1) {
                params = uuidArray[i];
                break;
            } else if (i === uuidArray.length) {
                params += uuidArray[i];
            } else {
                params += uuidArray[i] + "&";
            }
        }

        $(".conf-delete").attr("href", "/delete/" + params);
    });
});*/

////////////////////////////////
$(document).ready(function () {
    $('#newpackage-alltasks-table').DataTable({
        // Einträge anzahl und default anzahl
        "lengthMenu": [[5, 10, 20, 50, -1], [5, 10, 20, 50, "All"]],
        "iDisplayLength": 10,

        responsive: true,
        columnDefs: [
            {orderable: false, targets: 3}
        ],
        columns: [
            { width: "64%" },
            { width: "18%" },
            { width: "15%" },
            { width: "3%" }
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

    $('#newpackage-table').DataTable({
        // Einträge anzahl und default anzahl
        "lengthMenu": [[5, 10, 20, 50, -1], [5, 10, 20, 50, "All"]],
        "iDisplayLength": 10,

        responsive: true,
        columnDefs: [
            {orderable: false, targets: 3}
        ],
        columns: [
            { width: "62%" },
            { width: "17%" },
            { width: "15%" },
            { width: "6%" }
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

$(document).on("change", "#newpackage-alltasks-tbody #checkbox", function () {
    var newpackageTable = $('#newpackage-table').dataTable();

    if ($(this).is(":checked")) {
        var rowToAdd = $(this).closest('tr').clone();

        var parent = rowToAdd.find('#checkbox').closest('td').empty();
        parent.append($('<a type="button" class="btn btn-danger tasktable-btn task-delete" ' +
            'value="' + $(this).prop("value") + '">' +
            '<span class="glyphicon glyphicon-trash" style="vertical-align:middle" aria-hidden="true"></span></a>')
        );
        newpackageTable.fnAddData(rowToAdd);
    } else {
        $(this).prop( "checked", false );
        var rowToRemove = $('#newpackage-tbody [value=' + $(this).prop("value") + ']').closest('tr');
        newpackageTable.fnDeleteRow(rowToRemove);
    }

    if (newpackageTable.fnGetData().length) {
        $('#create-taskspackage').attr('disabled', false);
    } else {
        $('#create-taskspackage').attr("disabled", true);
    }
});


$(document).on("click", "#newpackage-tbody .task-delete", function () {
    var newpackageTable = $('#newpackage-table').dataTable();

    var rowToDelete = $(this).closest('tr');
    newpackageTable.fnDeleteRow(rowToDelete);

    $('#newpackage-alltasks-tbody  #checkbox[value=' + $(this).attr("value") + ']').prop( "checked", false );

    if (newpackageTable.fnGetData().length ) {
        $('#create-taskspackage').attr('disabled', false);
    } else {
        $('#create-taskspackage').attr("disabled", true);
    }
});

$(document).on("click", "#create-taskspackage", function () {
    var form = $( "form" );
    form.validate({
        errorClass: "validation-error-class",
        validClass: "validation-valid-class"
    });
    if (form.valid()) {
        createTasksPackage();
    }
});

function createTasksPackage() {
    var newpackageTable = $('#newpackage-table').DataTable();

    var packageInfo = [];
    var uuids = [];

    newpackageTable.rows().every( function ( rowIdx ) {
        var node = $(this.row(rowIdx).node());
        uuids[rowIdx] = node.find('.task-delete').attr("value");
    } );

    var tittle = $('#newpackage-tittle').val();
    var description = $('#newpackage-description').val();

    packageInfo.push({'tittle':tittle, 'description':description});
    packageInfo.push(uuids);

    var jsonPackageInfo = JSON.stringify(packageInfo);

    $(".loading-animation").LoadingOverlay("show");
    $.ajax({
        type        : "POST",
        url         : "/newpackage/create",
        async       : true,
        dataType    : 'json',
        data        : {'packageinfo': jsonPackageInfo},
        success     : function (response) {
            $(".loading-animation").LoadingOverlay("hide");
            alert(response.message);
        }
    });
}


