<?php

debug_backtrace() || die ("Direct access not permitted");

$siteContext = Pandao\Services\SiteContext::get();
$topId = $siteContext->getTopPageId();
$topPage = $siteContext->parents[$topId] ?? null;
if($topPage){ ?>

    <div class="widget-title"><?php echo $topPage->name; ?></div>
    <ul id="pages-list">
        <?php
        foreach($siteContext->parents[$topId] as $page){ ?>
            <li>
                <a href="<?php echo $page->path; ?>"><?php echo $page->name; ?></a>
                <?php
                if(isset($siteContext->parents[$page->id])){ ?>
                    <ul class="nostyle">
                        <?php
                        foreach($siteContext->parents[$id] as $sub_page){ ?>
                            <li>
                                <a href="<?php echo $sub_page->path; ?>"><?php echo $sub_page->name; ?></a>
                            </li>
                            <?php
                        } ?>
                    </ul>
                    <?php
                } ?>
            </li>
            <?php
        } ?>
    </ul>
    <?php
}