function updateDateTime() {
    var now = new Date();
    var dayOfWeek = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'][now.getDay()];

    $('.header-time').html(dayOfWeek + ": " + now.toLocaleDateString() + " " + now.toLocaleTimeString() + " Uhr");
}

setInterval(function () {
    updateDateTime();
}, 1000);

$(document).ready(function(){
    $('[data-tooltip="tooltip"]').tooltip();
});

