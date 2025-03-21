<?php
debug_backtrace() || die ("Direct access not permitted");

use Pandao\Common\Utils\DateUtils;

if($myView->object->comment){
    
    if($myView->object->num_comments > 0){ ?>
        <section class="mt40">
            <h3 class="commentNumbers">
                <?php
                echo $siteContext->texts['COMMENTS']." ";
                if(PMS_RTL_DIR) echo "&rlm;";
                echo "(".$myView->object->num_comments.")"; ?>
            </h3>
            <ul class="comments-item-list nostyle">
                <?php
                foreach($myView->object->itemComments as $i => $comment){ ?>
                    <li class="single-comment-item">
                        <div class="author-img">
                            <img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm&s=50" alt="" class="img-fluid">
                        </div>
                        <div class="author-info-comment">
                            <div class="info">
                                <h5><?php echo $comment['name']; ?></h5>
                                <div class="commentInfo"> <span><?php echo (!PMS_RTL_DIR) ? DateUtils::strftime(PMS_DATE_FORMAT, $comment['add_date']) : DateUtils::strftime("%F", $comment['add_date']); ?></span></div>
                            </div>
                            <div class="comment-text">
                                <?php echo nl2br($comment['msg']); ?>
                            </div>
                        </div>
                        <!--<div class="col-sm-3">
                            <?php
                            if($myView->object->rating == 1 && $comment['rating'] > 0 && $comment['rating'] <= 5){ ?>
                                <input type="hidden" class="rating" value="<?php echo $comment['rating']; ?>" data-rtl="<?php echo (PMS_RTL_DIR) ? true : false; ?>" data-size="xs" readonly="true" data-show-clear="false" data-show-caption="false">
                                <?php
                            } ?>
                        </div>-->
                        </li>
                    <?php
                } ?>
            </ul>
        </section>
        <?php
    } ?>
    
    <!-- Comments -->
    <h3 class="mb10 mt30"><?php echo $siteContext->texts['LET_US_KNOW']; ?></h3>
    
    <div class="row">
        <form method="post" action="" class="ajax-form">

            <div class="alert alert-success" style="display:none;"></div>
            <div class="alert alert-danger" style="display:none;"></div>
        
            <input type="hidden" name="item_type" value="<?php echo $myView->type; ?>">
            <input type="hidden" name="item_id" value="<?php echo $myView->object->id; ?>">
            <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response"> 
            <div class="col-sm-12">
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-fw fa-quote-left"></i></span>
                        <textarea class="form-control" name="msg" placeholder="<?php echo $siteContext->texts['COMMENT']; ?> *" rows="9"></textarea>
                    </div>
                    <div class="field-notice" rel="msg"></div>
                </div>
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-fw fa-user"></i></span>
                        <input type="text" class="form-control" name="name" value="" placeholder="<?php echo $siteContext->texts['LASTNAME']." ".$siteContext->texts['FIRSTNAME']; ?> *">
                    </div>
                    <div class="field-notice" rel="name"></div>
                </div>
                <div class="form-group mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-fw fa-envelope"></i></span>
                        <input type="text" class="form-control" name="email" value="" placeholder="<?php echo $siteContext->texts['EMAIL']; ?> *">
                    </div>
                    <div class="field-notice" rel="email"></div>
                </div>
                <?php
                if($myView->object->rating){ ?>
                    <div class="form-group form-inline">
                        <label for="rating">Rating</label>
                        <div class="input-group mb5">
                            <input type="hidden" name="rating" class="rating" value="<?php echo $rating; ?>" data-rtl="<?php echo (PMS_RTL_DIR) ? true : false; ?>" min="1" max="5" data-step="1" data-size="xs" data-show-clear="false" data-show-caption="false">
                        </div>
                    </div>
                    <?php
                } ?>
                <div class="form-group row">
                    <span class="col-sm-12"><button type="button" data-action="xhr/action/send_comment" class="btn btn-primary sendAjaxForm" name="send_comment"><?php echo $siteContext->texts['SEND']; ?></button> <i> * <?php echo $siteContext->texts['REQUIRED_FIELD']; ?></i></span>
                </div>
            </div>
        </form>
    </div>
    <?php
} ?>
