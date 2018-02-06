///////////////////////
// HOTSPOT Functions //
//////////////////////
var canvasToDraw;
var startX;
var startY;
var isDown = false;
var polyCoordinates = [];
var widthRatio = 1;
var heightRatio = 1;
var origImageWidth = 0;
var origImageHeight = 0;
var noImagePath = '/img/keinbuild.svg';


$(document).ready(function () {
    // Get the tbody that holds the collection of tags
    $collectionHolder = $('tbody.smc-tbody');

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    setUnrequedRightAnswer();
    addRemoveDeleteTasksButton();

    $('#parentOfCanvas').imagesLoaded( { background: '.bgImage' }, function() {
        var bgImage = $('#bgImage');

        scaleBgImage(bgImage.width(), bgImage.height());

        if (bgImage.attr("src").includes(noImagePath)) {
            $('#removeImgLink').prop('disabled', true);
            $('#clearImgLink').prop('disabled', true);
        } else {
            $('#removeImgLink').prop('disabled', false);
            $('#clearImgLink').prop('disabled', false);
        }
        addAndDrawCanvasesEditTask();
    });
});

$(window).on('resize', function(){
    if (origImageWidth > $("#parentOfCanvas").width()) {
        scaleBgImage(origImageWidth, origImageHeight);
        redrawCanvasesByResize();
    }
});

function redrawCanvasesByResize(canvas) {
    $('tbody.smc-tbody input[type="radio"]').each(function () {
        var coordEl = $(this).closest('tr').find('input[type="text"]');

        canvasToDraw = $('#layerCanvas_' + $(this).prop("id"));

        scaleCanvas(canvasToDraw);
        redrawCanvases(coordEl);
    })
}

function addAndDrawCanvasesEditTask() {
    $('tbody.smc-tbody input[type="radio"]').each(function () {
        var coordEl = $('#' + $(this).closest('tr').find('input[type="text"]').prop("id"));

        canvasToDraw = addCanvas($(this).prop("id"));
        redrawCanvases(coordEl);
    })
}

function redrawCanvases(coordEl) {
    if (coordEl.val()) {
        var coordStr = coordEl.val();
        var selection = coordEl.closest('tr').find('select').val();

        switch (selection) {
            case 'circle':
                var coordCircle = coordStr.split(",");
                var scaledCenterX = Math.round(parseInt(coordCircle[0]) / widthRatio);
                var scaledCenterY = Math.round(parseInt(coordCircle[1]) / heightRatio);
                var scaledRadius = Math.round(parseInt(coordCircle[2]) / widthRatio);

                drawCircle(scaledCenterX, scaledCenterY, scaledRadius);
                break;

            case 'ellipse':
                var coordEllipse = coordStr.split(",");
                var startX = parseInt(coordEllipse[0]) - parseInt(coordEllipse[2]);
                var startY = parseInt(coordEllipse[1]) - parseInt(coordEllipse[3]);
                var endX = parseInt(coordEllipse[0]) + parseInt(coordEllipse[2]);
                var endY = parseInt(coordEllipse[1]) + parseInt(coordEllipse[3]);

                var scaledStartX = Math.round(startX / widthRatio);
                var scaledStartY = Math.round(startY / heightRatio);
                var scaledEndX = Math.round(endX / widthRatio);
                var scaledEndY = Math.round(endY / heightRatio);

                drawEllipse(scaledStartX, scaledStartY, scaledEndX, scaledEndY);
                break;
            case 'rect':
                var coordSet = coordStr.split(";");
                var coordTop = coordSet[0].split(",");
                var coordBotton = coordSet[1].split(",");

                var scaledTopX = Math.round(parseInt(coordTop[0]) / widthRatio);
                var scaledTopY = Math.round(parseInt(coordTop[1]) / heightRatio);
                var scaledBotX = Math.round(parseInt(coordBotton[0]) / widthRatio);
                var scaledBotY = Math.round(parseInt(coordBotton[1]) / heightRatio);

                drawRect(scaledTopX, scaledTopY, scaledBotX, scaledBotY);
                break;
            case 'poly':
                var coordSet = coordStr.split(";");
                var polyCoordinates = [];

                for(index=0; index < coordSet.length; index++) {
                    var coord = coordSet[index].split(",");

                    var scaledX = Math.round(parseInt(coord[0]) / widthRatio);
                    var scaledY = Math.round(parseInt(coord[1]) / heightRatio);

                    polyCoordinates.push({x:scaledX,y:scaledY});
                }

                drawPolygon(polyCoordinates);
                break;
        }
    }
}

$(document).on('fileselect', '.hotspotImgSrc', function(event, numFiles, label) {

    var input = $(this).parents('.input-group-image-hotspot').find(':text'),
        log = numFiles > 1 ? numFiles + ' files selected' : label;

    if( input.length ) {
        input.val(log);
    } else {
        if( log ) alert(log);
    }
});

$(document).on('change', '.hotspotImgSrc', function() {

    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');

    input.trigger('fileselect', [numFiles, label]);

    readURL(this);
});

// set new image and scale it
function readURL(inputFile) {
    var bgImage = $('#bgImage');

    if (inputFile.files && inputFile.files[0]) {
        var reader = new FileReader();
        reader.readAsDataURL(inputFile.files[0]);
        reader.onload = function (e) {
            var img = new Image;
            img.src = e.target.result;
            img.onload = function() {
                bgImage.hide();
                bgImage.attr("src", img.src);

                scaleBgImage(img.width, img.height);

                bgImage.show();
                $('.canvas').each(function () {
                    scaleCanvas($(this));
                });
            };
        };
        $('#removeImgLink').prop('disabled', false);
        $('#clearImgLink').prop('disabled', false);
    } else {
        removeImage();
    }
}

$(document).on('click', '.btn-task-questionadd', function (e) {
    e.preventDefault();

    // add a new tag form
    addTagForm($collectionHolder);
    setUnrequedRightAnswer();
});

// set size of background image according to parent size of canvas
function scaleBgImage(orgImgWidth, orgImgHeight) {
    var canvasParent = $("#parentOfCanvas");
    var bgImage = $('#bgImage');
    var bgImageSrc = bgImage.attr("src");

    if (bgImageSrc.includes(noImagePath)) {
        origImageWidth = 227;
        origImageHeight = 150;
        bgImage.width(origImageWidth);
        bgImage.height(origImageHeight);
    } else {
        if (orgImgWidth < canvasParent.width()) {
            bgImage.width(orgImgWidth);
            bgImage.height(orgImgHeight);
            widthRatio = 1;
            heightRatio = 1;
        } else {
            var newImgWidth = canvasParent.width();
            var newImgHeight = newImgWidth * orgImgHeight / orgImgWidth;
            bgImage.width(newImgWidth);
            bgImage.height(newImgHeight);
            widthRatio = orgImgWidth / newImgWidth;
            heightRatio = orgImgHeight / newImgHeight;
        }

        origImageWidth = orgImgWidth;
        origImageHeight = orgImgHeight;
    }
    bgImage.show();
}

// set size of canvas according to the background image
function scaleCanvas(canvas) {
    var canvasParent = $("#parentOfCanvas");
    var bgImage = $('#bgImage');

    if (bgImage.width() < canvasParent.width()) {
        canvas.attr({"height": bgImage.height() , "width": bgImage.width()});
    } else {
        var canvasWidth = canvasParent.width();
        var canvasHeight = canvasWidth * bgImage.height() / bgImage.width();
        canvas.attr({"height": canvasHeight, "width": canvasWidth});
    }
}

// remove image and scale everything
function removeImage() {
    var bgImage = $('#bgImage');
    var img = new Image;

    img.src = noImagePath;
    img.onload = function() {
        // hier is set path of file and not dataURI from img.src => for fürther check by delete image
        bgImage.attr("src", noImagePath);
        scaleBgImage(img.width, img.height);

        $('.canvas').each(function () {
            scaleCanvas($(this));
        });

        $('.hotspotImageFileName').parents('.input-group-image-hotspot').find(':text').val("");
    };
    $('#removeImgLink').prop('disabled', true);
    $('#clearImgLink').prop('disabled', true);

}

function addCanvas(id) {
    var newCanvas = $('<canvas class="canvas" style="no-repeat center"></canvas>');
    var bgImage = $('#bgImage');

    newCanvas.css('position', 'absolute');
    newCanvas.attr("id", "layerCanvas_" + id);
    scaleCanvas(newCanvas);
    $("#parentOfCanvas").prepend(newCanvas);

    return newCanvas;
}

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

    var rightansverId = $newFormTr.find('input.one-rightanswer').prop("id");
    var newCanvas = addCanvas(rightansverId);

    var textAreaId = $newFormTr.find('textarea').prop("id");
}

// toggle of radiobuttons cause of using prototype
$(document).on('change', 'tbody.smc-tbody input.one-rightanswer', function () {
    var tableSmcTBody = $('tbody.smc-tbody');
    tableSmcTBody.find('input.one-rightanswer').not($(this)).prop('checked', false);
});

// add answer fields in table
$(document).on("click", ".btn-task-questionadd", function () {
    addRemoveDeleteTasksButton();
});

// set decription how to draw current shape
$(document).on('change', 'select', function () {
    var descriptionTd = $(this).closest('tr').find('td:nth-child(4)');

    switch ($(this).val()) {
        case 'circle':
            descriptionTd.find('p').text("Anklicken und ziehen Maus vom Zentrum bis zum Rand des Objektes");
            break;
        case 'ellipse':
            descriptionTd.find('p').text("Anklicken und ziehen Maus von linken oberen bis zu rechten unteren Ecke des Objektes");
            break;
        case 'rect':
            descriptionTd.find('p').text("Anklicken und ziehen Maus von linken oberen bis zu rechten unteren Ecke des Objektes");
            break;
        case 'poly':
            descriptionTd.find('p').text("Klicken auf wichtigen Punkten des Objektes");
            break;
    }

});

function addRemoveDeleteTasksButton() {
    if ($('tbody.smc-tbody tr').length > 1) {
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
            return false;
        }
    });
}

// remove answer fields from table
$(document).on("click", ".btn-deletetask", function () {
    var idCanvas = "layerCanvas_" + $(this).closest('tr').find('input.one-rightanswer').prop("id");

    // remove canvas
    $('#' + idCanvas).remove();
    $(this).closest('tr').remove();

    var selection = $(this).closest('tr').find("select  option:selected").val();

    if (selection === 'poly') {
        polyCoordinates = [];
    }

    if ($('tbody.smc-tbody tr').length < 2) {
        $('tbody.smc-tbody').find('.btn-deletetask').hide();
    }

    setUnrequedRightAnswer();
});

// set new image in canvas from input
$(document).on('change', '#showAllCanvases', function() {
    if ($(this).is(":checked")){
        $('.canvas').show();
    } else {
        $('.canvas').not(canvasToDraw).hide();
    }
});

$(document).on('change', 'tbody.smc-tbody input[type="radio"]', function() {
    canvasToDraw = $('#layerCanvas_' + $(this).prop("id"));

    polyCoordinates = [];

    if ($('#showAllCanvases').is(":checked")) {
        $('.canvas').show();
    } else {
        $('.canvas').hide();
        $(canvasToDraw).show();
    }
});

$(document).on("mousedown", '.canvas', function (e) {
    handleMouseDown(e);
});

$(document).on("mousemove", '.canvas', function (e) {
    handleMouseMove(e);
});

$(document).on("mouseup", '.canvas', function (e) {
    handleMouseUp(e);
});

$(document).on("mouseout", '.canvas', function (e) {
    handleMouseOut(e);
});


/*function cropCanvasToShapeSize(canvas, topX, topY, width, height) {
    // get your canvas and a context for it
    var ctx = canvas.getContext('2d');

    // get the image data you want to keep.
    var imageData = ctx.getImageData(topX, topY, width, height);

    // create a new cavnas same as clipped size and a context
    var newCan = document.createElement('canvas');
    newCan.width = width;
    newCan.height = height;
    var newCtx = newCan.getContext('2d');

    // put the clipped image on the new canvas.
    newCtx.putImageData(imageData, 0, 0);

    return newCan;
}*/

$(document).on('submit', 'form', function (e) {
    e.preventDefault();

    $('#smc-table > tbody  > tr').each(function() {
        var coordStr = $(this).closest('tr').find('input[type="text"]').val();
        if (coordStr) {
            var textareaImage = $(this).closest('tr').find('textarea');
            var selection = $(this).closest('tr').find("select  option:selected").val();

            var topX = 0;
            var topY = 0;
            var width = 0;
            var height = 0;

            // create a new canvas same as clipped size and a context
            canvasToDraw = $('<canvas class="canvass" style="no-repeat center"></canvas>');

            if (selection === 'circle') {
                var coord = coordStr.split(",");
                topX = parseInt(coord[0]) - parseInt(coord[2]);
                topY = parseInt(coord[1]) - parseInt(coord[2]);
                width = parseInt(coord[0]) + parseInt(coord[2]) - topX;
                height = parseInt(coord[1]) + parseInt(coord[2]) - topY;

                // set canvas size to shape size + 2px for spacing
                canvasToDraw.attr({"height": height + 2, "width": width + 2});

                $("#parentOfCanvas").prepend(canvasToDraw);

                // set center coordinates of drawen shape from radius + 1px for spacing top/left side
                drawCircle(parseInt(coord[2]) + 1, parseInt(coord[2]) + 1, parseInt(coord[2]));
            }
            if (selection === 'ellipse') {
                var coord = coordStr.split(",");
                topX = parseInt(coord[0]) - parseInt(coord[2]);
                topY = parseInt(coord[1]) - parseInt(coord[3]);
                var endX = parseInt(coord[0]) + parseInt(coord[2]);
                var endY = parseInt(coord[1]) + parseInt(coord[3]);

                width = parseInt(coord[0]) + parseInt(coord[2]) - topX;
                height = parseInt(coord[1]) + parseInt(coord[3]) - topY;

                // set canvas size to shape size + 2px for spacing
                canvasToDraw.attr({"height": height + 2, "width": width + 2});

                $("#parentOfCanvas").prepend(canvasToDraw);

                // set coordinates of drawen shape + 1px for spacing top/left side
                drawEllipse(1, 1, width + 1, height + 1);
            }
            if (selection === 'rect') {
                var coordSet = coordStr.split(";");
                var coordTop = coordSet[0].split(",");
                var coordBotton = coordSet[1].split(",");

                topX = parseInt(coordTop[0]);
                topY = parseInt(coordTop[1]);

                width = parseInt(coordBotton[0]) - topX;
                height = parseInt(coordBotton[1]) - topY;

                // set canvas size to shape size + 2px for spacing
                canvasToDraw.attr({"height": height + 2, "width": width + 2});

                $("#parentOfCanvas").prepend(canvasToDraw);

                // set coordinates of drawen shape + 1px for spacing top/left side
                drawRect(1, 1, width + 1, height + 1);
            }
            if (selection === 'poly') {
                var coordSet = coordStr.split(";");
                var polCoordX = [];
                var polCoordY = [];
                var polCoord = [];

                for (index = 0; index < coordSet.length; index++) {
                    var coord = coordSet[index].split(",");
                    polCoordX.push(coord[0]);
                    polCoordY.push(coord[1]);
                }

                topX = Math.min.apply(Math, polCoordX);
                topY = Math.min.apply(Math, polCoordY);
                width = Math.max.apply(Math, polCoordX) - topX;
                height = Math.max.apply(Math, polCoordY) - topY;

                for (index = 0; index < coordSet.length; index++) {
                    var coord = coordSet[index].split(",");

                    var currentX = parseInt(coord[0]) - topX + 1;
                    var currentY = parseInt(coord[1]) - topY + 1;
                    polCoord.push({x: currentX, y: currentY});
                }

                // set canvas size to shape size + 2px for spacing
                canvasToDraw.attr({"height": height + 2, "width": width + 2});

                drawPolygon(polCoord);
            }

            /*var w = window.open('about:blank', 'image from canvas');
            w.document.write("<img src='" + canvasToDraw.get(0).toDataURL("image/png") + "' alt='from canvas'/>");*/
            textareaImage.val(canvasToDraw.get(0).toDataURL("image/png"));

            canvasToDraw.remove();
        }
    });

    /*$('.hotspotImgSrc').get(0).value = "";
    polyCoordinates = [];
    $(".hotspot-removeImgSrc").prop('checked', true);*/

    this.submit();
});

function handleMouseDown(e) {
    e.preventDefault();
    e.stopPropagation();
    isDown = true;

    var bgImage = $('#bgImage');
    var bgImageSrc = bgImage.attr("src");

    currentHotspotAnswer = $('tbody.smc-tbody input[type="radio"]:checked');
    if (currentHotspotAnswer.length && bgImageSrc !== noImagePath) {
        var pos = getMousePos(canvasToDraw, e);
        startX = pos.x;
        startY = pos.y;
        var selection = currentHotspotAnswer.closest('tr').find("select  option:selected").val();

        if (selection === 'poly') {
            polyCoordinates.push({x:startX,y:startY});
            drawPolygon(polyCoordinates, true);
        }
    }
}

function handleMouseUp(e) {
    if (!isDown) {
        return;
    }
    e.preventDefault();
    e.stopPropagation();
    isDown = false;
}

function handleMouseOut(e) {
    if (!isDown) {
        return;
    }
    e.preventDefault();
    e.stopPropagation();
    isDown = false;
}

function handleMouseMove(e) {
    if (!isDown) {
        return;
    }
    e.preventDefault();
    e.stopPropagation();


    var bgImage = $('#bgImage');
    var bgImageSrc = bgImage.attr("src");

    currentHotspotAnswer = $('tbody.smc-tbody input[type="radio"]:checked');
    if (currentHotspotAnswer.length && bgImageSrc !== noImagePath) {
        var pos = getMousePos(canvasToDraw, e);
        var selection = currentHotspotAnswer.closest('tr').find("select  option:selected").val();
        switch (selection) {
            case 'circle':
                var radius = Math.sqrt(Math.pow(pos.x-startX, 2) + Math.pow(pos.y-startY, 2));
                drawCircle(startX, startY, radius, true);
                break;
            case 'ellipse':
                drawEllipse(startX, startY, pos.x, pos.y, true);
                break;
            case 'rect':
                drawRect(startX, startY, pos.x, pos.y, true);
                break;
        }
    }
}

// draw polygon
// param:
//      polyCoordinates -> coordinates of points: array({x:x, y:y})
function drawPolygon(polyCoordinates, updateCoords){
    updateCoords = updateCoords || false;

    var ctx = canvasToDraw.get(0).getContext("2d");

    ctx.clearRect(0, 0, canvasToDraw.get(0).width, canvasToDraw.get(0).height);
    ctx.beginPath();
    ctx.fillStyle = "rgba(240, 3, 3, 0.5)";
    ctx.moveTo(polyCoordinates[0].x, polyCoordinates[0].y);
    for(index=1; index<polyCoordinates.length;index++) {
        ctx.lineTo(polyCoordinates[index].x, polyCoordinates[index].y);
    }
    ctx.fill();
    ctx.closePath();
    ctx.stroke();

    if (updateCoords) {
        updateCoordinatesInputPolygon(polyCoordinates);
    }
}

function updateCoordinatesInputPolygon(polyCoordinates) {
    var currentHotspotAnswer = $('tbody.smc-tbody input[type="radio"]:checked');

    var polygonStringCoord = '';
    for(index=0; index<polyCoordinates.length;index++) {

        var scaledX = Math.round(parseInt(polyCoordinates[index].x) * widthRatio);
        var scaledY = Math.round(parseInt(polyCoordinates[index].y) * heightRatio);

        polygonStringCoord += scaledX + ',' + scaledY + ';';
    }

    if (polygonStringCoord.match(/;/)) {
        polygonStringCoord = polygonStringCoord.substr(0, polygonStringCoord.length - 1);
    }

    $(currentHotspotAnswer).closest('tr').find('input[type="text"]').val(polygonStringCoord);
}

// draw ellipse
// param:
//      startX,startX   -> left top corner (start coordinates)
//      x,y             -> current mouse position coordinates
function drawEllipse(startX, startY, x, y, updateCoords) {
    updateCoords = updateCoords || false;
    var ctx = canvasToDraw.get(0).getContext("2d");
    ctx.clearRect(0, 0, canvasToDraw.get(0).width, canvasToDraw.get(0).height);

    var radiusX = (x - startX) * 0.5,   /// radius for x based on input
        radiusY = (y - startY) * 0.5,   /// radius for y based on input
        centerX = startX + radiusX,      /// calc center
        centerY = startY + radiusY,
        step = 0.01,                 /// resolution of ellipse
        a = step,                    /// counter
        pi2 = Math.PI * 2 - step;    /// end angle

    /// start a new path
    ctx.beginPath();
    ctx.fillStyle = "rgba(240, 3, 3, 0.5)";

    /// set start point at angle 0
    ctx.moveTo(centerX + radiusX * Math.cos(0),
        centerY + radiusY * Math.sin(0));

    /// create the ellipse
    for(; a < pi2; a += step) {
        ctx.lineTo(centerX + radiusX * Math.cos(a),
            centerY + radiusY * Math.sin(a));
    }

    /// close it and stroke it for demo
    ctx.fill();
    ctx.closePath();
    ctx.stroke();

    if (updateCoords) {
        updateCoordinatesInInputEllipse(centerX, centerY, radiusX, radiusY);
    }
}

function updateCoordinatesInInputEllipse(centerX, centerY, radiusX, radiusY) {
    var currentHotspotAnswer = $('tbody.smc-tbody input[type="radio"]:checked');

    var unScaledCenterX = Math.round(centerX * widthRatio);
    var unScaledCenterY = Math.round(centerY * heightRatio);
    var unScaledRadiusX = Math.round(radiusX * widthRatio);
    var unScaledRadiusY = Math.round(radiusY * heightRatio);

    var ellipseString = unScaledCenterX + ',' + unScaledCenterY + ',' + unScaledRadiusX + ',' + unScaledRadiusY;

    $(currentHotspotAnswer).closest('tr').find('input[type="text"]').val(ellipseString);
}

// draw circle
// param:
//      centerX,centerY -> center of circle
//      radius          -> radius of a circle
function drawCircle(centerX, centerY, radius, updateCoords) {
    updateCoords = updateCoords || false;
    var ctx = canvasToDraw.get(0).getContext("2d");

    ctx.clearRect(0, 0, canvasToDraw.get(0).width, canvasToDraw.get(0).height);
    ctx.beginPath();
    ctx.fillStyle = "rgba(240, 3, 3, 0.5)";
    ctx.arc(centerX, centerY, Math.round(radius), 0, Math.PI*2);
    ctx.fill();
    ctx.closePath();
    ctx.stroke();

    if (updateCoords) {
        updateCoordinatesInInputCircle(centerX, centerY, Math.round(radius));
    }
}

function updateCoordinatesInInputCircle(centerX, centerY, radius) {
    var currentHotspotAnswer = $('tbody.smc-tbody input[type="radio"]:checked');

    var unscaledCenterX = Math.round(centerX * widthRatio);
    var unscaledCenterY = Math.round(centerY * heightRatio);
    var unscaledRadius = Math.round(radius * widthRatio);

    var ellipseString = unscaledCenterX + ',' + unscaledCenterY + ',' + unscaledRadius;

    $(currentHotspotAnswer).closest('tr').find('input[type="text"]').val(ellipseString);
}

// draw rect
// param:
//      startX,startX   -> left top corner (start coordinates)
//      x,y             -> current mouse position coordinates
function drawRect(startX, startY, x, y, updateCoords) {
    updateCoords = updateCoords || false;
    var ctx = canvasToDraw.get(0).getContext("2d");
    ctx.clearRect(0, 0, canvasToDraw.get(0).width, canvasToDraw.get(0).height);
    ctx.beginPath();
    ctx.fillStyle = "rgba(240, 3, 3, 0.5)";
    ctx.strokeRect(startX, startY, (x-startX), (y-startY));
    ctx.fillRect(startX, startY, (x-startX), (y-startY));
    ctx.closePath();
    ctx.stroke();

    if (updateCoords) {
        updateCoordinatesInInputRect(startX, startY, x, y);
    }
}

function updateCoordinatesInInputRect(startX, startY, x, y) {
    var currentHotspotAnswer = $('tbody.smc-tbody input[type="radio"]:checked');

    var scaledStartX = Math.round(startX * widthRatio);
    var scaledStartY = Math.round(startY * heightRatio);
    var scaledEndX = Math.round(x * widthRatio);
    var scaledEndY = Math.round(y * heightRatio);

    var ellipseString = scaledStartX + ',' + scaledStartY + ';' + scaledEndX + ',' + scaledEndY;

    $(currentHotspotAnswer).closest('tr').find('input[type="text"]').val(ellipseString);
}

// get mouse pos relative to canvas
// param:
//      canvas
function getMousePos(canvas, evt) {
    var rect = canvas.get(0).getBoundingClientRect();
    return {
        x: Math.round(evt.clientX - rect.left),
        y: Math.round(evt.clientY - rect.top)
    };
}

// remove background image from canvas
$(document).on("click", "#removeImgLink", function () {
    removeImage();
    clearCoordinates();
    $('.hotspotImgSrc').get(0).value = "";
    polyCoordinates = [];
    //$(".hotspot-removeImgSrc").prop('checked', true);

});

// clear canvas
$(document).on("click", "#clearImgLink", function () {
    clearallCanvases();
});

// remove all shapes from all canvases
function clearallCanvases() {
    $('#parentOfCanvas .canvas').each(function(idx, canvas) {
        var context = canvas.getContext('2d');
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.beginPath();
    });

    clearCoordinates();

    polyCoordinates = [];
}

// clear invisible field by removing shapes
function clearCoordinates() {
    $('tbody.smc-tbody input[type="text"]').each(function () {
        $(this).val('');
    });
}
