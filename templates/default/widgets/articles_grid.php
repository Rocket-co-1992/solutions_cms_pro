<?php

debug_backtrace() || die ("Direct access not permitted");

$siteContext = Pandao\Services\SiteContext::get();
$myPage = $siteContext->currentPage;

$grid = (!isset($grid) || !is_numeric($grid)) ? 4 : ceil(12 / $grid);
$limit = (!isset($limit) || !is_numeric($limit)) ? 9 : $limit;
$isotope = !isset($isotope) ? true : $isotope;

$articles = $siteContext->getArticles([
    'id_page' => $myPage->id,
    'excluded_ids' => $myPage->articles_disp_ids
]);

if (!empty($articles)) {
    
    if(!empty($myPage->tags)){ ?>

        <div class="container">
            <nav class="col-12 col-lg-12 text-center mb30 isotope-filter">
                <button data-filter="*" class="active"><?php echo $siteContext->texts['ALL']; ?></button>
                <?php
                foreach($myPage->tags as $tag){ ?>
                    <button data-filter=".tag<?php echo $tag['id']; ?>"><?php echo $tag['value']; ?></button>
                    <?php
                } ?>
            </nav>
        </div>

        <?php
    } ?>

    <div class="container mb40">
        <?php
    
        if (isset($myArticle)) echo '<div class="col-md-12"><h2>' . $siteContext->texts['DISCOVER_ALSO'] . '</h2></div>'; ?>

        <div class="lazy-wrapper row grid <?php if($isotope) echo 'isotope'; ?>"
            data-cols="3"
            data-loader="xhr/views/get_articles"
            data-mode="click"
            data-limit="<?php echo $limit; ?>"
            data-total="<?php echo ceil(count($articles)); ?>"
            data-more_caption="<?php echo $siteContext->texts['LOAD_MORE']; ?>"
            data-is_isotope="<?php echo $isotope; ?>"
            data-variables="grid=<?php echo $grid; ?>&page=<?php echo $myPage->id; ?>&article=<?php echo $myArticle->id ?? null; ?>">
        </div>

    </div>
    <?php
}