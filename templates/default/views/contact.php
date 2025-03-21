<section id="page">
    
    <?php
    require_once __DIR__ . '/partials/page_header.php';
        
    //-----------------------------------
    // Widgets before the main content
    $myPage->renderWidgets('main_before'); ?>
    
    <div class="content section-padding">
        <div class="container">
            
            <?php
            if(!empty($myPage->text)){ ?>
                <div class="clearfix mb20"><?php echo $myPage->text; ?></div>
                <?php
            } ?>
            
            <div class="row">
                <form method="post" action="<?php echo $myPage->path; ?>">
                    <input type="hidden" name="captchaHoney" value="" class="hide">
                    <div class="col-sm-6 offset-sm-3">

                        <div class="alert alert-success" style="display:none;"></div>
                        <div class="alert alert-danger" style="display:none;"></div>

                        <div class="form-group mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-fw fa-user"></i></span>
                                <input type="text" class="form-control" name="name" value="<?php echo $contactForm->name; ?>" placeholder="<?php echo $siteContext->texts['LASTNAME']." ".$siteContext->texts['FIRSTNAME']; ?> *">
                            </div>
                            <div class="field-notice" rel="name"></div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-fw fa-envelope"></i></span>
                                <input type="text" class="form-control" name="email" value="<?php echo $contactForm->email; ?>" placeholder="<?php echo $siteContext->texts['EMAIL']; ?> *">
                            </div>
                            <div class="field-notice" rel="email"></div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-fw fa-home"></i></span>
                                <textarea class="form-control" name="address" placeholder="<?php echo $siteContext->texts['ADDRESS'].", ".$siteContext->texts['POSTCODE'].", ".$siteContext->texts['CITY']; ?>"><?php echo $contactForm->address; ?></textarea>
                            </div>
                            <div class="field-notice" rel="address"></div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-fw fa-phone"></i></span>
                                <input type="text" class="form-control" name="phone" value="<?php echo $contactForm->phone; ?>" placeholder="<?php echo $siteContext->texts['PHONE']; ?>">
                            </div>
                            <div class="field-notice" rel="phone"></div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-fw fa-question"></i></span>
                                <input type="text" class="form-control" name="subject" value="<?php echo $contactForm->subject; ?>" placeholder="<?php echo $siteContext->texts['SUBJECT']; ?>">
                            </div>
                            <div class="field-notice" rel="subject"></div>
                        </div>
                        <div class="form-group mb-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-fw fa-quote-left"></i></span>
                                <textarea class="form-control" name="msg" placeholder="<?php echo $siteContext->texts['MESSAGE']; ?> *" rows="4"><?php echo $contactForm->msg; ?></textarea>
                            </div>
                            <div class="field-notice" rel="msg"></div>
                        </div>
                        <div class="form-group mb-3">
                            <input class="form-check-input" type="checkbox" name="privacy_agreement" value="1"<?php if($contactForm->privacy_agreement) echo ' checked="checked"'; ?>> <?php echo $siteContext->texts['PRIVACY_POLICY_AGREEMENT']; ?>
                            <div class="field-notice" rel="privacy_agreement"></div>
                        </div>
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response"> 
                        <div class="form-group row">
                            <span class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-primary" name="send" value="1"><?php echo $siteContext->texts['SEND']; ?></button><br><br>
                                <i> * <?php echo $siteContext->texts['REQUIRED_FIELD']; ?></i>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    var pms_locations = [
        <?php
        foreach ($locations as $i => $location) { ?>
            ['<?php echo $location->name; ?>', '<?php echo $location->address; ?>', <?php echo $location->lat; ?>, <?php echo $location->lng; ?>]
            <?php if ($i + 1 < count($locations)) echo ",\n";
        } ?>
    ];
</script>

<?php
if(!empty($locations) && PMS_GMAPS_API_KEY) { ?>

    <div id="contact-map-wrap"></div><div id="map-marker"></div>

    <?php
} ?>