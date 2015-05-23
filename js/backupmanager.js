
function purge_selectall(filearea) {
    $('.'+filearea+'sel').attr('checked', 'checked');
    $('#'+filearea+'-submit').attr('disabled', false);
    $('#'+filearea+'-old-submit').attr('disabled', false);
}

function purge_deselectall(filearea) {
    $('.'+filearea+'sel').attr('checked', null);
    $('#'+filearea+'-submit').attr('disabled', true);
    $('#'+filearea+'-old-submit').attr('disabled', true);
}

function purge_checkstate(filearea) {
    if ($('.'+filearea+'sel:checked').length) {
        $('#'+filearea+'-submit').attr('disabled', false);
        $('#'+filearea+'-old-submit').attr('disabled', false);
    } else {
        $('#'+filearea+'-submit').attr('disabled', true);
        $('#'+filearea+'-old-submit').attr('disabled', true);
    }
}
