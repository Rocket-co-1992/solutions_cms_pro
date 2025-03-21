<?php
debug_backtrace() || die ("Direct access not permitted");

$slides = $myPage->getSlides();

if (!empty($slides)) { ?>
	
    <section class="hero-wrapper hero-1">
        <div class="hero-slider-active owl-carousel">
            <?php
            foreach($slides as $i => $slide){ ?>

                <div class="single-slide bg-cover" 
                        data-bg="<?php echo $slide['path']; ?>"
                        data-bg-medium="<?php echo str_replace('/big/', '/medium/', $slide['path']); ?>"
                        data-bg-small="<?php echo str_replace('/big/', '/small/', $slide['path']); ?>">
                    
                    <?php
                    if(!empty($slide['legend'])){ ?>
                        <div class="container">
                            <div class="row">
                                <div class="col-12 ps-md-5 pe-md-5 col-xxl-7 col-lg-8 col-md-8 col-sm-10">
                                    <div class="hero-contents pe-lg-3">
                                        <?php echo $slide['legend']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    } ?>
                </div>

                <?php
            } ?>
        </div>
        <div class="slider-nav"></div>
    </section>

    <?php
} ?>