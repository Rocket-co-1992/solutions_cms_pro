<?php

debug_backtrace() || die ('Direct access not permitted');

$siteContext = Pandao\Services\SiteContext::get(); ?>

<div itemscope itemtype="http://schema.org/Corporation">
    <div class="wid-title">
        <h3 itemprop="name"><?php echo $siteContext->texts['GET_IN_TOUCH']; ?></h3>
    </div>
    <address>
        <div class="contact-us">
            <?php
            if(PMS_PHONE != '' || PMS_MOBILE != ''){ ?>
                <div class="single-contact-info">
                    <div class="icon">
                        <i class="fal fa-phone"></i>
                    </div>
                    <div class="contact-info"> 
                        <p><a href="tel:<?php echo PMS_PHONE; ?>" itemprop="telephone" dir="ltr" aria-label="Telephone"><?php echo PMS_PHONE; ?></p>
                        <p><a href="tel:<?php echo PMS_MOBILE; ?>" itemprop="telephone" dir="ltr" aria-label="Mobile"><?php echo PMS_MOBILE; ?></a></p>
                    </div>
                </div>
                <?php
            }
            if(PMS_EMAIL != ''){ ?>
                <div class="single-contact-info">
                    <div class="icon">
                        <i class="fal fa-envelope"></i>
                    </div>
                    <div class="contact-info">
                        <p><a itemprop="email" dir="ltr" href="mailto:<?php echo PMS_EMAIL; ?>"><?php echo PMS_EMAIL; ?></a></p>
                    </div>
                </div>
                <?php
            }
            if(PMS_ADDRESS != ''){ ?>
                <div class="single-contact-info">
                    <div class="icon">
                        <i class="fal fa-map-marker-alt"></i>
                    </div>
                    <div class="contact-info">
                        <p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><?php echo nl2br(PMS_ADDRESS); ?></p>
                    </div>
                </div>
                <?php
            } ?>
        </div>
    </address>
</div>

<?php
foreach($siteContext->socials as $s){ ?>
    <a href="<?php echo $s['url']; ?>" target="_blank" aria-label="<?php echo $s['type']; ?>">
        <i class="fa-brands me-3 fa-<?php echo $s['type']; ?>"></i> 
    </a> 
    <?php
} ?>