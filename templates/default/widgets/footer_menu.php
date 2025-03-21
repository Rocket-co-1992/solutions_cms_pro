<?php

use Pandao\Common\Utils\UrlUtils;

debug_backtrace() || die ("Direct access not permitted");

$siteContext = Pandao\Services\SiteContext::get();
$languages = $siteContext->languages; ?>

<ul>
    <?php
    foreach($siteContext->footerMenu as $nav_id => $nav){ ?>
		<li class="mb5"><a href="<?php echo $nav->href; ?>" title="<?php echo $nav->title; ?>"><?php echo $nav->name; ?></a></li>
		<?php
    } ?>
</ul>
<?php
if(PMS_LANG_ENABLED){
    if(!empty($languages)){ ?>
        <div class="dropdown mt20 float-start">
            <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?php echo $languages[PMS_LANG_TAG]['image']; ?>" alt="<?php echo $languages[PMS_LANG_TAG]['title']; ?>" class="me-1"><span class="d-none d-md-inline"> <?php echo $languages[PMS_LANG_TAG]['title']; ?></span>
            </button>
            <ul class="dropdown-menu">
                <?php
                foreach($languages as $tag => $row){
                    $title_lang = $row['title']; ?>
                    <li><a class="dropdown-item" href="<?php echo DOCBASE . $tag; ?>"><img src="<?php echo $row['image']; ?>" alt="<?php echo $title_lang; ?>"> <?php echo $title_lang; ?></a></li>
                    <?php
                } ?>
            </ul>
        </div>
        <?php
    }
}
if(PMS_CURRENCY_ENABLED){
    if(count($pms_currencies) > 0){ ?>
         <div class="dropdown mt20 float-start">
            <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span><?php echo PMS_CURRENCY_CODE; ?></span><span class="hidden-sm hidden-md"> <?php echo PMS_CURRENCY_SIGN; ?></span> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php
                foreach($pms_currencies as $row){ ?>
                    <li>
                        <a href="<?php echo UrlUtils::getUrl(); ?>" data-action="<?php echo DOCBASE . 'includes/change_currency.php'; ?>?curr=<?php echo $row['id']; ?>" class="dropdown-item ajax-link<?php if(!isset($_SESSION['currency']['code'])) echo ' currency-'.$row['code']; ?>">
                            <?php echo $row['code'].' '.$row['sign']; ?>
                        </a>
                    </li>
                    <?php
                } ?>
            </ul>
        </div>
        <?php
    }
} ?>
