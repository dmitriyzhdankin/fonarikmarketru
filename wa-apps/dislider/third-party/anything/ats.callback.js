function showDesc(slider){
    var desc_wrapper = $(slider.el).closest('.atSlider');
    var page_num = slider.currentPage;
    var page = $(slider.el).find('li').eq(page_num);
    var desc_block = $(page).children('.ats-desc-wrapper');
    if($(desc_block).length){
        if($(desc_block).length){
            width = desc_block.width();
            height = desc_block.height();
            if($(desc_wrapper).hasClass('desc-left')){
                $(desc_block).animate({"left": "0"}, 300);
            }else if($(desc_wrapper).hasClass('desc-right')){
                $(desc_block).animate({"right": "0"}, 300);
            }else if($(desc_wrapper).hasClass('desc-top')){
                $(desc_block).animate({"top": "0"}, 300);
            }else{
                $(desc_block).animate({"bottom": "0"}, 300);
            }
        }
    }
}
function closeDescs(slider){
    var desc_wrapper = $(slider.currentTarget).closest('.atSlider');
    $(slider.currentTarget).find('li').each(function(){
        var desc_block = $(this).children('.ats-desc-wrapper');
        var width = 0;
        var height = 0;
        if($(desc_block).length){
            width = desc_block.width();
            height = desc_block.height();
            if($(desc_wrapper).hasClass('desc-left')){
                $(desc_block).animate({"left": width * -1}, 100);
            }else if($(desc_wrapper).hasClass('desc-right')){
                $(desc_block).animate({"right": width * -1}, 100);
            }else if($(desc_wrapper).hasClass('desc-top')){
                $(desc_block).animate({"top": height * -1}, 100);
            }else{
                $(desc_block).animate({"bottom": height * -1}, 100);
            }
        }
    });
}
