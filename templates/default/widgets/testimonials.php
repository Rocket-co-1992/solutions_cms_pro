<?php

use Pandao\Common\Utils\StrUtils;

debug_backtrace() || die("Direct access not permitted");

$siteContext = Pandao\Services\SiteContext::get();
$comments = $siteContext->currentPage->itemComments;

if(!empty($comments)) { ?>

    <div class="container">
        <div class="testimonial-carousel-list owl-carousel mt30 mb40">
            <div class="row">

                <?php
                foreach ($comments as $i => $row) { ?>

                    <div class="col-lg-4 mb20">
                        <div class="single-testimonial-carousel" itemprop="review" itemscope itemtype="http://schema.org/Review">
                            <p itemprop="description">
                                <?php echo StrUtils::strtrunc(nl2br($row['msg']), 600); ?>
                            </p>
                            <span itemprop="author"><b><?php echo mb_strtoupper($row['name']); ?></b> <?php echo $row['title']; ?></span>
                        </div>
                    </div>

                    <?php
                } ?>

            </div>
        </div>
    </div>
    
    <?php
} ?>