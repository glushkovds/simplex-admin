$(document).ready(function() {
    $('select:not(.cat-select)').select2();
    $('.cat-select').select2({
        tags: true,
        tokenSeparators: [',']
    });
});