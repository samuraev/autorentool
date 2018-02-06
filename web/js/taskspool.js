
////////////////////////////////
$(document).ready(function () {
    $('#taskspool-table').DataTable({
        // Einträge anzahl und default anzahl
        "lengthMenu": [[5, 10, 20, 50, -1], [5, 10, 20, 50, "All"]],
        "iDisplayLength": 10,

        responsive: true,
        columnDefs: [
            {orderable: false, targets: 4},
            {responsivePriority: 1, targets: 0},
            {responsivePriority: 2, targets: -1},
            {responsivePriority: 2, targets: 0},
            {responsivePriority: 3, targets: 2},
            {responsivePriority: 4, targets: 3}
        ],
        columns: [
            { width: "58%" },
            { width: "14%" },
            { width: "10%" },
            { width: "9%" },
            { width: "9%" }
        ],
        pageResize: true,
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

function editTask(url){
    var uuid = url.substring(url.lastIndexOf("/") + 1);

    $.ajax({
        type        : "POST",
        url         : "/stateoftask/" + uuid,
        async       : true,
        dataType    : 'json',
        success     : function (response) {
            if (response.status) {
                $(location).attr('href', url)
            } else {
                $('#submitedit').attr('href', url);
                $('#modalDesign').modal('show');
            }
        }
    });
}

function deleteTask(url){
    var uuid = url.substring(url.lastIndexOf("/") + 1);

    $.ajax({
        type        : "POST",
        url         : "/stateoftask/" + uuid,
        async       : true,
        dataType    : 'json',
        success     : function (response) {
            if (response.status) {
                $('#submitDelete').attr('href', url);
                $('#modalDelete').modal('show');
            } else {
                $('#submitDeleteDesign').attr('href', url);
                $('#modalDeleteDesign').modal('show');
            }
        }
    });
}

$(document).on("click", ".task-delete", function () {
    deleteTask($(this).data('delete-link'));
});

$(document).on("click", ".btn-edit-task", function () {
    editTask($(this).data('edit-link'));
});

$(document).on("click", ".ajaxTest", function () {

    var myData = {"username":"admin", "password":"admin"};

    $.ajax({
        type        : "POST",
        url         : "/api/alltasks",
        data        : JSON.stringify(myData),
        async       : true,
        dataType    : 'json',
        success     : function (response) {
            if (response.status === "success") {
                console.log(response.data);
            } else {
                console.log(response.status);
            }

        }
    });
});