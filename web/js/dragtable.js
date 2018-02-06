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

    var cellCount = $('#tabletask-table tr:first td').length - 1;
    // First create your thead section
    $('#tabletask-table').append('<thead><tr></tr></thead>');

    $thead = $('#tabletask-table > thead > tr:first');
    for (var i = 0, len = cellCount; i < len; i++) {
            $thead.append('<td align="center">' +
                '<button class="btn btn-sm btn-link delcol" disabled type="button">' +
                '<i class="fa fa-trash-o"></i>' +
                '</button></td>');
    }

    // set header
    if ($('.dragtable-mode-select').val() === 'row') {
        $("table tr td:nth-child(1)").each(function () {
            $(this).find('input.cell-head').prop('checked', true);
            $(this).find('input.cell-head').prop('disabled', true);
            $(this).find('input.cell-head').closest('td').addClass('headBg');
        });
    } else {
        $("table tr:nth-child(1) td").each(function () {
            $(this).find('input.cell-head').prop('checked', true);
            $(this).find('input.cell-head').prop('disabled', true);
            $(this).find('input.cell-head').closest('td').addClass('headBg');
        });
    }

    if (cellCount <= 2) {
        $('.delcol').attr('disabled', true);
    }

    var rowCount = $('#tabletask-table tr').length - 1;
    if (rowCount <= 2) {
        $('.delrow').attr('disabled', true);
    }

    $('.dragtable-mode-select').find('option[value=""]').remove();

    // Get the tbody that holds the collection of tags
    $collectionHolderRows = $('tbody#tabletask-tbody');
    $collectionHolderCells = $('tbody#tabletask-tbody');

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolderRows.data('index', $collectionHolderRows.find('tr').length);
    $collectionHolderCells.data('index', $collectionHolderCells.find('tr:first :input').length);
});

$(document).on('click', '.table-row-add', function (e) {
    e.preventDefault();

    // add a new tag form
    addRowForm($collectionHolderRows);
});


$(document).on('click', '.table-col-add', function (e) {
    e.preventDefault();

    // add a new tag form
    addColForm($collectionHolderCells);
});

$(document).on('change', '.dragtable-mode-select', function (e) {

    $('.cell-head').prop('checked', false);
    $('.cell-head').closest('td').removeClass('headBg');

    // set header
    if ($(this).val() === 'row') {
        $("table tr td:nth-child(1)").each(function () {
            $(this).find('input.cell-head').prop('checked', true);
            $(this).find('input.cell-head').prop('disabled', true);
            $(this).find('input.cell-head').closest('td').addClass('headBg');
        });
    } else {
        $("table tr:nth-child(1) td").each(function () {
            $(this).find('input.cell-head').prop('checked', true);
            $(this).find('input.cell-head').prop('disabled', true);
            $(this).find('input.cell-head').closest('td').addClass('headBg');
        });
    }
});


function addColForm($collectionHolder) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');
    var index = $collectionHolder.data('index');
    var cellCount =$("#tabletask-tbody tr").length;

    $('tbody#tabletask-tbody  > tr').each(function() {

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

    $thead = $('#tabletask-table > thead > tr:first');
    $thead.append('<td align="center">' +
        '<button class="btn btn-sm btn-link delcol" type="button">' +
        '<i class="fa fa-trash-o"></i></button></td>');

    cellCount = $('#tabletask-table tr:first td').length - 1;
    if (cellCount <= 2) {
        $('.delcol').attr('disabled', true);
    } else {
        $('.delcol').attr('disabled', false);
    }

    // set header
    if ($('.dragtable-mode-select').val() === 'column') {
        $("table tr:nth-child(1) td").each(function () {
            $(this).find('input.cell-head').prop('checked', true);
            $(this).find('input.cell-head').prop('disabled', true);
            $(this).find('input.cell-head').closest('td').addClass('headBg');
        });
    }

}

function addRowForm($collectionHolder) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    var index = $collectionHolder.data('index');
    //console.log(index);
    var tr = $('<tr></tr>');
    var newFormRow = prototype.replace(/__rowname__/g, index);

    var cellCount =$("#tabletask-tbody tr:first > td").length - 1;

    for (i = 0; i < cellCount; i++) {
        // get the new index
        index = $collectionHolderCells.data('index');

        // Replace '__name__' in the prototype's HTML to
        // instead be a number based on how many items we have
        var newFormCell = newFormRow.replace(/__cellname__/g, index);

        // increase the index with one for the next item
        $collectionHolderCells.data('index', index + 1);

        var newFormTd = $('<td></td>').append(newFormCell);
        tr.append(newFormTd);
    }

    var delTd = $('<td align="center"></td>').append('' +
        '<button class="btn btn-sm btn-link delrow" type="button">' +
        '<i class="fa fa-trash-o"></i></button>');
    tr.append(delTd);

    $collectionHolder.data('index', index + 1);

    // Display the form in the table in an tr
    $('tbody#tabletask-tbody').append(tr);

    var rowCount = $('#tabletask-table tr').length - 1;
    if (rowCount > 1) {
        $('.delrow').attr('disabled', false);
    }

    // set header
    if ($('.dragtable-mode-select').val() === 'row') {
        $("table tr td:nth-child(1)").each(function () {
            $(this).find('input.cell-head').prop('checked', true);
            $(this).find('input.cell-head').prop('disabled', true);
            $(this).find('input.cell-head').closest('td').addClass('headBg');
        });
    }
}


$(document).on('click', '.delcol', function () {
    var colIndex = $(this).closest("td").index();
    $("td", event.delegateTarget).remove(":nth-child(" + colIndex + ")");

    var cellCount = $('#tabletask-table tr:first td').length - 1;
    if (cellCount <= 2) {
        $('.delcol').attr('disabled', true);
    }
});


$(document).on('click', '.delrow', function () {
    //var rowIndex = $(this).closest("tr").index();
    $(this).closest('tr').remove();

    var rowCount = $('#tabletask-table tr').length - 1;
    if (rowCount <= 2) {
        $('.delrow').attr('disabled', true);
    }
});

$(document).on("click", "#removeTittleImgLink", function () {
    $(".tittle-img-preview").attr('src', '/img/empty.svg');
    $(".itemBody-removeImgSrc").prop('checked', true);
    $("#assessment_item_itemBody_imgSrc").val('');
});

function setTittleImage(id) {
    if (id.files && id.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('.tittle-img-preview').attr('src', e.target.result);
            $(".itemBody-removeImgSrc").prop('checked', false);
        };
        reader.readAsDataURL(id.files[0]);
    } else {
        $('.tittle-img-preview').attr('src', '/img/empty.svg');
        $(".itemBody-removeImgSrc").prop('checked', true);
    }
}