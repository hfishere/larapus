$(document).ready(function() {
    // Confirm delete
    $(document.body).on('submit', '.js-confirm', function() {
        var $el = $(this)
        var text = $el.data('confirm') ? $el.data('confirm') : 'Anda yakin ingin melakukan tindakan ini?'
        var c = confirm(text);
        return c;
    });
});