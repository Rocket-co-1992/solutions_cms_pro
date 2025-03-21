<?php
//-----------------------------------
// CSS / JS used in this model

use Pandao\Core\Services\AssetsManager;

AssetsManager::addCss(DOCBASE . 'assets/js/plugins/lazyloader/lazyloader.css');
AssetsManager::addJs(DOCBASE . 'assets/js/plugins/lazyloader/lazyloader.js');

AssetsManager::addCss('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css');
AssetsManager::addCss('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css');
AssetsManager::addJs('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js');
AssetsManager::addJs('https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js');

//-----------------------------------
// Article model ?>

<article id="page">

    <?php 
    require_once __DIR__ . '/partials/page_header.php'; ?>
    
    <div class="content section-padding">
        <div class="container">
            
            <div class="row">
                <div class="col-sm-6 mb10">
                    <?php echo $myArticle->text; ?>
                </div>
                <div class="col-sm-6 mb10">
                    <?php
                    //-----------------------------------
                    // Article main image
                    $img = $myArticle->getMainImage();
                    if(!empty($img)){ ?>

                        <a href="<?php echo $img['path']; ?>" class="image-link">
                            <img alt="<?php echo $img['label']; ?>" data-src="<?php echo $img['path']; ?>" class="img-fluid lazy" width="<?php echo $img['w']; ?>" height="<?php echo $img['h']; ?>">
                        </a>

                        <?php
                    } ?>
                </div>
                <?php
                //-----------------------------------
                // Article images gallery
                $imgs = $myArticle->getImages();
                if(count($imgs) > 1){ ?>

                    <div class="col-sm-12">
                        <div class="row grid mb40">
                            <?php
                            for($i = 1; $i < count($imgs); $i++){ ?>

                                <div class="col-lg-4 col-md-6 grid-item">
                                    <div class="project-item-box">
                                        <a href="<?php echo $imgs[$i]['url']; ?>" class="project-thumb bg-cover image-link" style="background-image: url('<?php echo $imgs[$i]['url']; ?>')"></a>
                                        <div class="content-wrap">
                                            <div class="contents">
                                                <span><?php echo $imgs[$i]['label']; ?></span>
                                            </div>
                                        </div>
                                        <a href="<?php echo $imgs[$i]['url']; ?>" class="image-link project-link"><i class="fa-light fa-plus"></i></a>
                                    </div>
                                </div>

                                <?php
                            } ?>
                        </div>
                    </div>
                    <?php
                } ?>
            </div>
			
            <div class="row grid mb40">
                <?php
                //-----------------------------------
                // Related articles
                require_once __DIR__ . '/../widgets/articles_grid.php'; ?>
            </div>

            <?php
            //-----------------------------------
            // Comments if enabled
            include __DIR__ . '/partials/comments.php'; ?>
        </div>
    </div>
</article>
