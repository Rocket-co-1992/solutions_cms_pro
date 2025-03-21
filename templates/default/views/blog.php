<?php
//-----------------------------------
// CSS / JS used in this model
use Pandao\Core\Services\AssetsManager;

AssetsManager::addCss(DOCBASE . 'assets/js/plugins/lazyloader/lazyloader.css');
AssetsManager::addJs(DOCBASE . 'assets/js/plugins/lazyloader/lazyloader.js');

AssetsManager::addCss('//cdn.jsdelivr.net/gh/kartik-v/bootstrap-star-rating@4.0.7/css/star-rating.css');
AssetsManager::addJs('//cdn.jsdelivr.net/gh/kartik-v/bootstrap-star-rating@4.0.7/js/star-rating.js');
         
$start_month = null;
$end_month = null;
if(isset($_GET['month']) && is_numeric($_GET['month']) && isset($_GET['year']) && is_numeric($_GET['year'])){
    $start_month = mktime(0, 0, 0, $_GET['month'], 1, $_GET['year']);
    $end_month = mktime(0, 0, 0, $_GET['month'], date('t', $start_month), $_GET['year']);
}

$tag = (isset($_GET['tag']) && is_numeric($_GET['tag'])) ? $_GET['tag'] : 0;

$articles = $siteContext->getArticles([
    'id_page' => $myPage->id
]);

//-----------------------------------
// Page model ?>

<section id="page">
    
    <?php
    require_once __DIR__ . '/partials/page_header.php'; ?>
    
    <div class="content section-padding">
        <div class="container" itemprop="text">
            
            <div class="alert alert-success" style="display:none;"></div>
            <div class="alert alert-danger" style="display:none;"></div>
            
            <?php
            if(!empty($articles)){ ?>
                
                <section class="blog-wrapper news-wrapper">
                    <div class="row">
                        <div class="col-12 col-lg-8">
                            <div class="blog-posts lazy-wrapper" itemscope itemtype="http://schema.org/Blog" id="blog-content"
                                data-loader="xhr/views/get_articles_blog"
                                data-mode="click"
                                data-limit="5"
                                data-total="<?php echo ceil(count($articles)); ?>"
                                data-more_caption="<?php echo $siteContext->texts['LOAD_MORE']; ?>"
                                data-is_isotope="false"
                                data-variables="page=<?php echo $myPage->id; ?>&start_month=<?php echo $start_month; ?>&end_month=<?php echo $end_month; ?>&tag=<?php echo $tag; ?>">
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="main-sidebar">

                                <?php $myPage->renderWidgets('right'); ?>

                            </div>
                        </div>
                    </div>
                </section>

                <?php
            } ?>

        </div>
    </div>
</section>
