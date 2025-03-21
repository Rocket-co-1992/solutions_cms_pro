<?php

use Pandao\Common\Utils\StrUtils;

debug_backtrace() || die ("Direct access not permitted");

$siteContext = Pandao\Services\SiteContext::get();
$myPage = $siteContext->currentPage;
$articles = $myPage->getSingleFeaturedHomeArticle();

foreach($articles as $article_id => $article){ ?>

    <section class="container mb40 mt40 appear" data-anim="animate__fadeInUp">
        <div class="row">
            <div class="col-xl-6 col-lg-6 pe-xl-5 ps-xl-5 col-12">
                <figure class="image">
                    <?php
                    $img = $article->getMainImage('big', false);
                    if(!empty($img['path'])){ ?>
                        <img alt="" data-src="<?php echo $img['path']; ?>" width="<?php echo $img['w']; ?>" height="<?php echo $img['h']; ?>" class="img-fluid lazy">
                        <?php
                    } ?>
                </figure>
            </div>
            <div class="col-xl-6 col-lg-6 col-12 mt-5 mt-lg-0">
                <h5><?php echo $siteContext->texts['WHATS_NEW']; ?></h5>
                <h2><?php echo $article->title; ?></h2>
                <?php echo StrUtils::strtrunc($article->text, 700); ?>
                <p class="mt20">
                    <a itemprop="url" href="<?php echo $article->path; ?>" class="btn btn-primary" title="<?php echo $article->title; ?>"><?php echo $siteContext->texts['READMORE']; ?></a>
                </p>
            </div>
        </div>
    </section>
    <?php
} ?>