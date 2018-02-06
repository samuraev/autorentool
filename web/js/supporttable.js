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

    var cellCount = $('#support-tabletask-table tr:first td').length - 1;
    // First create your thead section
    $('#support-tabletask-table').append('<thead><tr></tr></thead>');

    $thead = $('#support-tabletask-table > thead > tr:first');
    for (var i = 0, len = cellCount; i < len; i++) {
            $thead.append('<td align="center">' +
                '<button class="btn btn-sm btn-link delsupportcol" disabled type="button">' +
                '<i class="fa fa-trash-o"></i>' +
                '</button></td>');

    }

    if (cellCount <= 2) {
        $('.delsupportcol').attr('disabled', true);
    } else {
        $('.delsupportcol').attr('disabled', false);
    }

    var rowCount = $('#support-tabletask-table tr').length - 1;
    if (rowCount <= 1) {
        $('.delsupportrow').attr('disabled', true);
    } else {
        $('.delsupportrow').attr('disabled', false);
    }
});

$(document).ready(function () {
    // Get the tbody that holds the collection of tags
    $collectionHolderSupportRows = $('tbody#support-tabletask-tbody');
    $collectionHolderSupportCells = $('tbody#support-tabletask-tbody');

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolderSupportRows.data('index', $collectionHolderSupportRows.find('tr').length);
    $collectionHolderSupportCells.data('index', $collectionHolderSupportCells.find('tr:first :input').length);
});

$(document).on('click', '.table-supportrow-add', function (e) {
    e.preventDefault();

    // add a new tag form
    addSupportRowForm($collectionHolderSupportRows);
});

$(document).on('click', '.table-supportcol-add', function (e) {
    e.preventDefault();

    // add a new tag form
    addSupportColForm($collectionHolderSupportCells);
});


function addSupportColForm($collectionHolder) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var cellCount =$("#support-tabletask-tbody tr").length;

    $('tbody#support-tabletask-tbody  > tr').each(function() {

        var inputId = $(this).find('input:first').prop('id');
        var rowIndex = parseInt(inputId.split('_row_', 2)[1].split('_cell_', 2)[0]);
        var newFormRow = prototype.replace(/__rowname__/g, rowIndex);

        // get the new index
        index = $collectionHolder.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newFormCell = newFormRow.replace(/__cellname__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        var newFormTd = $('<td></td>').append(newFormCell);

        $(this).find("td:last").before(newFormTd);

    });

    $thead = $('#support-tabletask-table > thead > tr:first');
    $thead.append('<td align="center">' +
        '<button class="btn btn-sm btn-link delsupportcol" type="button">' +
        '<i class="fa fa-trash-o"></i></button></td>');

    cellCount = $('#support-tabletask-table tr:first td').length - 1;
    if (cellCount <= 2) {
        $('.delsupportcol').attr('disabled', true);
    } else {
        $('.delsupportcol').attr('disabled', false);
    }
}

function addSupportRowForm($collectionHolder) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');
    //console.log(index);
    var tr = $('<tr></tr>');
    var newFormRow = prototype.replace(/__rowname__/g, index);

    var cellCount =$("#support-tabletask-tbody tr:first > td").length - 1;

    for (i = 0; i < cellCount; i++) {
        // get the new index
        index = $collectionHolder.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newFormCell = newFormRow.replace(/__cellname__/g, index);

        // increase the index with one for the next item
        $collectionHolder.data('index', index + 1);

        var newFormTd = $('<td></td>').append(newFormCell);
        tr.append(newFormTd);
    }

    var delTd = $('<td align="center"></td>').append('' +
        '<button class="btn btn-sm btn-link delsupportrow" type="button">' +
        '<i class="fa fa-trash-o"></i></button>');
    tr.append(delTd);

    $collectionHolder.data('index', index + 1);

    // Display the form in the table in an tr
    $('tbody#support-tabletask-tbody').append(tr);

    var rowCount = $('#support-tabletask-table tr').length - 1;
    if (rowCount > 1) {
        $('.delsupportrow').attr('disabled', false);
    }
}

// check if at least one checkbox is checked
$(document).on('click', '.cell-support-head', function () {

    if ($(this).is(':checked')) {
        $(this).closest('td').addClass('headBg');
    } else {
        $(this).closest('td').removeClass('headBg');
    }
});

$(document).on('click', '.delsupportcol', function () {
    var colIndex = $(this).closest("td").index();
    $("#support-tabletask-table td", event.delegateTarget).remove(":nth-child(" + colIndex + ")");

    var cellCount = $('#support-tabletask-table tr:first td').length - 1;
    if (cellCount <= 2) {
        $('.delsupportcol').attr('disabled', true);
    }
});

$(document).on('click', '.delsupportrow', function () {
    //var rowIndex = $(this).closest("tr").index();
    $(this).closest('tr').remove();

    var rowCount = $('#support-tabletask-table tr').length - 1;
    if (rowCount <= 1) {
        $('.delsupportrow').attr('disabled', true);
    }
});