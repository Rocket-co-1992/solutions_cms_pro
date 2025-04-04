function getNumericInput(input){
	var val = parseFloat(input.val().replace(/[^\d.-]/g, ''));
	return (val > 0) ? val : 0;
}
function setNumericInput(input, val){
	if(val > 0) input.val(Math.round(val*100)/100); else input.val(0);
}
/* =====================================================================
 * DOCUMENT READY
 * =====================================================================
 */
$(document).ready(function(){
	'use strict';
    
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
            $(this).find('input').not('.noreset')
                .filter(':text, :password, :file').val('')
                .end()
                .filter(':checkbox, :radio').prop('checked', false).removeAttr('checked');

            $(this).find('textarea').not('.noreset').val('');

            $(this).find('select').not('.noreset')
                .prop('selectedIndex', -1)
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
						$('.field-notice',form).html('').hide().parent().removeClass('error');
						$('.field-notice',form).parent().find('input, textarea, select').removeClass('is-invalid');
						$('.alert.alert-danger').html('').hide();
						$('.alert.alert-success').html('').hide();
                    }
                    
                    var jsonResponse = (typeof response === 'object') ? response : $.parseJSON(response);
                    
                    if(targetCont != '') $(targetCont).removeClass('loading-ajax');
                    
                    if(jsonResponse.error != '') $('.alert.alert-danger', form).html(jsonResponse.error).slideDown();
                    else if(jsonResponse.redirect != '' && jsonResponse.redirect != undefined) window.location.href = jsonResponse.redirect;
                    else if(refresh === true){
                        var href = window.location.href;
                        window.location = href.substr(0, href.lastIndexOf('#'));
                     }
                    if(jsonResponse.success != ''){
                        $('.alert.alert-success', form).html(jsonResponse.success).slideDown();
                        if(clear && jsonResponse.error == '' && jsonResponse.notices.length == 0)
                            form.clear();
                    }
                    
                    if(!$.isEmptyObject(jsonResponse.notices)){
                        if(targetCont != "") $(targetCont).hide();
                        $.each(jsonResponse.notices, function(field,notice){
                            var elm = $('.field-notice[rel="'+field+'"]', form);
                            if(elm.get(0) !== undefined){
                                var parent = elm.parent();
                                elm.html(notice).fadeIn('slow');
                                parent.addClass('error');
                                parent.find('input, textarea, select').addClass('is-invalid');
                            }
                        });
                        $('.captcha_refresh', form).trigger('click');
                    }else{
                        if(targetCont != ''){
                            $(targetCont).html(jsonResponse.html);
                            if($('.open-popup-link').length){
                                $('.open-popup-link').magnificPopup({
                                    type:'inline',
                                    midClick: true
                                });
                            }
                            if($('.selectpicker').length) $('.selectpicker').selectpicker('refresh');
                        }
                        if(extraTarget != '')
                            $(extraTarget).html(jsonResponse.extraHtml);
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
            
            if((e.type == 'click' && ((tagName == 'INPUT' && (elm.attr('type') == 'submit' || elm.attr('type') == 'image')) || tagName == 'A' || tagName == 'BUTTON')) || e.type == 'change') {
                var targetCont = elm.data('target');
                var refresh = elm.data('refresh') ?? false;
                var clear = elm.data('clear') ?? true;
                var form = elm.parents('form.ajax-form');
                
                // Ajouter ici la logique de récupération du token reCAPTCHA
                if (typeof grecaptcha !== 'undefined') {
                    var pKey = $('meta[name="recaptcha_pkey"]').attr('content');
                    grecaptcha.ready(function() {
                        grecaptcha.execute(pKey, { action: 'homepage' }).then(function(token) {
                            // Assigner le token à l'input hidden
                            form.find('#g-recaptcha-response').val(token);
                            
                            // Maintenant soumettre le formulaire via AJAX
                            if(targetCont != "") $(targetCont).html('').addClass('loading-ajax').show();
                            sendAjaxForm(form, elm.data('action'), targetCont, refresh, clear, elm.data('extratarget'), onload);
                        });
                    });
                } else {
                    // Si reCAPTCHA n'est pas défini, soumettre directement
                    if(targetCont != "") $(targetCont).html('').addClass('loading-ajax').show();
                    sendAjaxForm(form, elm.data('action'), targetCont, refresh, clear, elm.data('extratarget'), onload);
                }
                
                //if(tagName == 'A') return false;
            } else {
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
