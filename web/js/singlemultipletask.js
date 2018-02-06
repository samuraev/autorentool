///////////////////////////////
// Single/Multiple Functions //
///////////////////////////////

$(document).ready(function () {
    // Get the tbody that holds the collection of tags
    $collectionHolder = $('tbody.smc-tbody');

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $('.btn-task-questionadd').on('click', function (e) {
        e.preventDefault();

        // add a new tag form
        addTagForm($collectionHolder);

        setUnrequedRightAnswer();
        //setMappedValueDefault();
    });

    setUnrequedRightAnswer();
    addRemoveDeleteTasksButton();
});

function addTagForm($collectionHolder) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the table in an tr
    var $newFormTr = $('<tr></tr>').append(newForm);
    $('tbody.smc-tbody').append($newFormTr);

    /*    if ($palCommands) {
            custom_combobox($palCommands);
        }*/
}

// toggle of radiobuttons cause of using prototype
$(document).on('change', 'tbody.smc-tbody input.one-rightanswer', function () {
    var tableSmcTBody = $('tbody.smc-tbody');
    tableSmcTBody.find('input.one-rightanswer').not($(this)).prop('checked', false);
    tableSmcTBody.find('input.one-rightanswer').prop('required', false);
    $('.one-rightanswer').parent().find('label.validation-error-class').remove();

});

// set 0 if point field is empty
$(document).on('change', 'tbody.smc-tbody input.mapped-value', function (event) {
    if (!event.target.value) {
        event.target.value = 0;
    }
});

// add answer fields in table
$(document).on("click", ".btn-task-questionadd", function () {
    addRemoveDeleteTasksButton();

    var validator = $( "form" ).validate();
    validator.resetForm();
});

// check if at least one checkbox is checked
$(document).on('click', 'tbody.smc-tbody input.many-rightanswer', function () {

    if ($('tbody.smc-tbody input[type="checkbox"]').is(':checked')) {
        $('tbody.smc-tbody').find('input.many-rightanswer').prop('required', false);
        $('.many-rightanswer').parent().find('label.validation-error-class').remove();
    } else {
        $('tbody.smc-tbody').find('input.many-rightanswer').prop('required', true);
    }
});

// remove answer fields from table
$(document).on("click", ".btn-deletetask", function () {
    $(this).closest('tr').remove();

    if ($('tbody.smc-tbody tr').length < 3) {
        $('tbody.smc-tbody').find('.btn-deletetask').hide();
    }

    setUnrequedRightAnswer();
});

$(document).on("click", "#removeTittleImgLink", function () {
    $(".tittle-img-preview").attr('src', '/img/empty.svg');
    $(".itemBody-removeImgSrc").prop('checked', true);
    $("#assessment_item_itemBody_imgSrc").val('');
});

function removeImageFromAntwort(imgSrcId, removeImgSrcStateID) {
    $("#simplechoice-img-preview-"+imgSrcId.id).attr('src', '/img/empty.svg');
    $(removeImgSrcStateID).prop('checked', true);
}

function setAntwortImage(imgSrcId, removeImgSrcStateID) {
    if (imgSrcId.files && imgSrcId.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#simplechoice-img-preview-"+imgSrcId.id).attr('src', e.target.result);
            $(removeImgSrcStateID).prop('checked', false);
        };
        reader.readAsDataURL(imgSrcId.files[0]);
    } else {
        $("#simplechoice-img-preview-"+imgSrcId.id).attr('src', '/img/empty.svg');
        $(removeImgSrcStateID).prop('checked', true);
    }
}

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

function setMappedValueDefault() {
    $('tbody.smc-tbody input.mapped-value').each(function () {
        if(! $(this).val() ) {
            $(this).val(0);
        }
    });
}

function addRemoveDeleteTasksButton() {
    if ($('tbody.smc-tbody tr').length > 2) {
        $('tbody.smc-tbody').find('.btn-deletetask').show();
    } else {
        $('tbody.smc-tbody').find('.btn-deletetask').hide();
    }
}

function setUnrequedRightAnswer() {
    var checked = false;
    $('tbody.smc-tbody input[type="radio"]').each(function () {
        if($(this).is(":checked")) {
            checked = true;
        }
    });

    if (checked) {
        $('tbody.smc-tbody').find('input.one-rightanswer').prop('required', false);
    } else {
        $('tbody.smc-tbody').find('input.one-rightanswer').prop('required', true);
    }

    checked = false;
    $('tbody.smc-tbody input[type="checkbox"]').each(function () {
        if($(this).is(":checked")) {
            checked = true;
        }
    });

    if (checked) {
        $('tbody.smc-tbody').find('input.many-rightanswer').prop('required', false);
    }
}

/*// drag and drop table rows
$(function () {
    var smc_tbody = $('.smc-tbody');
    smc_tbody.sortable();
    smc_tbody.disableSelection();
});*/

/*
// drag and drop table rows
$(function () {
    var smc_tbody = $('tbody.smc-tbody');
    var oldIndex;
    smc_tbody.sortable({

        start: function(event, ui) {
            ui.item.startPos = ui.item.index();
        },
        update: function(event, ui) {
            console.log("Start position: " + ui.item.startPos);
            console.log("New position: " + ui.item.index());
            console.log("Item: " + $(this));
            change($(this).data().uiSortable.currentItem);
        }




        });
    smc_tbody.disableSelection();


});

function change(item) {

    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // I assume we use jQuery
    var $prototype = $(prototype);

    // Get the item name
    // by convention, instad of a numeric index it will contain __name__
    var itemName = $prototype[2].childNodes[1].childNodes["0"].attributes[2].value;
console.log(item);
    // We can replace '__name__' it to whatever you like
    item["0"].children[1].childNodes[1].childNodes["0"].name = itemName.replace(/__name__/g, 111);
    
}*/
