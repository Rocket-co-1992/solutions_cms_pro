<?php debug_backtrace() || die ('Direct access not permitted'); ?>
<!DOCTYPE html>
<html lang="<?php echo PMS_LANG_TAG; ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>
    Solutions CMS
    <?php
    echo TITLE_ELEMENT;
    if(defined('PMS_SITE_TITLE')) echo ' | '.PMS_SITE_TITLE; ?>
</title>

<?php
if(defined('PMS_TEMPLATE')){ ?>
    <link rel="icon" type="image/png" href="<?php echo DOCBASE; ?>assets/images/favicon.png">
    <?php
} ?>
    
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@100..800&family=Urbanist:wght@100..900&display=swap">
<link rel="stylesheet" href="<?php echo DOCBASE; ?>common/css/shortcodes.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.6.0/css/all.css">
<link rel="stylesheet" href="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/assets/css/layout.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/magnific-popup.min.css">

<?php
foreach($assets_css as $css) { ?>
    <link rel="stylesheet" href="<?php echo $css; ?>">
    <?php
} ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.14.0/jquery-ui.min.js" integrity="sha256-Fb0zP4jE3JHqu+IBB9YktLcSjI1Zc6J2b6gTjB0LpoM=" crossorigin="anonymous"></script>
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/jquery.magnific-popup.min.js"></script>
<script defer src="https://use.fontawesome.com/releases/v6.6.0/js/all.js"></script>

<?php
foreach($assets_js as $js) { ?>
    <script defer src="<?php echo $js; ?>"></script>
    <?php
} ?>

<script defer src="<?php echo DOCBASE . PMS_ADMIN_FOLDER; ?>/assets/js/utils.js"></script>

<script>
    $(function(){

        var msg_error = '<?php echo str_replace(addslashes("\n"), "\n", addslashes(implode('<br>', $_SESSION['msg_error']))); ?>';
        var msg_success = '<?php echo str_replace(addslashes("\n"), "\n", addslashes(implode('<br>', $_SESSION['msg_success']))); ?>';
        var msg_notice = '<?php echo str_replace(addslashes("\n"), "\n", addslashes(implode('<br>', $_SESSION['msg_notice']))); ?>';
        
        var button_close = '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        if(msg_error != '') $('.alert-container .alert-danger').html(msg_error+button_close).show();
        if(msg_success != '') $('.alert-container .alert-success').html(msg_success+button_close).show();
        if(msg_notice != '') $('.alert-container .alert-info').html(msg_notice+button_close).show();
        
        
        $('select[data-filter]').each(function(){
            var target = $(this);
            var currval = $(this).val();
            var curropt = $('option[value="'+currval+'"]', target);
            var input = $('select').filter('<?php if(defined('MODULE')) : ?>[name^="<?php echo MODULE; ?>_'+target.attr('data-filter')+'"],<?php endif; ?>[name="'+target.attr('data-filter')+'"]');
            input.on('change', function(){
                var val = $(this).val();
                $('option[value!=""]', target).hide().prop('selected', false);
                $('option[rel="'+val+'"]', target).show();
                if(curropt.attr('rel') == val) curropt.prop('selected', true);
            });
            input.trigger('change');
        });
    })
</script>
<?php if(!isset($headModulePath)) echo '</head>';