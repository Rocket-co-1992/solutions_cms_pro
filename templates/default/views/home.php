<?php

use Pandao\Core\Services\AssetsManager;

AssetsManager::addCss('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css');
AssetsManager::addCss('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css');
AssetsManager::addJs('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js');

AssetsManager::addCss('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');

//-----------------------------------
// Main slideshow
require_once __DIR__. '/partials/slideshow.php'; ?>

<section class="content">

    <!-- Widgets before the main content -->
    <?php $myPage->renderWidgets('main_before'); ?>

    <div class="container">
        <section class="row mb-3 justify-content-center mt40 mb40 appear" data-anim="animate__fadeInUp">
            <div class="col-md-8 text-center">
                <h5><?php echo $myPage->subtitle; ?></h5>
                <h2 class="col-xl-6 col-lg-8 mb-4 offset-xl-3 offset-lg-2 col-12"><?php echo $myPage->title; ?></h2>

                <?php echo $myPage->text; ?>
            </div>
        </section>
    </div>

    <!-- Widgets after the main content -->
    <?php $myPage->renderWidgets('main_after'); ?>

</section>
