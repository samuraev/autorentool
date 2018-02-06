
var tasksData = null;
var relatedUuids = [];

// set new task tab active while change type of tasks
$(function () {
    $('#newtask-nav').addClass('active');
});

function renameNavTabNewToEdit () {
    $('.nav-tabs').find('li:eq(1)')
        .children('a').bind('click', false)
        .end().children('a').text("Edit Aufgabe");

    $('.nav-tabs').find('li:eq(1)')
        .children('a').css('cursor', 'not-allowed');
}

$(document).ready(function () {
    var chosenSelectorCategory = $(".chosen-select-category");
    chosenSelectorCategory.chosen({width: "100%"});
    chosenSelectorCategory.chosen();

    var chosenSelectorSupport = $(".chosen-select-support");
    chosenSelectorSupport.chosen({width: "100%"});
    chosenSelectorSupport.chosen();

    toggleSupport($('.support-type').find('input[type=radio]:checked').val());
});

// add new category tag by pressing enter key
$(document).on('keyup', '.chosen-container', function( event ) {
    if (event.which === 13) {
        event.preventDefault();

        var chosenSelector = $(this).parent().find('select');

        // check if it is not already exists and add it
        if ($.inArray($(event.target).val(), chosenSelector.val()) < 0) {

            chosenSelector.append("<option value='" + $(event.target).val() + "'>" + $(event.target).val() + "</option>");
            chosenSelector.find('option[value=' + $(event.target).val() + ']').attr('selected', 'selected');
            chosenSelector.trigger('chosen:updated');

        }
    }
});

$(document).on("click", ".btn-savetask", function () {

    var form = $( "form" );
    form.validate({
        errorClass: "validation-error-class",
        validClass: "validation-valid-class"
    });

    if ($(this).data('uuid-link')) {
        $('.cell-head').prop('disabled', false);
        saveTask($(this).data('uuid-link'));
    } else {
        if (form.valid()) {
            $('.cell-head').prop('disabled', false);
            form.submit();
        }
    }
});

//check state of task, it can be from other person in this time deleted
function saveTask(uuid){
    $.ajax({
        type        : "POST",
        url         : "/stateoftask/" + uuid,
        async       : true,
        dataType    : 'json',
        success     : function (response) {
            if (response.status === -1) {
                $('#submittasknotfound').attr('href', "\\");
                $('#modalTaskNotFound').modal('show');
            } else {
                $( "form" ).submit();
            }
        }
    });
}

$(document).on("click", "#support-toggle", function () {
    $(this).find('i').toggleClass('glyphicon-menu-down').toggleClass('glyphicon-menu-up');
});

$(document).on("click", ".support-type input[type=radio]", function () {
    toggleSupport($(this).val());
});

function toggleSupport(valueOfRadioButton) {
    switch(valueOfRadioButton) {
        case 'media' :
            showHideSupportContainer($('.support-media-container'), true);
            showHideSupportContainer($('.support-textbox-container'), false);
            showHideSupportContainer($('.support-selection-container'), false);
            showHideSupportContainer($('.support-table-container'), false);
            break;
        case 'textbox' :
            showHideSupportContainer($('.support-media-container'), false);
            showHideSupportContainer($('.support-textbox-container'), true);
            showHideSupportContainer($('.support-selection-container'), false);
            showHideSupportContainer($('.support-table-container'), false);
            break;
        case 'selection' :
            showHideSupportContainer($('.support-media-container'), false);
            showHideSupportContainer($('.support-textbox-container'), false);
            showHideSupportContainer($('.support-selection-container'), true);
            showHideSupportContainer($('.support-table-container'), false);
            break;
        case 'table' :
            showHideSupportContainer($('.support-media-container'), false);
            showHideSupportContainer($('.support-textbox-container'), false);
            showHideSupportContainer($('.support-selection-container'), false);
            showHideSupportContainer($('.support-table-container'), true);
            break;
    }
}

function showHideSupportContainer(container, show) {
    if (show){
        container.css("display","inline");
    } else {
        container.css("display","none");
    }
}

$(document).on('fileselect', '.support-media', function(event, numFiles, label) {

    var input = $(this).parents('.input-group-media-support').find(':text'),
        log = numFiles > 1 ? numFiles + ' files selected' : label;

    if( input.length ) {
        input.val(log);
    } else {
        if( log ) alert(log);
    }
});

$(document).on('change', '.support-media', function() {

    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

    input.trigger('fileselect', [numFiles, label]);
});


// remove background image from canvas
$(document).on("click", "#removeSupportImage", function () {
    $('.support-media-filename').parents('.input-group-media-support').find(':text').val("");

    $('.support-media').get(0).value = "";
    //$(".hotspot-removeImgSrc").prop('checked', true);

});
