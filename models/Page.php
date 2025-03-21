<?php

namespace Pandao\Models;

use Pandao\Common\Utils\StrUtils;
use Pandao\Common\Utils\DbUtils;

class Page extends Content
{
    public $id;
    public $lang;
    public $name;
    public $title;
    public $subtitle;
    public $title_tag;
    public $alias;
    public $descr;
    public $robots;
    public $keywords;
    public $text;
    public $id_parent;
    public $page_model;
    public $article_model;
    public $home;
    public $checked;
    public $rank;
    public $add_date;
    public $edit_date;
    public $comment;
    public $rating;
    public $system;
    public $show_langs;
    public $hide_langs;
    public $path;

    public $articles = [];
    public $widgets = [];
    public $breadcrumbs = [];

    public $featuredHomeArticles = [];
    public $singleFeaturedHomeArticle = [];
    public $articles_disp_ids = [];

    /**
     * Loads page data based on the alias or home flag.
     *
     * @param string|null $alias The page alias.
     * @param bool $home Indicates if the page is the homepage.
     */
    public function loadPage($alias, $home = false)
    {
        $query = 'SELECT * FROM solutionsCMS_page WHERE lang = :lang AND checked = 1';
        $params = ['lang' => PMS_LANG_ID];
        if ($home) {
            $query .= ' AND home = 1';
        } elseif ($alias !== null) {
            $query .= ' AND alias = :alias';
            $params['alias'] = $alias;
        }
        $query .= ' LIMIT 1';

        $stmt = $this->pms_db->prepare($query);
        $stmt->execute($params);
        $pageData = $stmt->fetch();
        if ($pageData) {
            $this->populateProperties($pageData);

            $lang_tag = PMS_LANG_ENABLED ? PMS_LANG_TAG . '/' : '';
            $this->path = DOCBASE . $lang_tag . StrUtils::textFormat($this->alias);

            $this->articles = $this->siteContext->getArticlesByPageId($this->id);
            $this->loadPageFiles();
            $this->loadWidgets();
            $this->loadBreadcrumbs();
            $this->loadComments();
        }
    }

    /**
     * Loads the images associated with the page.
     */
    private function loadPageFiles()
    {
        $stmt = $this->pms_db->prepare('SELECT * FROM solutionsCMS_page_file WHERE id_item = :page_id AND checked = 1 AND lang = :lang AND `type` = \'image\' ORDER BY `rank`');
        $stmt->execute(['page_id' => $this->id, 'lang' => $this->lang]);
        $this->images = $stmt->fetchAll();
    }

    /**
     * Loads the breadcrumbs for the current page based on the parent pages.
     */
    private function loadBreadcrumbs()
    {
        $this->breadcrumbs = array();
        $id_parent = $this->id_parent;
        while (isset($this->siteContext->parents[$id_parent])) {
            if ($id_parent > 0 && $id_parent != $this->siteContext->getHome()->id) {
                $this->breadcrumbs[] = $id_parent;
                $id_parent = $this->siteContext->parents[$id_parent]->id_parent;
            } else {
                break;
            }
        }
        $this->breadcrumbs = array_reverse($this->breadcrumbs);
    }

    /**
     * Populates the properties of the page with the provided data.
     *
     * @param array $pageData The data to populate the properties.
     */
    public function populateProperties($pageData)
    {
        foreach ($pageData as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Retrieves featured home articles for the page.
     *
     * @return array The featured home articles.
     */
    public function getFeaturedHomeArticles()
    {
        if (empty($this->featuredHomeArticles)) {
            $this->featuredHomeArticles = $this->siteContext->getArticles([
                'home' => 1,
                'excluded_ids' => $this->articles_disp_ids,
                'checked' => 1,
                'lang' => PMS_LANG_ID,
                'limit' => 3,
                'current_time' => time()
            ]);
            $this->articles_disp_ids += array_keys($this->featuredHomeArticles);
        }
        return $this->featuredHomeArticles;
    }

    /**
     * Retrieves a single featured home article for the page.
     *
     * @return array The single featured home article.
     */
    public function getSingleFeaturedHomeArticle()
    {
        if (empty($this->singleFeaturedHomeArticle)) {
            $this->singleFeaturedHomeArticle = $this->siteContext->getArticles([
                'home' => 1,
                'excluded_ids' => $this->articles_disp_ids,
                'limit' => 1
            ]);
            $this->articles_disp_ids += array_keys($this->singleFeaturedHomeArticle);
        }
        return $this->singleFeaturedHomeArticle;
    }

    /**
     * Retrieves the slides associated with the page.
     *
     * @return array The list of slides.
     */
    public function getSlides()
    {
        $slides = [];

        $result_slide = $this->pms_db->prepare('SELECT s.id AS slide_id, s.legend, f.id AS file_id, f.file
                                                FROM solutionsCMS_slide s
                                                LEFT JOIN solutionsCMS_slide_file f ON s.id = f.id_item AND f.checked = 1 AND f.lang = :lang_f AND f.type = \'image\'
                                                WHERE s.id_page = :id_page AND s.checked = 1 AND s.lang = :lang_s
                                                ORDER BY s.rank, f.rank');

        $result_slide->execute(['id_page' => $this->id, 'lang_f' => $this->lang, 'lang_s' => $this->lang]);

        if ($result_slide !== false) {
            foreach ($result_slide as $row) {
                $path = 'medias/slide/big/' . $row['file_id'] . '/' . $row['file'];
                $webp_path = preg_replace('/\.\w+$/', '.webp', $path);
                $imgpath = file_exists(SYSBASE . 'public/' . $webp_path) ? DOCBASE . $webp_path : DOCBASE . $path;

                $slides[] = [
                    'path' => $imgpath,
                    'legend' => $row['legend']
                ];
            }
        }

        return $slides;
    }

    /**
     * Retrieves the popup information for the page if it exists.
     *
     * @return array|null The popup data or null if no popup is found.
     */
    public function getPopup()
    {
        $result_popup = $this->pms_db->prepare('SELECT * FROM solutionsCMS_popup
                                                WHERE lang = :lang
                                                    AND checked = 1 
                                                    AND (publish_date IS NULL || publish_date <= ' . time() . ')
                                                    AND (unpublish_date IS NULL || unpublish_date > ' . time() . ')
                                                    AND (allpages = 1 OR pages REGEXP :regid)
                                                LIMIT 1');

        $result_popup->execute(['regid' => '(^|,)' . $this->id . '(,|$)', 'lang' => $this->lang]);

        if ($result_popup !== false && DbUtils::lastRowCount($this->pms_db) > 0) {
            $row = $result_popup->fetch();

            if (!isset($_SESSION['popup_' . $row['id']])) {
                $_SESSION['popup_' . $row['id']] = 1;

                return $row;
            }
        }
        return null;
    }

    /**
     * Gets the template for the articles.
     *
     * @return string The template name for the articles.
     */
    public function getArticleTemplate()
    {
        return $this->article_model;
    }

    /**
     * Gets the template for the page.
     *
     * @return string The template name for the page.
     */
    public function getPageTemplate()
    {
        return $this->page_model;
    }

    /**
     * Loads the widgets associated with the page using WidgetService.
     */
    public function loadWidgets()
    {
        $widgetService = $this->siteContext->getWidgetService();
        $this->widgets = $widgetService->loadWidgets($this->id, $this->lang);
    }

    /**
     * Renders the widgets in the specified position using WidgetService.
     *
     * @param string $position The position of the widgets.
     */
    public function renderWidgets($position)
    {
        $widgetService = $this->siteContext->getWidgetService();
        $widgetService->renderWidgets($this->widgets, $position);
    }
}
