

$(document).ready(function(){
    insert_new_item(0);
    $(document).on('change','.field-type select',change_item_type);
    $(document).on('click', '.item-add-child', click_add_new_item);
    $(document).on('change', '.field-delete input', click_delete_item);
    
    $('.product-autocomplete').autocomplete(autocomplete_options);
});

var autocomplete_options = {
    source: '?action=autocomplete',
    minLength: 3,
    delay: 300,
    select: function(event, ui) {
        var menu_item_id = $(this).attr('rel');
        $('#item_' + menu_item_id ).find('.field-url input').val(ui.item.id);
        $(this).val(ui.item.value);
        return false;
    }
};
function click_delete_item() {
    var item_id = $(this).attr('rel');
    var item = $('#item_' + item_id);
    
    if(this.checked) {
        item.find('.field-group').addClass('hidden');
        //item.find('.field-group .field-delete input').prop('checked', true);
    } else {
        item.find('.field-group').removeClass('hidden');
        //item.find('.field-group .field-delete input').prop('checked', false);
    }
}
function change_item_type() {
    var item_id = $(this).attr('rel');
    var item = $('#item_' + item_id);
    item.find('.field-title input').val('');
    item.find('.field-url input').val('');
    item.find('.field-title input').autocomplete( "destroy" );
    if( $(this).find(":selected").val() == 'product' ) {
        item.find('.field-title input').addClass('product-autocomplete').autocomplete(autocomplete_options);
        item.find('.field-url').hide();
        item.find('.field-add').hide();
        //remove all child
        item.find('.field-group').addClass('hidden');
        item.find('.field-group .field-delete input').prop('checked', true);
        
    } else if( $(this).find(":selected").val() == 'link' ) {
        item.find('.field-url').show();
        item.find('.field-add').show();
    }

}    
function click_add_new_item() {
   var parent_id = $(this).attr('rel');
   insert_new_item(parent_id);
}
function insert_new_item(parent_id) {
    var num = $('.item-list').find('.new-item').length + 1;
    var data = { 
        num : num,
        parent : parent_id
    };
    var parent_el = $('#item_' + parent_id );
    if( parent_id == 0 ) {
        parent_el = $('.item-list');
    }
    $('#add_item_template').tmpl(data).appendTo(parent_el);
    if(parent_id == 0 || $('#item_new_'+num).parent().find('.parent-id').val() != 0 ) {
        $('#item_new_'+num).find('.field-colunm').addClass('hidden');
        $('#item_new_'+num).find('.field-type').addClass('hidden');
    }
}