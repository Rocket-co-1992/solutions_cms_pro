(function($){

    $.lazyLoader = {
		defaults: {
			loader: 'get_data.php',
			total: 0,
			limit: 50,
            mode: 'scroll',
            more_caption: 'Load more',
            variables: '',
            isIsotope: false
		}
	};
    
    $.fn.extend({
		
		lazyLoader : function(settings){
		
			var elems = this;
			var s = $.extend({}, $.lazyLoader.defaults, settings);
            
            _loadPlugins = function(elem){
                if($('a.image-link', elem).length){
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
                if($('.img-container', elem).length){
                    $('.img-container').imagefill();
                }
            }
                
            elems.each(function(){
				
                var Obj = {
                    elem: $(this),
                    offset: 0,
                    loading: false,
                    oldscroll: 0,
                    img_loading: null,
                    _init: function(){
                        this.more_wrapper = $('<div class="lazy-more-wrapper">').appendTo(this.elem);
                        if(s.mode == 'scroll')
                            this.img_loading = $('<div class="lazy-img-loading">').appendTo(this.more_wrapper);
                        if(s.mode == 'click')
                            this.more_btn = $('<a class="lazy-more-btn btn btn-secondary">'+s.more_caption+'</a>').appendTo(this.more_wrapper);
                    },
                    _load_content: function(){
						var obj = this;
                        $.ajax({
                            'url' : s.loader,
                            'type' : 'post',
                            'data' : 'offset='+obj.offset+'&limit='+s.limit+'&ajax=1&'+s.variables,
                            success:function(data){
                                var data = $.parseJSON(data);
                                obj.elem.removeClass('loading');
                                
                                if(s.isIsotope){
                                    $(data.html).imagesLoaded(function(){
                                        $('.isotope').isotope('insert', $(data.html), function(){
                                            _loadPlugins($(data.html));
                                        });
                                    });
                                }else{
                                    $(data.html).hide().appendTo(obj.elem).fadeIn(1000);
                                    _loadPlugins($(data.html));
                                }
                                
                                obj.offset += s.limit;
                                obj.more_wrapper.fadeOut();
                                obj.loading = false;
                            }
                        });	
                    }
                }
                Obj._init();
                Obj._load_content();

                $(window).scroll(function(){
                    
                    if(($(window).scrollTop() + $(window).height() >= Obj.elem.offset().top + Obj.elem.height()) && (Obj.offset < s.total)){
                        if(!Obj.loading){
                            Obj.loading = true;
                            Obj.elem.addClass('loading');
                            Obj.more_wrapper.fadeIn();
                            
                            if(s.mode == 'scroll'){
                                setTimeout(function(){
                                    Obj._load_content();
                                },500);
                            }
                            if(s.mode == 'click'){
                                Obj.more_btn.unbind('click').click(function(){
                                    Obj._load_content();
                                });
                            }
                        }
                    }
                });
            });
        }
    });
})(jQuery);
