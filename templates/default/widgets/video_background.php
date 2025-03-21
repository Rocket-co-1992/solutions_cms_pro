<?php

debug_backtrace() || die("Direct access not permitted");
        
$file_path = $widget->getMainFile(false);

if(!empty($file_path)){ ?>
        
    <div class="container">
        <div class="video-background">
            <video autoplay muted playsinline loop class="lazy-video">
                <source data-src="<?php echo $file_path; ?>" type="video/mp4">
            </video>
            <div class="video-content text-white col-12 col-xxl-6">
                <?php echo $widget->content; ?>
            </div>
        </div>
    </div>
    
    <?php
} ?>