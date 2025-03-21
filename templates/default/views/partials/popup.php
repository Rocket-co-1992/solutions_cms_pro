<?php

debug_backtrace() || die ("Direct access not permitted");

$popup = $myPage->getPopup();

if (!empty($popup)){ ?>
    
    <a class="popup-modal hide" href="#popup-<?php echo $popup['id']; ?>"></a>
    
    <div id="popup-<?php echo $popup['id']; ?>" class="white-popup-block mfp-hide"<?php if(!empty($popup['background'])) echo ' style="background-color:'.$popup['background'].';"'; ?>>
        <div class="fluid-container">
            <div class="row">
                <div class="col-md-12">
                    <?php echo $popup['content']; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}