<?php

debug_backtrace() || die ('Direct access not permitted');

$siteContext = Pandao\Services\SiteContext::get();
$articles = $siteContext->getArticles([
    'limit' => 4,
    'sort' => 'latest'
]); ?>

<div class="d-flex justify-content-between">

    <?php
    foreach($articles as $i => $article){ ?>
        
        <a href="<?php echo $article->path; ?>" title="<?php echo $article->title; ?>" class="img-container sm float-start tips">
            <img data-src="<?php echo $img['path']; ?>" width="<?php echo $img['w']; ?>" height="<?php echo $img['h']; ?>" alt="" class="lazy">
        </a>

        <?php
    } ?>

</div>