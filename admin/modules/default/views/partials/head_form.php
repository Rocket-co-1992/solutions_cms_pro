<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.3.4/typeahead.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.3.4/typeahead.bundle.min.js"></script>

<?php
if(EDITOR_TYPE == 'builder') { ?>
    <script type="importmap">
        {
            "imports": {
                "ckeditor5Balloon": "./assets/js/plugins/jquery-pageflow/libs/ckeditor5Balloon/ckeditor5.js",
                "ckeditor5Balloon/": "./assets/js/plugins/jquery-pageflow/libs/ckeditor5Balloon/"
            }
        }
    </script>
    <link rel="stylesheet" href="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/assets/js/plugins/jquery-pageflow/css/jquery-pageflow.min.css">
    <script src="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/assets/js/plugins/jquery-pageflow/js/jquery-pageflow.min.js" type="module"></script>
    <script>
        $(function(){
            'use strict';

            $('.pageflow').pageflow({
                uploadUrl: '<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/handlers/pflow_media_uploader.php', // script called when a media is added
                removeUrl: '<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/handlers/pflow_media_remover.php', // script called when a media is removed
                showExportBtn: false, // whether to show export button
                exportToInput: true // export into a text field
            });
        });
    </script>
    <?php
} else { ?>
    <link rel="stylesheet" href="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/assets/js/plugins/ckeditor5/style.css">
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.1/ckeditor5.css">
    <script type="importmap">
        {
            "imports": {
                "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/42.0.1/ckeditor5.js",
                "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/42.0.1/"
            }
        }
    </script>
    <script type="module" src="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/assets/js/plugins/ckeditor5/config.js"></script>
    <?php
} ?>

<script>
    var typeahead_opts = new Array();
    function pms_init_typeahead(input){
        var target = input.attr('rel');
        if(typeahead_opts[target]) {
            if (typeof typeahead_opts[target].limit === 'undefined') {
                typeahead_opts[target].limit = 10;
            }
            input.typeahead({
                highlight: true,
                minLength: 0
            }, typeahead_opts[target]);
        }
    }
    function pms_init_datepicker(input){
        var target = input.attr('rel');
        if(input.attr('readonly') != 'readonly'){
            $(input).datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: null
            });
        }
    }
        
    $(function(){
        'use strict';
        
        $('.typeahead').each(function(){
            var input = $(this);
            var target = input.attr('rel');
        });
        
        $('select[name="multiple_actions_file"]').on('change', function(){
            if(($(this).val() == 'delete_multi_file' && confirm('<?php echo $adminContext->texts['DELETE_CONFIRM1'] . " " . $adminContext->texts['LOOSE_DATAS']; ?>'))
            || ($(this).val() != 'delete_multi_file' && confirm('<?php echo $adminContext->texts['ACTION_CONFIRM'] . " " . $adminContext->texts['LOOSE_DATAS']; ?>')))
                $('#form').attr('action','module=<?php echo MODULE; ?>&view=form&csrf_token=<?php echo $csrf_token; ?>&action='+$(this).val()).trigger('submit');
        });
        $('.btn-slide').on('click', function(){
            var library = $('#wrap-library');
            if($(this).hasClass('left')){
                $(this).removeClass('left').addClass('right');
                library.animate({
                    right:'0px'
                }, 1000);
            }else{
                $(this).removeClass('right').addClass('left');
                library.animate({
                    right:-library.width()+'px'
                }, 1000);
            }
            return false;
        });
        $('.actions-file a').on('click', function(){
            if(!confirm('<?php echo $adminContext->texts['ACTION_CONFIRM'] . " " . $adminContext->texts['LOOSE_DATAS']; ?>')) return false;
        });
        
        $('.datepicker:not([readonly="readonly"])').each(function(){
            pms_init_datepicker($(this));
        });
            
        $('.add_option').on('click', function(){
            var list_id = $(this).attr('rel');
            $('#'+list_id+'_tmp > option:selected').remove().appendTo('#'+list_id);
            $('#'+list_id+' > option').prop('selected', true);
            return false;
            
        });
        $('.remove_option').on('click', function(){
            var list_id = $(this).attr('rel');
            $('#'+list_id+' > option:selected').remove().appendTo('#'+list_id+'_tmp');
            $('#'+list_id+' > option').prop('selected', true);
            return false;
        });
        var indexes = Array();
        $('.new_entry').on('click', function(){
            var table = $($(this).attr('href'));
            var row = $('tr', table);
            var index = row.length-1;
            var last_row = row.last();
            
            $('.typeahead', last_row).typeahead('destroy');
            $('.datepicker', last_row).datepicker('destroy').attr('id', '');
            row = last_row.clone();
            
            $('.typeahead', last_row).each(function(){
                pms_init_typeahead($(this));
            });
            $('.datepicker', last_row).each(function(){
                pms_init_datepicker($(this));
            });
            
            $('input, textarea', row).val('');
            $('select > option', row).prop('selected', false);
            $('checkbox, radio', row).prop('checked', false);
            
            $('input, textarea, select, checkbox, radio', row).each(function(){
                var old_name = $(this).attr('name');
                if(old_name !== undefined) $(this).attr('name', old_name.replace(/([a-zA-Z_0-9\[\]]+)\[([0-9]+)\](\[\])?/, '$1['+index+']$3'));
                var old_id = $(this).attr('id');
                if(old_id !== undefined) $(this).attr('id', old_id.replace(/([a-zA-Z_0-9]+)_([0-9]+)/, '$1_'+index));
                  
                if($(this).hasClass('typeahead')) pms_init_typeahead($(this));
                if($(this).hasClass('datepicker')) pms_init_datepicker($(this));
            });
            row.appendTo(table);
            return false;
        });
        if($('textarea[data-editor="1"]').length){
            setTimeout(function(){
                $('.btn-slide').trigger('click');
            }, 800);
        }
        $('.tab-content').on('keyup', '.numeric > input', function(){
            let val = $(this).val().replace(',', '.');
			val = val.replace(/[^\d.-]/g, '');
			$(this).val(val);
		});
        $('.tab-content').on('blur', '.numeric > input', function(){
			var val = parseFloat($(this).val());
			if(isNaN(val)) val = '';
			$(this).val(val);
		});
    });
</script>

<?php
if(NB_FILES > 0 && ($adminContext->editAllowed || $adminContext->addAllowed)){ ?>

    <script src="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/assets/js/plugins/uploadifive/jquery.uploadifive.js"></script>
    <link href="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/assets/js/plugins/uploadifive/uploadifive.css" rel="stylesheet">
    <script src="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/assets/js/toolMan.js"></script>
    <script>
        var dragsort = ToolMan.dragsort();
        var junkdrawer = ToolMan.junkdrawer();
        
        $(function() {
            'use strict';
            
            $('.files-list').each(function(){
                
                var id_list = $(this).attr('id');
                
                dragsort.makeListSortable(document.getElementById(id_list), saveOrder);
                
                function saveOrder(item) {
                    var group = item.toolManDragGroup;
                    var id = group.element.parentNode.getAttribute('id');
                    if(id == null) return;
                    group.register('dragend', function(){
                        
                        var list = ToolMan.junkdrawer().serializeList(document.getElementById(id_list));
                        
                        $.ajax({
                            type: "POST",
                            url: '<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/xhr/order_medias?list='+list+'&table=<?php echo MODULE . "_file"; ?>&id_item=<?php echo $id; ?>&prefix=file'
                        });
                    })
                }
            });
            
            <?php
            if(empty($_SESSION['msg_error'])){ ?>
                $.ajax({
                    type: 'POST',
                    url: '<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/xhr/clear_tmp',
                    data: 'dir=<?php echo MODULE; ?>&token=<?php echo $_SESSION['token'];?>',
                    success: function(data){
                        
                    }
                });
                <?php
            } ?>
            
            $('.file_upload').each(function(){
                
                var id = $(this).attr('id');
                var rel = $(this).attr('rel').split(',');
                var lang = rel[0];
                var max_file = rel[1];
                
                if(max_file > 10) max_file = 10;
                
                var container = $('#file_uploaded_' + lang);
                if($('.prev-file', container).length) container.slideDown();
                
                $('#'+id).uploadifive({
                    'formData'         : {
                        'timestamp' : '<?php echo $_SESSION['timestamp']; ?>',
                        'uniqid' : '<?php echo $_SESSION['uniqid']; ?>',
                        'token' : '<?php echo $_SESSION['token']; ?>',
                        'dir' : '<?php echo MODULE; ?>',
                        'exts' : '<?php echo serialize(array_keys($adminContext->allowableExts)); ?>',
                        'lang' : lang
                    },
                    'headers' : { 'X-Requested-With' : 'XMLHttpRequest' },
                    'buttonText'     : '<i class="fa fa-fw fa-folder-open"></i> <?php echo $adminContext->texts['CHOOSE_FILE']; ?>',
                    'fileTypeDesc'     : 'Files',
                    'fileTypeExts'     : '<?php foreach (array_keys($adminContext->allowableExts) as $file_ext) echo "*." . $file_ext . ";*." . mb_strtoupper($file_ext, "UTF-8") . ";"; ?>',
                    'multi'            : (max_file > 1),
                    'queueSizeLimit': max_file,
                    'uploadLimit'     : max_file,
                    'queueID'        : 'file_upload_'+lang+'-queue',
                    'uploadScript'     : '<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/xhr/uploadifive',
                    'onUploadComplete' : function(file, data, response) {

                        data = data.split('|');

                        if ($('.prev-file', container).length == 0) {
                            container.slideDown();
                        }

                        var filename = data[0].substring(data[0].lastIndexOf('/') + 1);
                        var ext = filename.substring(filename.lastIndexOf('.') + 1).toLowerCase();

                        if (data[2] == 0 && data[3] == 0) {

                            var icon = '';

                            switch (ext) {
                                <?php
                                foreach($adminContext->allowableExts as $file_ext => $icon) {
                                    echo "case '" . $file_ext . "' : icon = '" . $icon . "'; break;\n";
                                } ?>
                            }

                            container.append(
                                '<div class="prev-file card float-start me-3" style="width: 18rem;">' +
                                '<i class="fa-regular fa-' + icon + '"></i>' +
                                '<div class="card-body">' +
                                filename.substring(0, filename.lastIndexOf('.')).substring(0, 15) + '...' + ext + 
                                '<br>' + data[1] + 
                                '</div></div>'
                            );

                        } else {
                            container.append(
                                '<div class="prev-file card float-start me-3" style="width: 18rem;">' +
                                '<img src="' + data[0] + '" class="card-img-top">' +
                                '<div class="card-body">' +
                                filename.substring(0, filename.lastIndexOf('.')).substring(0, 15) + '...' + ext +
                                '<br>' + data[1] + ' | ' + data[2] + ' x ' + data[3] +
                                '</div></div>'
                            );
                        }

                        if ($('.prev-file', container).length == 1) {
                            container.slideDown();
                        }
                    }
                });
            });
        });
    </script>
    <?php
} ?>
</head>