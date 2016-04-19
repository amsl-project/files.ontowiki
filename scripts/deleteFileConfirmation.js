$(document).ready(function() {
    $('.delete-item-atag').click(function(event) {
        var text = $('.delete-item-atag').attr('name');
        var ok = confirm(text);
        if(!ok){
            event.preventDefault();
            event.stopPropagation();
            return;
        }
    });
});
