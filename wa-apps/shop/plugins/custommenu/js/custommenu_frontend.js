$(document).ready(function(){
    var dropdowns = $('.custom-menu .dropdown_3columns');

    $.each(dropdowns,function(index,el) {
        var menu_li = $(el).parent();
        var menu_ul = $(el).parent().parent();
        $(el).width( $(el).find('.col_1').length * (280 + 10) ) ;
        
        
        var mrg = ( $(menu_ul).offset().left + $(menu_ul).width() - ( $(el).width() + $(el).parent().offset().left ) );
        $(el).css('margin-left',Math.min(mrg,0)+'px');
        //$(el).parent().parent().offset()
        //$(el).parent().offset()
        //$(el).parent().parent().width()
    });
});