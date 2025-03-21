<?php
//-----------------------------------
// CSS / JS used in this model

use Pandao\Core\Services\AssetsManager;

AssetsManager::addCss(DOCBASE . 'assets/js/plugins/lazyloader/lazyloader.css');
AssetsManager::addJs(DOCBASE . 'assets/js/plugins/lazyloader/lazyloader.js');

AssetsManager::addJs('https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js');

//-----------------------------------
// Page model ?>

<section id="page">
    
    <?php
    require_once __DIR__ . '/partials/page_header.php';
    
    //-----------------------------------
    // Widgets before the main content
    $myPage->renderWidgets('main_before'); ?>

    <div class="content section-padding">
        <section class="container" itemprop="text">
            <div class="row">

                <div class="col-sm-<?php if(!empty($myPage->widgets['right'])) echo 6; else echo 12; ?>">
                    
                    <div class="alert alert-success" style="display:none;"></div>
                    <div class="alert alert-danger" style="display:none;"></div>
                    
                    <?php
                    //-----------------------------------
                    // Main content
                    echo $myPage->text; 
                    
                    //-----------------------------------
                    // Page images gallery -> Number min. images = 2
                    if(!empty($myPage->images) && count($myPage->images) >= 2){ ?>
                        <div class="row grid mb40">
                            <?php
                            foreach($myPage->images as $i => $row){
                                $path = 'medias/page/big/'.$row['id'] . '/' . $row['file'];
                                if(is_file(SYSBASE . 'public/' . $path)){ ?>
                                    <div class="col-lg-4 col-md-6 grid-item">
                                        <div class="project-item-box">
                                            <a href="<?php echo DOCBASE . $path; ?>" class="project-thumb bg-cover image-link" style="background-image: url('<?php echo DOCBASE . $path; ?>')"></a>
                                            <div class="content-wrap">
                                                <div class="contents">
                                                    <span><?php echo $row['label']; ?></span>
                                                </div>
                                            </div>
                                            <a href="<?php echo DOCBASE . $path; ?>" class="image-link project-link"><i class="fa-light fa-plus"></i></a>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } ?>
                        </div>
                        <?php
                    } ?>
                </div>
                
                <?php
                if(!empty($myPage->widgets['right'])){ ?>
                    <div class="col-sm-3">
                        <?php $myPage->renderWidgets('right'); ?>
                    </div>
                    <?php
                } ?>
            </div>
            
            <?php
            //-----------------------------------
            // Comments if enabled
            include __DIR__ . '/partials/comments.php'; ?>

        </section>
    </div>

    <?php
    //-----------------------------------
    // Widgets after the main content
    $myPage->renderWidgets('main_after'); ?>

</section>
