<?php

use Pandao\Core\Services\AssetsManager;
use Pandao\Common\Utils\DateUtils;

//-----------------------------------
// CSS / JS used in this model
AssetsManager::addCss(DOCBASE . 'assets/js/plugins/lazyloader/lazyloader.css');
AssetsManager::addJs(DOCBASE . 'assets/js/plugins/lazyloader/lazyloader.js');

AssetsManager::addCss('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css');
AssetsManager::addCss('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css');
AssetsManager::addJs('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js');

AssetsManager::addJs(DOCBASE . 'assets/js/plugins/c-share/jquery.c-share.js'); ?>

<article itemscope itemtype="http://schema.org/BlogPosting" itemprop="mainEntity" id="page">
    
    <?php
    require_once __DIR__ . '/partials/page_header.php'; ?>
    
    <div class="content section-padding blog-wrapper news-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="blog-post-details border-wrap">
                        <div class="single-blog-post post-details">
                            <div class="post-content">
                                <h2><?php echo $myArticle->title; ?></h2>

                                <div class="post-meta">
                                    <i class="fa-light fa-fw fa-thumbtack"></i> 
                                    <span>
                                        <time itemprop="dateCreated datePublished dateModified" datetime="<?php echo date('c', $myArticle->publish_date); ?>">
                                            <?php echo (!PMS_RTL_DIR) ? DateUtils::strftime(PMS_DATE_FORMAT, $myArticle->publish_date) : DateUtils::strftime('%F', $myArticle->publish_date); ?>
                                        </time>
                                    </span>
                                    <i class="fa-light fa-fw fa-comment"></i> <span><?php echo $myArticle->num_comments.' '.mb_strtolower($siteContext->texts['COMMENTS'], 'UTF-8'); ?></span>
                                    <?php
                                    /*$result_users = $pms_db->query('SELECT * FROM solutionsCMS_user WHERE FIND_IN_SET(id, '.$myArticle->users.')');
                                    if($result_users != false){
                                        foreach($result_users as $user_article){ ?>
                                            <i class="fa-light fa-fw fa-user"></i> <span itemprop="creator author publisher"><?php echo $user_article['login']; ?></span>
                                            <?php
                                        }
                                    }*/
                                    foreach($myArticle->getTags() as $tag){ ?>
                                        <i class="fa-light fa-fw fa-tag"></i> <span><?php echo trim($tag['value']); ?></span>
                                        <?php
                                    } ?>
                                </div>
                                
                                <p><?php echo $myArticle->short_text; ?></p>
                                
                                <?php
                                //-----------------------------------
                                // Article main image
                                $img = $myArticle->getMainImage();
                                if(!empty($img)){ ?>

                                    <figure>
                                        <img alt="<?php echo $img['label']; ?>" data-src="<?php echo $img['path']; ?>" class="img-fluid lazy" width="<?php echo $img['w']; ?>" height="<?php echo $img['h']; ?>">
                                    </figure>

                                    <?php
                                } ?>

                                <div itemprop="articleBody">
                                    <?php echo $myArticle->text; ?>
                                </div>
                                
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-8 col-12">
                                <h4><?php echo $siteContext->texts['SHARE']; ?></h4>
                                <div class="share-block" data-text="<?php echo $myArticle->getShortText(); ?>"></div>
                            </div>

                            <div class="col-lg-4 col-12 text-end">
                                <?php
                                $my_articles = $myPage->articles;
                                while(strval(key($my_articles)) != strval($myArticle->id) && key($my_articles) != null) next($my_articles);
                                $prev_article = prev($my_articles);
                                if($prev_article !== false) next($my_articles); else reset($my_articles);
                                $next_article = next($my_articles); ?>

                                <a href="<?php echo $myPage->path; ?>" class="me-2"><i class="fa-solid fa-fw fa-arrow-left"></i> <?php echo $siteContext->texts['BACK']; ?></a>
                                
                                <?php
                                if($prev_article){ ?>
                                    <a href="<?php echo $prev_article->path; ?>" class="me-1"><i class="fa-solid fa-fw fa-chevron-left"></i> <?php echo $siteContext->texts['PREV']; ?></a> 
                                    <?php
                                }
                                if($next_article){ ?>
                                    <a href="<?php echo $next_article->path; ?>" class="ms-1"><?php echo $siteContext->texts['NEXT']; ?> <i class="fa-solid fa-fw fa-chevron-right"></i></a> 
                                    <?php
                                } ?>
                            </div>
                        </div>
                        
                        <div class="row grid pt20 mt20 mb40 border-top">
                            <?php
                            //-----------------------------------
                            // Related articles
                            $grid = 2;
                            $limit = 4;
                            $isotope = false;
                            require_once __DIR__ . '/../widgets/articles_grid.php'; ?>
                        </div>

                        <?php
                        //-----------------------------------
                        // Comments if enabled
                        include __DIR__ . '/partials/comments.php'; ?>
                    </div>
                </div>
                    
                <div class="col-12 col-lg-4">
                    <div class="main-sidebar">
                        <?php $myPage->renderWidgets('right'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
