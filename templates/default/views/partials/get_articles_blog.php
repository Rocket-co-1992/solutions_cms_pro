<?php

use Pandao\Common\Utils\UrlUtils;
use Pandao\Common\Utils\DateUtils;
use Pandao\Common\Utils\StrUtils;

$grid = (!isset($grid) || !is_numeric($grid)) ? ceil(12 / 1) : ceil(12 / $grid);
$html = '';
$lz_offset = $_POST['offset'] ?? 1;
$lz_limit = $_POST['limit'] ?? 5;
$page_id = $_POST['page'] ?? null;
$article_id = $_POST['article'] ?? [];

$start_month = null;
$end_month = null;
if (isset($_POST['month']) && is_numeric($_POST['month']) && isset($_POST['year']) && is_numeric($_POST['year'])) {
    $start_month = mktime(0, 0, 0, $_POST['month'], 1, $_POST['year']);
    $end_month = mktime(0, 0, 0, $_POST['month'], date('t', $start_month), $_POST['year']);
}

$tag = (isset($_POST['tag']) && is_numeric($_POST['tag'])) ? $_POST['tag'] : 0;

$articles = $siteContext->getArticles([
    'id_page' => $page_id,
    'limit' => $lz_limit,
    'start_date' => $start_month,
    'end_date' => $end_month,
    'tag' => $tag,
    'offset' => ($lz_offset-1)*$lz_limit
]);

foreach ($articles as $article_id => $article) {
    $article_text = StrUtils::strtrunc(StrUtils::ripTags($article->text), 170);
    $publish_date = $article->publish_date ?? $article->add_date;

    // Prepare tags
    $tags = '';
    if (!empty($article->tags)) $tags = ' tag'.str_replace(',', ' tag', $article->tags);

    $html .= '
    <article class="single-blog-post'.$tags.'" itemprop="blogPosts" itemscope itemtype="http://schema.org/BlogPosting">
        <link itemprop="mainEntityOfPage" href="' . UrlUtils::getUrl(true).$article->path . '">';

    // Get the main image
    $img = $article->getMainImage('big', false, 'article', $article_id);
    if (!empty($img['path'])) {
        $html .= '
        <a itemprop="url" href="' . $article->path . '">
            <figure class="post-featured-thumb img-container mb0" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
                <img alt="' . $article->title . '" src="' . $img['path'] . '" itemprop="url" width="' . $img['w'] . '" height="' . $img['h'] . '">
                <meta itemprop="height" content="' . $img['h'] . '">
                <meta itemprop="width" content="' . $img['w'] . '">
            </figure>
        </a>';
    }

    $html .= '
        <div class="post-content">
            <a href="' . $article->path . '"><h2 itemprop="name headline">' . $article->title . '</h2></a>
            <p>' . $article_text . '</p>
            <div class="d-flex justify-content-between align-items-center mt-30">
                <div class="post-meta">
                    <i class="fa-light fa-fw fa-thumbtack"></i> 
                    <span><time itemprop="dateCreated datePublished dateModified" datetime="' . date('c', $publish_date) . '">';
    $html .= (!PMS_RTL_DIR) ? DateUtils::strftime(PMS_DATE_FORMAT, $publish_date) : DateUtils::strftime('%F', $publish_date);
    $html .= '</time></span>';

    $html .= ' <i class="fa-light fa-fw fa-comment"></i> <span>' . $article->num_comments . ' ' . mb_strtolower($siteContext->texts['COMMENTS'], 'UTF-8') . '</span>';

    // Fetch and display the article's authors
    $user_ids = explode(',', $article->users);
    foreach ($user_ids as $user_id) {
        if (isset($pms_users[$user_id])) {
            $html .= ' <i class="fa-light fa-fw fa-user"></i> <span itemprop="creator author publisher">' . $pms_users[$user_id]['login'] . '</span>';
        }
    }

    // Fetch and display tags
    if (!empty($article_tags)) {
        foreach (explode(',', $article_tags) as $tag_id) {
            if (isset($pms_tags[$tag_id])) {
                $html .= ' <i class="fa-light fa-fw fa-tag"></i> <span>' . $pms_tags[$tag_id]['value'] . '</span>';
            }
        }
    }

    $html .= '
                </div>
                <div class="post-link">
                    <a href="' . $article->path . '"><i class="fa-light fa-arrow-right"></i> ' . $siteContext->texts['READMORE'] . '</a>
                </div>
            </div>
        </div>
    </article>';
}

if(isset($_POST['ajax']) && $_POST['ajax'] == 1)
    echo json_encode(array('html' => $html));
else
    echo $html;