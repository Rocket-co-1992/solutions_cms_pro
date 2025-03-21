<?php

use Pandao\Common\Utils\StrUtils;

debug_backtrace() || die("Direct access not permitted");

$siteContext = Pandao\Services\SiteContext::get();
$myPage = $siteContext->currentPage;
$articles = $myPage->getFeaturedHomeArticles();

if (!empty($articles)) { ?>
    <div class="container">
        <div class="row">
            <?php
            foreach ($articles as $article_id => $article) { ?>

                <article class="article-<?php echo $article_id; ?> col-xl-4 col-md-6 col-12 over mb20 appear" data-anim="animate__fadeInUp" itemscope itemtype="http://schema.org/Article">
                    <div class="single-service-card matchHeight">
                        <a itemprop="url" href="<?php echo $article->path; ?>" title="<?php echo $article->title; ?>">
                            <?php
                            // Get the main image using pms_getMainImage
                            $img = $article->getMainImage('medium', false);
                            if (!empty($img['path'])) { ?>
                                <figure class="img-container md lazy">
                                    <img alt="<?php echo $article->title; ?>" data-src="<?php echo $img['path']; ?>" width="<?php echo $img['w']; ?>" height="<?php echo $img['h']; ?>">
                                </figure>
                                <?php
                            } ?>
                        </a>
                        <div class="content">
                            <a itemprop="url" href="<?php echo $article->path; ?>">
                                <h3 itemprop="name"><?php echo $article->title; ?></h3>
                            </a>
                            <h4><?php echo $article->subtitle; ?></h4>
                            <p>
                                <?php echo StrUtils::strtrunc(strip_tags($article->short_text ?? ''), 150); ?>
                            </p>
                            <a class="read-more" itemprop="url" href="<?php echo $article->path; ?>" title="<?php echo $article->title; ?>"><?php echo $siteContext->texts['READMORE']; ?> <i class="fa-light fa-arrow-right"></i></a>
                        </div>
                    </div>
                </article>
                <?php
            } ?>
        </div>
    </div>
    <?php
} ?>