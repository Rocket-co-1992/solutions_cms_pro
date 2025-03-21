<?php debug_backtrace() || die ("Direct access not permitted");

$pageBanner = $myPage->getMainImagePath('big', false, 1500);
if(!isset($myArticle)) $img = $pageBanner;
else {
    $img = $myArticle->getMainImagePath('big', false, 1500);
    if(empty($img)) $img = $pageBanner;
} ?>

<div class="page-banner-wrap bg-cover" style="background-image: url('<?php echo $img; ?>')">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-12">
                <div class="page-heading text-white">
                    <h1 itemprop="name"><?php echo $myView->title; ?></h1>
                    <?php if(!empty($myView->subtitle)) echo '<p class="lead mb0">' . $myView->subtitle . '</p>'; ?>
                </div>
                <div class="breadcrumb-wrap">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo $siteContext->getHome()->path; ?>" title="<?php echo $siteContext->getHome()->title; ?>"><?php echo $siteContext->getHome()->name; ?></a></li>
                            
                            <?php
                            foreach($myPage->breadcrumbs as $id_parent){
                                if(isset($siteContext->parents[$id_parent])){ ?>

                                    <li class="breadcrumb-item">
                                        <a href="<?php echo $siteContext->parents[$id_parent]->path; ?>" title="<?php echo $siteContext->parents[$id_parent]->title; ?>">
                                            <?php echo $siteContext->parents[$id_parent]->name; ?>
                                        </a>
                                    </li>

                                    <?php
                                }
                            }
                            if(!empty($myArticle)){ ?>
                                <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo $myPage->path; ?>" title="<?php echo $myPage->title; ?>"><?php echo $myPage->name; ?></a></li>
                                <?php
                            } ?>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo $myView->name; ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>