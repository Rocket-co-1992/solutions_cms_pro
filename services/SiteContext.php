<?php

namespace Pandao\Services;

use Pandao\Services\PageService;
use Pandao\Services\ArticleService;
use Pandao\Services\MenuService;
use Pandao\Services\widgetService;
use Pandao\Common\Models\LangManager;

class SiteContext
{
    private static $instance = null;

    protected $pms_db;
    protected $pageService;
    protected $articleService;
    protected $menuService;
    protected $widgetService;
    protected $commentService;
    protected $langManager;

    public $currentPage;
    public $currentArticle;
    public $view;
    public $mainMenu;
    public $footerMenu;
    public $allPages = [];
    public $allArticles = [];
    public $allNavItems = [];
    public $parents = [];
    public $texts = [];
    public $socials = [];
    public $langs = [];
    public $languages = [];

    /**
     * Constructor to initialize the SiteContext instance with all necessary services.
     *
     * @param Database $db The database connection object.
     */
    private function __construct($db)
    {
        $this->pms_db = $db;

        $this->pageService = new PageService($this->pms_db, $this);
        $this->articleService = new ArticleService($this->pms_db, $this);
        $this->menuService = new MenuService($this->pms_db, $this);
        $this->widgetService = new WidgetService($this->pms_db, $this);
        $this->langManager = new LangManager($this->pms_db);
        $this->commentService = new CommentService($this->pms_db);

        $this->loadTexts();
        $this->loadSocials();
        $this->loadAllPages();
        $this->loadAllArticles();
        $this->loadAllNavItems();
        $this->mainMenu = $this->menuService->getTopLevelNavItems('main', $this->allNavItems);
        $this->footerMenu = $this->menuService->getTopLevelNavItems('footer', $this->allNavItems);
        $this->languages = (PMS_LANG_ENABLED) ? $this->langManager->getLanguagesWithImages() : [['id' => 0, 'title' => '', 'image' => '']];

    }

    /**
     * Singleton method to get the instance of SiteContext.
     *
     * @param Database $db The database connection object (required for the first call).
     * 
     * @return SiteContext The instance of SiteContext.
     * @throws \Exception if the database connection is not provided for the first instance.
     */
    public static function get($db = null)
    {
        if (self::$instance === null) {
            if ($db === null) {
                throw new \Exception("Database connection is required for the first instance of SiteContext.");
            }
            self::$instance = new self($db);
        }
        return self::$instance;
    }

    /**
     * Loads all the pages using PageService.
     */
    private function loadAllPages()
    {
        $result = $this->pageService->getAllPages();
        
        $this->allPages = $result['pages'];
        $this->parents = $result['parents'];
    }

    /**
     * Loads all the articles using ArticleService.
     */
    private function loadAllArticles()
    {
        $this->allArticles = $this->articleService->getAllArticles(array_keys($this->allPages), $this->allPages);
    }

    /**
     * Loads all navigation items using MenuService.
     */
    private function loadAllNavItems()
    {
        $this->allNavItems = $this->menuService->getAllNavItems();
    }

    /**
     * Loads the texts from the database.
     */
    private function loadTexts()
    {
        $stmt = $this->pms_db->query('SELECT * FROM solutionsCMS_text WHERE lang = ' . PMS_LANG_ID);
        $this->texts = [];
        foreach ($stmt as $row) {
            $this->texts[$row['name']] = $row['value'];
        }
    }

    /**
     * Loads the social links from the database.
     */
    private function loadSocials()
    {
        $this->socials = $this->menuService->getAllSocials();
    }

    /**
     * Returns a page based on its ID or alias.
     *
     * @param int|string $identifier
     * @return Page|null
     */
    public function getPage($identifier)
    {
        return $this->pageService->getPage($identifier);
    }

    /**
     * Returns an article based on its ID or alias.
     *
     * @param int|string $identifier
     * @return Article|null
     */
    public function getArticle($identifier)
    {
        return $this->articleService->getArticle($identifier);
    }

    /**
     * Returns an article based on its alias.
     *
     * @param string $alias
     * @return Article|null
     */
    public function getArticleByAlias($alias)
    {
        return $this->articleService->getArticleByAlias($alias);
    }

    /**
     * Retrieves all articles by page ID.
     *
     * @param int $pageId
     * @return array
     */
    public function getArticlesByPageId($pageId)
    {
        return $this->articleService->getArticlesByPageId($pageId);
    }

    /**
     * Returns the home page.
     *
     * @return Page|null
     */
    public function getHome()
    {
        return $this->pageService->getHomePage();
    }

    /**
     * Retrieves the top-level page ID from the current page hierarchy.
     *
     * @return int The top-level page ID.
     */
    public function getTopPageId()
    {
        $currPageId = $this->currentPage->id;
        return $this->pageService->getTopPageId($currPageId);
    }

        /**
         * Retrieves filtered articles based on various options.
         *
         * @param array $options
         * @return array
         */
            public function getArticles($options = [])
            {
                return $this->articleService->getFilteredArticles($options);
            }

    /**
     * Filters articles by a specific month range.
     *
     * @param array $articles
     * @param int|null $startMonth
     * @param int|null $endMonth
     * @return array
     */
    public function filterArticlesByMonth($articles, $startMonth = null, $endMonth = null)
    {
        return $this->articleService->filterArticlesByMonth($articles, $startMonth, $endMonth);
    }

    /**
     * Get the number of articles per month for the current page.
     *
     * @return array Associative array. Keys are the timestamps for the first day of each month
     *               and the values are the count of articles for that month.
     */
    public function getArticlesCountByMonth()
    {
        return $this->articleService->getArticlesCountByMonth($this->currentPage->id);
    }

    /**
     * Get the WidgetService instance.
     *
     * @return WidgetService
     */
    public function getWidgetService()
    {
        return $this->widgetService;
    }

    /**
     * Get the CommentService instance.
     *
     * @return CommentService
     */
    public function getCommentService()
    {
        return $this->commentService;
    }

    /**
     * Get the MenuService instance.
     *
     * @return MenuService
     */
    public function getMenuService()
    {
        return $this->menuService;
    }
}
