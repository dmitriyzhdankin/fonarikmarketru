function slidesSortInit(){
    $("#sort-slides").sortable({
        axis: 'y',
        update: function(event, ui){
            var sorted = $("#sort-slides").sortable("serialize");
            $.post('?module=ajax&action=sortSlider', sorted, '', 'json');
        }
    });
}
function addSlider(){
    $("#slider-add").waDialog({onSubmit: function (d) {
        $.post('?module=ajax&action=addSlider', $(this).serialize(), function (r) {
           d.trigger('close');
           if(r.data.id > 0) document.location.href = '?id=' + r.data.id;
        }, 'json');
        return false;
    }});
}
function deleteSlider(el){
    var slider = $(el).attr('rel');
    $('<div><h2>'+di_loc['delete_this_slider']+' ?</h2><br /></div>').waDialog({
    'buttons': '<input type="submit" value="'+di_loc['delete']+'" class="button red"> '+di_loc['or']+' <a href="javascript:void(0)" class="inline-link cancel"><b><i>'+di_loc['cancel']+'</i></b></a>',
    'width' : '350px',
    'height' : '150px',
    'onSubmit': function (d) {
            $.post('?module=ajax&action=deleteSlider', {sid: slider}, function (r) {
                   if(r.data.sid > 0) document.location.href = '?id=' + r.data.sid;
                }, 'json');
                d.trigger('close');
                return false;
        }
    });
}
function saveSlide(el){
    $(document).unbind('keyup');
    $.post('?module=ajax&action=saveSlide', $(el).serialize(), function (r) {
        if(r.data.errors == 0) greenButton(el);
    }, 'json');
}
function saveOptions(el){
    $.post('?module=ajax&action=saveOptions', $(el).serialize(), function (r) {
        if(r.data.sid > 0) document.location.href = '?id=' + r.data.sid;
    }, 'json');
}
function addImage(s){
    var url = '?module=ajax&action=addImage';
    var form = '#upload-image';
    var dwindow = '#image-add';
    var pref = 'i-';
    if(s > 0){
        form = '#upload-slide';
        dwindow = '#image-add-to-slider';
        pref = 's-';
    }
    $(dwindow).waDialog({
        onLoad: function () {
            var bar = $('#'+pref+'bar');
            var percent = $('#'+pref+'percent');
            var status = $('#'+pref+'status');
            var result = '';
            var sid = 0;
            $(form).ajaxForm({
                type: 'POST',
                url: url,
                dataType: 'json',
                beforeSend: function() {
                    $(dwindow).find("input[type=submit]").attr('disabled', 'disabled');
                    var percentVal = '0%';
                    $('#'+pref+'input-file-block').css('display', 'none');
                    status.css('display', 'block');
                    bar.css('width', percentVal);
                    percent.html(percentVal);
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percentVal = percentComplete + '%';
                    bar.css('width', percentVal);
                    percent.html(percentVal);
                },
                success: function(r) {
                    var percentVal = '100%';
                    bar.css('width', percentVal);
                    percent.html(percentVal);
                    if(r.data.result){
                        if(r.data.sid > 0)
                            sid = r.data.sid;
                        result = r.data.result;
                        if(r.data.errors > 0)
                            result += ', ' + r.data.errHTML;
                        $('#'+pref+'result').html(result);
                        if(r.data.errors > 0)
                            $("#upload-errors").click(function(){
                                if($('#error-details').hasClass('display-none')){
                                    $('#error-details').removeClass('display-none');
                                    $('#error-details').siblings('i')
                                            .removeClass('rarr').addClass('darr');
                                }else{
                                    $('#error-details').addClass('display-none');
                                    $('#error-details').siblings('i')
                                            .removeClass('darr').addClass('rarr');
                                }
                            });
                    }
                },
                complete: function(xhr) {
                    $('.dialog-buttons-gradient').html('<input type="button" value="OK" class="button green">');
                    $('.dialog-buttons-gradient').click(function(){
                        $(dwindow).trigger('close');
                        if(sid > 0) document.location.href = '?id=' + sid;
                        else document.location.href = $('#all-images').attr('href');
                    });
                }
            });
            return false;
        }
    });
}
function addImagesToSlider(){
    if($("#exist-sliders").length > 0){
        $("#image-add-to-slider").waDialog({onSubmit: function (d) {
            var fdata = $(this).serialize();
            var imgs = new Array();
            $(".chk-images:checked").each(function(){
                imgs.push($(this).attr("value"));
            });
            var count = imgs.length;
            var step = parseInt(100/count);
            var succ = 0;
            var errors = 0;
            var bar = $('#s-bar');
            var percent = $('#s-percent');
            var status = $('#s-status');
            var percentVal = 0;
            var msg = '';
            $("#image-add-to-slider").find("input[type=submit]").attr('disabled', 'disabled');
            $('#s-input-file-block').css('display', 'none');
            status.css('display', 'block');
            bar.css('width', percentVal+'%');
            percent.html(percentVal+'%');
            for(i=0; i<count; i++){
                $.ajax({
                    type: "POST",
                    url: '?module=ajax&action=addImageToSlider&image_id='+imgs[i],
                    data: fdata,
                    dataType: 'json',
                    async: 'false',
                    success: function(r){
                        if(r.data.errors > 0) errors++;
                        else succ++;
                        percentVal += step;
                        bar.animate({'width': percentVal+'%'}, 350);
                        percent.html(percentVal+'%');
                        msg = di_loc['Processed']+': '+succ;
                        if(errors > 0) msg += ', '+di_loc['Errors']+': '+errors;
                        $('#s-result').html(msg);
                        if(succ+errors == count){
                            percentVal = '100';
                            bar.animate({'width': percentVal+'%'}, 250);
                            percent.html(percentVal+'%');
                            $('.dialog-buttons-gradient').html('<input type="button" value="OK" class="button green">');
                            $('.dialog-buttons-gradient').click(function(){
                                d.trigger('close');
                                document.location.href = $('#all-images').attr('href');
                            });
                        }
                    }
                });
            }
            return false;
        }});
    }else{
        alert(di_loc['no_sliders_exist']);
    }
}
function previewSlide(el){
    var url = $(el).attr('rel');
    var sname = $(el).children('img').attr('alt');
    var parts = url.split('/');
    var fname = parts[parts.length-1];
    parts = fname.split('.');
    var sizes = parts[1].split('x');
    var width = parseInt(sizes[0])+40;
    var height = parseInt(sizes[1])+50;
    $('<div id="slide-preview"><img src="'+url+'" /></div>').waDialog({
    'buttons': '<input type="submit" value="OK" class="button green"><div id="filename">'+sname+'</div>',
    'width' : width+'px',
    'height' : height+'px',
    'onSubmit': function (d) {
                    d.trigger('close');
                    return false;
                }
    });
}
function previewImage(el){
    var url = $(el).attr('rel');
    var sname = $(el).children('img').attr('alt');
    var width = 680;
    var height = 530;
    $('<div id="slide-preview"><img src="'+url+'" /></div>').waDialog({
    'buttons': '<input type="submit" value="OK" class="button green"><div id="filename">'+sname+'</div>',
    'width' : width+'px',
    'height' : height+'px',
    'onSubmit': function (d) {
                    d.trigger('close');
                    return false;
                }
    });
}
function greenButton(el){
    var formID = $(el).attr('id');
    var butt = $('#'+formID).children('.slide-save-field').children('.slide-save').children('.button');
    $(butt).removeClass('yellow').addClass('green');
}
function yellowButton(butt){
    $(butt).removeClass('green').addClass('yellow');
}
function valueChanged(el){
    var formID = $(el).closest('form').attr('id');
    var fieldID = $(el).attr('id');
    var butt = $('#'+formID).children('.slide-save-field').children('.slide-save').children('.button');
    $(document).keyup(function(){
        var dval = $('#'+fieldID).siblings('input').attr('value');
        var txt = $('#'+fieldID).val();
        if(dval !== txt) yellowButton(butt);
    });
}
function deleteSlide(el){
    var form = $(el).closest('form').attr('id');
    var slider = $('#'+form).attr('rel');
    var slide = $(el).attr('rel');
    var title = $('#'+form).find('.slide-filename').html();
    $('<div><h2>'+di_loc['delete_this_slide']+' ?</h2><br /><b>'+title+'</b></div>').waDialog({
    'buttons': '<input type="submit" value="'+di_loc['delete']+'" class="button red"> '+di_loc['or']+' <a href="javascript:void(0)" class="inline-link cancel"><b><i>'+di_loc['cancel']+'</i></b></a>',
    'width' : '350px',
    'height' : '150px',
    'onSubmit': function (d) {
            $.post('?module=ajax&action=deleteSlide', {id: slide, sid: slider}, function (r) {
                   if(r.data.sid > 0) document.location.href = '?id=' + r.data.sid;
                }, 'json');
                d.trigger('close');
                return false;
        }
    });
}
function deleteImages(){
    var html = '';
    var count = $(".chk-images:checked").length;
    $(".chk-images:checked").each(function(){
        html += '<li> '+$(this).parent().siblings('a').attr('title')+'</li>';
    });
    $('<div><h2>'+di_loc['delete_this_images']+' ('+count+') ?</h2><ol>'+html+'</ol></div>').waDialog({
    'buttons': '<input type="submit" value="'+di_loc['delete']+'" class="button red"> '+di_loc['or']+' <a href="javascript:void(0)" class="inline-link cancel"><b><i>'+di_loc['cancel']+'</i></b></a>',
    'width' : '350px',
    'height' : '150px',
    'onSubmit': function (d) {
        $.post('?module=ajax&action=deleteImages', $("#images-form").serialize(), function (r) {
               if(r.data.errors == 0) document.location.href = $('#all-images').attr('href');
            }, 'json');
            d.trigger('close');
            return false;
        }
    });
}
$(document).ready(function() {
    if($("#c-core").innerHeight() > $("#options").height())
        $("#options").css('min-height', $("#c-core").innerHeight());

    $("#slider-add-link").click(function(e){
        e.preventDefault();
        addSlider();
    });

//Get Help from Official site: www.difabrik.ru
    $("#view-help").click(function(e){
        e.preventDefault();
        //var view = $(this).attr('rel');
        var url = 'http://www.difabrik.ru/dislider/?view=help';
        $('<iframe src="'+ url +'" width="600" height="400" frameborder="0"></iframe>').waDialog({
        'buttons': '<input type="submit" value="OK" class="button green">',
        'width' : '640px',
        'height' : '480px',
        'onSubmit': function (d) {
                        d.trigger('close');
                        return false;
                    }
        });
    });

//All Images View
    if($("#images-add-link")){
        $("#images-add-link").click(function(e){
            e.preventDefault();
            addImage('0');
        });
        $("#images-to-slider").click(function(e){
            e.preventDefault();
            if($(".chk-images:checked").length > 0){
                addImagesToSlider();
            }else{
                alert(di_loc['nothing_selected']);
            }
        });
        $("#images-delete").click(function(e){
            e.preventDefault();
            if($(".chk-images:checked").length > 0){
                deleteImages();
            }else{
                alert(di_loc['nothing_selected']);
            }
        })
        if($(".image-preview")){ //All Images View with existing images
            $(".image-preview").click(function(e){
                e.preventDefault();
                previewImage(this);
            });
            $(".chk-images:checked").each(function(){
                $(this).parents('li').addClass('selected');
            });
            $(".chk-images").click(function(){
                $(this).parents('li').toggleClass('selected');
            });
        }

    }

//Slider View
    if($("#slide-add-link")){
        $("#slide-add-link").click(function(e){
            e.preventDefault();
            addImage('1');
        });
        $("#slider-delete").click(function(e){
            e.preventDefault();
            deleteSlider(this);
        });
        $("#itype").change(function(){
            $(".slide-opts").each(function(){
                if($(this).attr('rel') == $("#itype").val()){
                    $(this).css('display', 'block');
                }else{
                    $(this).css('display', 'none');
                }
            });
        });
        $(".opts-form").submit(function(e){
            e.preventDefault();
            saveOptions(this);
        });
        if($(".slide-value")){ //Slider View with existing slides
            $(".slide-value input").focus(function(e){
                e.preventDefault();
                valueChanged(this);
            });
            $(".slide-value textarea").focus(function(e){
                e.preventDefault();
                valueChanged(this);
            });
            $(".slide-delete-link").click(function(e){
                e.preventDefault();
                deleteSlide(this);
            });
            $(".form-slide").submit(function(e){
                e.preventDefault();
                saveSlide(this);
            });
            $(".slide-preview").click(function(e){
                e.preventDefault();
                previewSlide(this);
            });
            slidesSortInit();
        }
    }
});
$(window).load(function() {
    if($("#c-core").innerHeight() > $("#options").height())
        $("#options").css('min-height', $("#c-core").innerHeight());
});
