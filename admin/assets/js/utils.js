function pms_getNumericInput(input){
    let val = input.val();
    if(val !== null && val !== undefined) {
	    val = parseFloat(input.val().replace(/[^\d.-]/g, ''));
    }
	return (val > 0) ? val : 0;
}
function pms_setNumericInput(input, val){
	if(val > 0) input.val(Math.round(val*100)/100); else input.val(0);
}

$(document).ready(function(){
	'use strict';

    $('[data-toggle="tooltip"]').tooltip();
        
    $(window).on('resize', function(){
        var h = $(this).height() - 50;
        $('.side-nav').css('max-height', h);
    });
    $(window).trigger('resize');

    /**
     * Thumbnails in the listing of a module
     */
    $('.wrap-img img').each(function() {
        var i = $(this),
            mw = 160,
            mh = 36,
            iw = i[0].naturalWidth,
            ih = i[0].naturalHeight;

        if (iw <= mw && ih <= mh) {
            i.css({
                'width': iw + 'px',
                'height': ih + 'px',
                'margin-left': 0,
                'margin-top': 0,
                'position': 'relative'
            });
        } else if ((iw / mw) > (ih / mh)) {
            i.css({
                'height': mh + 'px',
                'width': 'auto',
                'margin-left': -(i.width() - mw) / 2 + 'px'
            });
        } else {
            i.css({
                'width': mw + 'px',
                'height': 'auto',
                'margin-top': -(i.height() - mh) / 2 + 'px'
            });
        }
    });

    /**
     * Overlay toggling
     */
    $('#overlay').fadeOut();

    $('form').on('submit', function(){
        $('#overlay').addClass('loading');
    });

    /**
     * Password controls management
     */
    if($('.pr-password').length) {
        $('.pr-password').passwordRequirements();
    }

    $('.toggle-password').on('click', function() {

        if (!$(this).hasClass('active')) {
            $(this).addClass('active');
            $(this).siblings('input[type="password"]').attr('type', 'text');
        } else {
            $(this).removeClass('active');
            $(this).siblings('input[type="text"]').attr('type', 'password');
        }
    });

    /**
     * Switch controls management
     */
    $('.form-switch .form-check-input').each(function () {
        const hiddenCheckbox = $('<input>')
            .attr('type', 'checkbox')
            .attr('name', $(this).attr('name'))
            .val('0')
            .hide();

        $(this).before(hiddenCheckbox);

        $(this).on('change', function () {
            if ($(this).is(':checked')) {
                hiddenCheckbox.prop('checked', false);
            } else {
                hiddenCheckbox.prop('checked', true);
            }
        }).trigger('change');
    });

    /* =================================================================
     * MAGNIFIC POPUP
     * =================================================================
     */
	if($('a.image-link').length){
        $('a.image-link').magnificPopup({
            type:'image',
            mainClass: 'mfp-with-zoom',
            gallery:{
                enabled: true 
            },
            zoom: {
                enabled: true
            }
        });
	}
    if($('.ajax-popup-link').length){
        $('.ajax-popup-link').each(function(){
            $(this).magnificPopup({
                type: 'ajax',
                ajax: {
                    settings: {
                        method: 'POST',
                        data: $(this).data('params')
                    }
                }
            });
        });
    }
    
    /* =================================================================
     * ALERT
     * =================================================================
     */ 
	$('.alert').delegate('button', 'click', function(){
		$(this).parent().fadeOut('fast');
	});
	
	/* =================================================================
     * AJAX / FORM
     * =================================================================
     */
    if($('form.ajax-form').length){
        $.fn.clear = function(){
            $(this)
                .find('input').not('.noreset')
                    .filter(':text, :password, :file, :hidden').val('').end()
                    .filter(':checkbox, :radio').prop('checked', false).removeAttr('checked').end()
                .end()
                .find('textarea').not('.noreset').val('').end()
                .find('select').not('.noreset').prop('selectedIndex', -1).prop('selected', false)
                    .find('option:selected').removeAttr('selected');

            return this;
        };
        function sendAjaxForm(form, action, targetCont, refresh, clear, extraTarget, onload){
            var posQuery = action.indexOf('?');
            var extraData = '';
            if(posQuery != -1){
                extraData = action.substr(posQuery+1);
                if(extraData != '') extraData = '&'+extraData;
                action = action.substr(0, posQuery);
            }
            $.ajax({
                url: action,
                type: form.attr('method'),
                data: form.serialize()+extraData,
                success: function(response){
                    if(onload != 1){
						$('.field-notice',form).html('').hide().parent().removeClass('alert alert-danger');
						$('.alert.alert-danger').html('').hide();
						$('.alert.alert-success').html('').hide();
                    }
                    
                    var response = $.parseJSON(response);
                    
                    if(targetCont != '') $(targetCont).removeClass('loading-ajax');
                    
                    if(response.error != '') $('.alert.alert-danger', form).html(response.error).slideDown();
                    else if(response.redirect != '' && response.redirect != undefined) window.location.href = response.redirect;
                    else if(refresh === true){
                        var href = window.location.href;
                        window.location = href.substr(0, href.lastIndexOf('#'));
                     }
                    if(response.success != ''){
                        $('.alert.alert-success', form).html(response.success).slideDown();
                        if(clear && response.error == '' && response.notices.length == 0)
                            form.clear();
                    }
                    
                    if(!$.isEmptyObject(response.notices)){
                        if(targetCont != "") $(targetCont).hide();
                        $.each(response.notices, function(field,notice){
                            var elm = $('.field-notice[rel="'+field+'"]', form);
                            if(elm.get(0) !== undefined) elm.html(notice).fadeIn('slow').parent().addClass('alert alert-danger');
                        });
                        $('.captcha_refresh', form).trigger('click');
                    }else{
                        if(targetCont != ''){
                            $(targetCont).html(response.html);
                            $('.open-popup-link').magnificPopup({
                                type:'inline',
                                midClick: true
                            });
                            $('.selectpicker').selectpicker('refresh');
                        }
                        if(extraTarget != '')
                            $(extraTarget).html(response.extraHtml);
                    }
                    
                    if($('.alert:visible', form).length){
                        
                        var scroll_1 = $('html, body').scrollTop();
                        var scroll_2 = $('body').scrollTop();
                        var scrolltop = scroll_1;
                        if(scroll_1 == 0) scrolltop = scroll_2;
                        
                        var scrolltop2 = $('.alert:visible:first', form).offset().top - 80;
                        if(scrolltop2 < scrolltop) $('html, body').animate({scrollTop: scrolltop2+'px'});
                    }
                } 
            });
        }
        $('form.ajax-form').on('click change', '.sendAjaxForm', function(e){
            e.defaultPrevented;
            var elm = $(this);
            var onload = elm.attr('data-sendOnload');
            var tagName = elm.prop('tagName');
            if((e.type == 'click' && ((tagName == 'INPUT' && (elm.attr('type') == 'submit' || elm.attr('type') == 'image')) || tagName == 'A' || tagName == 'BUTTON')) || e.type == 'change'){
                var targetCont = elm.data('target');
                var refresh = elm.data('refresh');
                var clear = elm.data('clear');
                if(targetCont != "") $(targetCont).html('').addClass('loading-ajax').show();
                sendAjaxForm(elm.parents('form.ajax-form'), elm.data('action'), targetCont, refresh, clear, elm.data('extratarget'), onload);
                //if(tagName == 'A') return false;
            }else{
                //if(tagName == 'A') return false;
            }
        });
        $('.submitOnClick').on('click', function(e){
            e.defaultPrevented;
            $(this).parents('form').submit();
            return false;
        });
        $('.sendAjaxForm[data-sendOnload="1"]').trigger('change');
    }
    if($('a.ajax-link').length){
        $('a.ajax-link').on('click', function(e){
            e.defaultPrevented;
            var elm = $(this);
            var href = elm.attr('href');
            $.ajax({
                url: elm.data('action'),
                type: 'get',
                success: function(response){
                    if(href != '' && href != '#') $(location).attr('href', href);
                } 
            });
            return false;
        });
    }
});
