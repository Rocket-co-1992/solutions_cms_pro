<?php debug_backtrace() || die ('Direct access not permitted'); ?>

<body id="page-<?php echo $myPage->id; ?>" class="body-wrapper" itemscope itemtype="https://schema.org/WebPage"<?php if(PMS_RTL_DIR) echo " dir=\"rtl\""; ?>>
<div class="search__popup">
    <div class="container">
        <div class="row">
            <div class="col-xxl-12">
                <div class="search__wrapper">
                    <div class="search__top d-flex justify-content-between align-items-center">
                        <div class="search__logo">
                            <a href="<?php echo $siteContext->getHome()->path; ?>" title="<?php echo PMS_SITE_TITLE; ?>">
                                <img src="<?php echo DOCBASE; ?>assets/images/footer-logo.png" alt="">
                            </a>
                        </div>
                        <div class="search__close">
                            <button type="button" class="search__close-btn search-close-btn" aria-label="Close search">
                                <i class="fal fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="search__form">
                        <form method="post" action="<?php echo $siteContext->getPage('search')->path; ?>" class="form-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                            <div class="search__input">
                                <input class="search-input-field" type="text" name="global-search" placeholder="<?php echo $siteContext->texts['SEARCH']; ?>">
                                <span class="search-focus-border"></span>
                                <button type="submit" aria-label="<?php echo $siteContext->texts['SEARCH']; ?>">
                                    <i class="far fa-search"></i>
                                </button>
                            </div>
                        </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<header class="header-wrap header-1">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="logo">
            <a href="<?php echo $siteContext->getHome()->path; ?>" title="<?php echo $siteContext->getHome()->title; ?>"><img class="img-fluid" src="<?php echo DOCBASE; ?>assets/images/logo.png" alt="<?php echo PMS_SITE_TITLE; ?>"></a>
        </div>
        <div class="header-right-area d-flex">
            <div class="main-menu d-none d-xl-block">
                <nav id="mobile-menu" role="navigation">
                    <ul>
                        <?php
                        foreach ($siteContext->mainMenu as $nav) {
                            ?>
                            <li class="nav-<?php echo $nav->id; ?>">
                                <?php
                                if ($nav->item_type == 'page' && $siteContext->getPage($nav->id_item)->home == 1) {
                                    ?>
                                    <a class="<?php if ($myPage->home) echo ' active'; ?>" href="<?php echo $siteContext->getHome()->path; ?>" title="<?php echo $nav->title; ?>"><?php echo $nav->name; ?></a>
                                    <?php
                                } else {
                                    $hasChildNav = $nav->hasChildNav();
                                    ?>
                                    <a class="<?php if ($nav->isActive()) echo ' active'; ?>" href="<?php echo $nav->href; ?>" title="<?php echo $nav->title; ?>">
                                        <?php
                                        echo $nav->name;
                                        if ($hasChildNav) echo ' <i class="fa-light fa-plus fa-fw"></i>'; ?>
                                    </a>
                                    <?php if ($hasChildNav) $nav->displaySubMenu();
                                } ?>
                            </li>
                            <?php
                        } ?>
                        <li>
                            <a href="#" class="search-btn search-open-btn" aria-label="Open search"><i class="fas fa-search"></i></a>
                        </li>
                    </ul>
                </nav>
            </div>
            <div class="header-right-elements d-flex align-items-center justify-content-between">
                <!-- Button in main navigation
                <a href="contact.html" class="theme-btn d-none d-sm-inline-block">Get Free Quote</a>-->
                <a href="#" class="side-menu-toggle side-toggle" aria-label="Open menu"><i class="fa-light fa-bars"></i></a>
            </div>
        </div>
    </div>
</header>

<!-- side toggle start -->
<aside>
    <div class="side-info">
        <div class="side-info-content">
            <div class="offset__widget offset__header">
                <div class="offset__logo">
                    <a href="<?php echo $siteContext->getHome()->path; ?>" title="<?php echo PMS_SITE_TITLE; ?>">
                        <img src="<?php echo DOCBASE; ?>assets/images/footer-logo.png" alt="logo">
                    </a>
                </div>
                <button class="side-info-close" aria-label="Close side infos">
                    <i class="fal fa-times"></i>
                </button>
            </div>
            <div class="mobile-menu d-xl-none fix"></div>
            <div class="offset__widget offset__support">
                <ul class="mobile-nav__contact list-unstyled">
                    <?php
                    if(!empty(PMS_EMAIL)){ ?>
                        <li class="d-flex align-items-center">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo PMS_EMAIL; ?>"><?php echo PMS_EMAIL; ?></a>
                        </li>
                        <?php
                    }
                    if(!empty(PMS_PHONE) || !empty(PMS_MOBILE)){ ?>
                        <li class="d-flex align-items-center">
                            <i class="fas fa-phone"></i>
                            <div class="d-flex flex-column">
                                <a href="tel:<?php echo PMS_PHONE; ?>" dir="ltr" aria-label="Telephone"><?php echo PMS_PHONE; ?></a>
                                <a href="tel:<?php echo PMS_MOBILE; ?>" dir="ltr" aria-label="Mobile"><?php echo PMS_MOBILE; ?></a>
                            </div>
                        </li>
                        <?php
                    }
                    if(!empty(PMS_ADDRESS)){ ?>
                        <li class="d-flex align-items-center">
                            <i class="fas fa-map-marker-alt"></i> <?php echo PMS_ADDRESS; ?>
                        </li>
                        <?php
                    } ?>
                </ul>
                <div class="mobile-nav__top">
                    <div class="mobile-nav__social">
                        <?php
                        foreach($siteContext->socials as $s){ ?>
                            <a href="<?php echo $s['url']; ?>" target="_blank" aria-label="<?php echo $s['type']; ?>"><i class="fa-brands fa-<?php echo $s['type']; ?>"></i></a>
                            <?php
                        } ?>
                    </div>
                    <!-- /.mobile-nav__social -->
                </div>
            </div>
        </div>
    </div>
</aside>
<!-- side toggle end -->

<!-- SIDE OPEN OVERLAY -->
<div class="offcanvas-overlay"></div>
<div class="offcanvas-overlay-white"></div>

<!-- PRELOADER -->
<div id="preloader">
    <div class="preloader-close">x</div>
    <div class="sk-three-bounce">
        <div class="sk-child sk-bounce1"></div>
        <div class="sk-child sk-bounce2"></div>
        <div class="sk-child sk-bounce3"></div>
    </div>
</div>

<!-- SIDE SOCIAL MEDIAS -->
<div class="hero-social-elements d-none d-xxl-block">
    <div class="flp-text">
        <p><?php echo $siteContext->texts['FOLLOW_US']; ?></p>
    </div>
    <div class="long-arrow"></div>
    <div class="social-link">
        <?php
        foreach($siteContext->socials as $s){ ?>
            <a href="<?php echo $s['url']; ?>" target="_blank" aria-label="<?php echo $s['type']; ?>"><i class="fa-brands fa-<?php echo $s['type']; ?>"></i></a>
            <?php
        } ?>
    </div>
</div>