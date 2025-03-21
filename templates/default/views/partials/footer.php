<?php debug_backtrace() || die ('Direct access not permitted'); ?>

<footer class="footer-2 footer-wrap">
    <div class="footer-widgets-wrapper text-white">
        <div class="container">
            <!-- FOOTER WIDGETS -->
            <div class="row">
                <!-- / FOOTER WIDGET COL 1 -->
                <div class="col-xl-3 pe-xl-0 col-sm-6 col-12">
                    <div class="single-footer-wid site_info_widget">

                        <?php $myPage->renderWidgets('footer_col_1'); ?>

                    </div>
                </div>
                <!-- / FOOTER WIDGET COL 2 -->
                <div class="col-sm-6 offset-xl-1 col-xl-3 ps-xl-5 col-12">
                    <div class="single-footer-wid site_info_widget">

                        <?php $myPage->renderWidgets('footer_col_2'); ?>

                    </div>
                </div>
                <!-- / FOOTER WIDGET COL 3 -->
                <div class="col-sm-6 col-xl-4 offset-xl-1 col-12">
                    <div class="single-footer-wid site_info_widget">

                        <?php $myPage->renderWidgets('footer_col_3'); ?>

                    </div>
                </div>
            </div>
            <!-- / FOOTER WIDGETS -->
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-12 text-center text-md-start">
                    <div class="copyright-info">
                        <p>
                            <?php
                            
                            echo 'Copyright &copy; ' . date('Y') . ' ' . PMS_OWNER . ' ' . $siteContext->texts['ALL_RIGHTS_RESERVED'] . ' - ' . $siteContext->texts['CREATION']; ?>

                            <a href="https://www.pandao.eu" title="" target="_blank"><img data-src="https://www.pandao.eu/templates/default/images/pandao-creation-icon-w.png" alt="PANDAO" class="lazy"></a>
                        </p>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="footer-menu mt-3 mt-md-0 text-center text-md-end">
                        <!-- / FOOTER NAV -->
                        <ul>

                            <?php
                            foreach($siteContext->footerMenu as $nav){ ?>

                                <li><a href="<?php echo $nav->href; ?>" title="<?php echo $nav->title; ?>"><?php echo $nav->name; ?></a></li>

                                <?php
                            } ?>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php include( __DIR__ . '/popup.php'); ?>

<a href="#" id="toTop" aria-label="Go to top"><i class="fa-solid fa-fw fa-angle-up"></i></a>

<?php
if(PMS_ENABLE_COOKIES_NOTICE == 1 && !isset($_COOKIE['cookies_enabled'])){ ?>

    <div id="cookies-notice">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 d-flex align-items-center justify-content-center">
                    <i class="fa-light fa-2x fa-cookie me-2"></i> <?php echo $siteContext->texts['COOKIES_NOTICE']; ?>
                    <button class="btn ms-3">OK</button>
                </div>
            </div>
        </div>
    </div>

    <?php
} ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.11/jquery.lazy.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/jquery.magnific-popup.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.appear/0.4.1/jquery.appear.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.appear/0.4.1/jquery.appear.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.matchHeight/0.7.2/jquery.matchHeight-min.js"></script>
<script src="<?php echo DOCBASE; ?>assets/js/plugins/meanmenu/meanmenu.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/5.0.0/imagesloaded.pkgd.min.js"></script>
<script src="<?php echo DOCBASE; ?>assets/js/plugins/imagefill/js/jquery-imagefill.min.js"></script>
<?php foreach (Pandao\Core\Services\AssetsManager::getJs() as $js) echo '<script src="' . $js . '"></script>'."\n"; ?>
<script src="<?php echo DOCBASE; ?>common/js/utils.min.js"></script>
<script src="<?php echo DOCBASE; ?>assets/js/main.min.js"></script>

<script>
    $(function(){
		<?php
        $msg_error = isset($msg_error) ? mb_ereg_replace("(\r\n|\n|\r)","'+\n'",nl2br($msg_error)) : '';
        $msg_success = isset($msg_success) ? mb_ereg_replace("(\r\n|\n|\r)","'+\n'",nl2br($msg_success)) : '';

        if(!empty($msg_error)){ ?>
            $('.alert-danger').html('<?php echo $msg_error; ?>').slideDown();
            setTimeout(function(){$('html, body').animate({scrollTop: ($('.alert-danger').position().top-230)+'px'})}, 800);
            <?php
        }
        if(!empty($msg_success)){ ?>
            $('.alert-success').html('<?php echo $msg_success; ?>').slideDown();
            setTimeout(function(){$('html, body').animate({scrollTop: ($('.alert-success').position().top-230)+'px'})}, 800);
            <?php
        }
        if(isset($field_notice) && !empty($field_notice)){
            foreach($field_notice as $field => $notice){ ?>
                $('.field-notice[rel="<?php echo $field; ?>"]').html('<?php echo $notice; ?>').fadeIn('slow').parent().addClass('error').find('.form-control, .form-select').addClass('is-invalid');
                <?php
            }
        } ?>
    });
</script>
</body>
</html>
