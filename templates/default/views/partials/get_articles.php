<?php

$html = '';

$grid = $_POST['grid'] ?? 4;
$article_id = $_POST['article'] ?? 0;

$articles = $siteContext->getArticles([
    'id_page' => $_POST['page'] ?? null,
    'excluded_ids' => [$article_id],
    'limit' => $_POST['limit'],
    'offset' => $_POST['offset']
]);

if (!empty($articles)) {

    foreach ($articles as $article_id => $article) {
        
        if ($article->tags != '') $article->tags = ' tag' . str_replace(',', ' tag', $article->tags);

        $html .= '
        <article class="col-lg-' . $grid . ' col-md-6 over isotopeItem' . $article->tags . '" itemscope itemtype="http://schema.org/Article">
            <div class="project-item-card">
                <a itemprop="url" href="' . $article->path . '" class="d-block popup-link img-container md">';

        // Get the main image using pms_getMainImage
        $img = $article->getMainImage('medium', false);
        if (!empty($img['path'])) {
            $html .= '<img alt="' . $article->title . '" src="' . $img['path'] . '" width="' . $img['w'] . '" height="' . $img['h'] . '">';
        }

        $html .= '
                </a>
                <div class="contents">
                    <a itemprop="url" href="' . $article->path . '" class="project-link"><i class="fa-light fa-plus"></i></a>
                    <span>' . $article->subtitle . '</span>
                    <h3><a href="' . $article->path . '">' . $article->title . '</a></h3>
                </div>
            </div>
        </article>';
    }
}
if(isset($_POST['ajax']) && $_POST['ajax'] == 1)
    echo json_encode(array('html' => $html));
else
    echo $html;