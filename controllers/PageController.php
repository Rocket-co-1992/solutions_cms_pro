<?php

namespace Pandao\Controllers;

use Pandao\Common\Utils\UrlUtils;
use Pandao\Services\SiteContext;
use Pandao\Models\View;

class PageController extends Controller
{
    /**
     * Displays a page by rendering the corresponding view.
     *
     * @param object $myPage The page object to display.
     * @param bool $autoRender Whether to automatically render the view (default is true).
     */
    public function view($myPage, $autoRender = true)
    {
        if ($myPage === null) {
            UrlUtils::err404();
            return;
        }

        $siteContext = SiteContext::get();
        $siteContext->currentArticle = null;
        
        // Set page view properties
        $myView = new View();
        $myView->name = htmlentities($myPage->name ?? '', ENT_QUOTES);
        $myView->title = htmlentities($myPage->title ?? '', ENT_QUOTES);
        $myView->subtitle = htmlentities($myPage->subtitle ?? '', ENT_QUOTES);
        $myView->title_tag = htmlentities($myPage->title_tag ?? '', ENT_QUOTES);
        $myView->descr = htmlentities($myPage->descr ?? '', ENT_QUOTES);
        $myView->robots = htmlentities($myPage->robots ?? '', ENT_QUOTES);
        $myView->keywords = htmlentities($myPage->keywords ?? '', ENT_QUOTES);
        $myView->published_time = date('c', $myPage->add_date);
        $myView->modified_time = date('c', $myPage->edit_date);
        $myView->object = $myPage;
        $myView->type = 'page';

        $siteContext->view = $myView;

        $this->viewData = array_merge($this->viewData, [
            'siteContext' => $siteContext,
            'myPage' => $myPage,
            'myView' => $myView
        ]);
        
        // Automatically render the page template
        if ($autoRender) {
            $this->render($myPage->getPageTemplate());
        }
    }

    /**
     * Displays an article within a page by rendering the article's view.
     *
     * @param object $myPage The page object containing the article.
     * @param object $myArticle The article object to display.
     * @param bool $autoRender Whether to automatically render the article view (default is true).
     */
    public function viewArticle($myPage, $myArticle, $autoRender = true)
    {
        if ($myPage === null || $myArticle === null) {
            UrlUtils::err404();
            return;
        }

        $siteContext = SiteContext::get();
        $siteContext->currentArticle = $myArticle;
        
        // Set article view properties
        $myView = new View();
        $myView->name = htmlentities($myArticle->title ?? '', ENT_QUOTES);
        $myView->title = htmlentities($myArticle->title ?? '', ENT_QUOTES);
        $myView->subtitle = htmlentities($myArticle->subtitle ?? '', ENT_QUOTES);
        $myView->title_tag = htmlentities($myArticle->title . ' - ' . $myPage->title_tag ?? '', ENT_QUOTES);
        $myView->descr = htmlentities($myPage->descr ?? '', ENT_QUOTES);
        $myView->robots = htmlentities($myPage->robots ?? '', ENT_QUOTES);
        $myView->keywords = htmlentities($myPage->keywords ?? '', ENT_QUOTES);
        $myView->published_time = date('c', $myArticle->publish_date);
        $myView->modified_time = date('c', $myArticle->edit_date);
        $myView->object = $myArticle;
        $myView->type = 'article';
        $myView->isArticle = true;

        $siteContext->view = $myView;
        
        $this->viewData = array_merge($this->viewData, [
            'siteContext' => $siteContext,
            'myPage' => $myPage,
            'myArticle' => $myArticle,
            'myView' => $myView
        ]);
        
        // Automatically render the article template
        if ($autoRender) {
            $this->render($myPage->getArticleTemplate());
        }
    }
}
