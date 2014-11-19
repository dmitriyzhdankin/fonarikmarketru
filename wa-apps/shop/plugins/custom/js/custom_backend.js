function initRoundPriceButtons() {
    var currency_settings = setInterval(function(){
        if( $('#s-settings-currencies').length ) {
            $('.s-settings-currencies tr td.s-actions').width(70)
            $('#s-settings-currencies .s-settings-currency').each(function(key,el){
                $('<a href="javascript:void(0);" title="Round price"><i class="icon16 update"></i></a>').appendTo( $(el).find('.s-actions') );
            });
            clearInterval(currency_settings);
        }
    },100);
}
function roundPrice(e){
    var el = e.target;
    var code = $(el).parents('tr:first').attr('data-code');
    $.ajax({
        type: "POST",
        url: '?plugin=custom&action=saveRoundPrice',
        data: { code: code },
        dataType: 'json',
        success: function(data) {
            console.log(data);
        },
        error: function(e) {
            console.log(e);
        }
    });
}

function initProductCategories() {
     var product_page = setInterval(function(){
        if( $('#s-content').length ) {
            $('.s-product-categories').parent('.field').remove();
            clearInterval(product_page);
        }
    },100);
}
if(document.location.search == '?action=settings' /* && document.location.hash == '#/currencies/' */ ) {
    initRoundPriceButtons();
    $(document).on('click','#s-settings-currencies .s-settings-currency a',roundPrice);
}
if( true /*document.location.search == '?action=products'*/ /* && document.location.hash.match('#/product/[0-9]+/edit')*/ ) {
    initProductCategories();
    $(document).on('click','.hide_categories', function() {
        $('.categories_checkbox').hide();
        $('.change_categories').show();
        $('.categories_list').show();
        var cat_list = '';
        $("[name='product[categories][]']:checked").each(function() {
            cat_list += $(this).parent().text() + '<br/>';
        });
        $('.categories_list').each(function(){
            $(this).html(cat_list);
        });
        return false;
   });
   $(document).on('click','.change_categories',function() {
        $('.categories_list').hide();
        $('.change_categories').hide();
        $('.categories_checkbox').show();
        return false;
   });
}
/** Copy product */
$(document).on('click','#s-product-list-toolbar .copy-products',copyProducts);
function copyProducts() {
    var products = $.product_list.getSelectedProducts(true);
    
    if (!products.count) {
        alert($_('Please select at least one product'));
        return false;
    }
    $('.copy-products').addClass('hidden');
    $.shop.jsonPost('?plugin=custom&action=copyProducts', products.serialized, function(r) {
        console.log(r)
        $.product_list.sidebar.trigger('update', r.data.lists);
        $.products.dispatch();
        $('.copy-products').removeClass('hidden');
    });
    
    return false;
}

function initProductPageCopy() {
    var product_page = setInterval(function(){
    var product_name_el = '#s-product-save :input[name="product\[name\]"]';
    if( $(product_name_el).length ) {
            var product_name_value = $(product_name_el).val();
            if( product_name_value.match('(Copy)')) {
                $(document).on('blur',product_name_el,createUrl);
            }
            clearInterval(product_page);
        }
    },100);
}
function createUrl() {
    var url = $('#s-product-frontend-url-input').val();
    var target = $('#s-product-save').find(':input[name="product\\[url\\]"]');
    $.product.helper.urlHelper(this,target);
    
    var product_url = setInterval(function(){
        if(url != $('#s-product-frontend-url-input').val() ) {
            $('a.s-frontend-base-url').attr('href',$('span.s-frontend-base-url').text() + $('#s-product-frontend-url-input').val());
            $('#s-product-frontend-url').text($('#s-product-frontend-url-input').val());
            clearInterval(product_url);
        }
    },100);
}
var init_product_copy_page = false;
if(!init_product_copy_page) {
    init_product_copy_page = true;
    initProductPageCopy();
}
/** END Copy product */