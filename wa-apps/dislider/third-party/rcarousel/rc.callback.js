function generateDots() {
    var total, i, link;
    var sid = $(this).attr('id');
    var wrapper = $("#"+sid).siblings(".pages");
    total = $(this).rcarousel("getTotalPages");
    for(i = 0; i < total; i++){
        link = $("<a class='rcarousel-dot' href='#'></a>");
        $(link).bind("click", {page: i}, function(event){
                $('#'+sid).rcarousel("goToPage", event.data.page);
                event.preventDefault();
            }
        )
        .addClass("off")
        .appendTo(wrapper);
    }
    if($(this).parent().hasClass('horizontal')){
        var pw = $("#"+sid).siblings('.pages').innerWidth()/2;
        $("#"+sid).siblings('.pages').css('margin-left', '-'+pw+'px');
    }else{
        var ph = $("#"+sid).siblings('.pages').innerHeight()/2;
        $("#"+sid).siblings('.pages').css('margin-top', '-'+ph+'px');
    }
    $("a:eq(0)", wrapper).removeClass("off").addClass("on");
}

function generateNumbs() {
    var total, i, link;
    var sid = $(this).attr('id');
    var wrapper = $("#"+sid).siblings(".pages");
    total = $(this).rcarousel("getTotalPages");
    for(i = 0; i < total; i++){
        link = $("<a class='rcarousel-numb' href='#'>"+parseInt(i+1)+"</a>");
        $(link).bind("click", {page: i}, function(event){
                $('#'+sid).rcarousel("goToPage", event.data.page);
                event.preventDefault();
            }
        )
        .addClass("off")
        .appendTo(wrapper);
    }
    if($(this).parent().hasClass('horizontal')){
        var pw = $("#"+sid).siblings('.pages').innerWidth()/2;
        $("#"+sid).siblings('.pages').css('margin-left', '-'+pw+'px');
    }else{
        var ph = $("#"+sid).siblings('.pages').innerHeight()/2;
        $("#"+sid).siblings('.pages').css('margin-top', '-'+ph+'px');
    }
    $("a:eq(0)", wrapper).removeClass("off").addClass("on");
}

function pageLoaded( event, data ) {
    var sid = $(this).attr('id');
    var wrapper = $("#"+sid).siblings(".pages");
    $("a.on", wrapper).removeClass("on").addClass("off");
    $("a", wrapper).eq(data.page).removeClass("off").addClass("on");
}