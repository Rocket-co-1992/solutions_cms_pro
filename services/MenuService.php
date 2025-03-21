<?php

namespace Pandao\Services;

use Pandao\Models\NavItem;
use Pandao\Common\Utils\UrlUtils;
use \PDO;

class MenuService
{
    protected $pms_db;
    protected $siteContext;

    public function __construct($db, $siteContext)
    {
        $this->pms_db = $db;
        $this->siteContext = $siteContext;
    }

    /**
     * Retrieves all navigation items.
     *
     * @return array List of navigation items grouped by 'main' and 'footer'.
     */
    public function getAllNavItems()
    {
        $navItems = [
            'main' => [],
            'footer' => []
        ];

        $query = 'SELECT * FROM solutionsCMS_menu WHERE checked = 1 AND lang = :lang ORDER BY `rank`';
        $stmt = $this->pms_db->prepare($query);
        $stmt->execute(['lang' => PMS_LANG_ID]);
    
        $result = $stmt->fetchAll();
        if ($result !== false) {
            foreach ($result as $row) {
                if ($this->isValidNavItem($row)) {
                    
                    $navItem = new NavItem($row, $this->siteContext);
                    $navItem->populateProperties($row);

                    $href = $navItem->getNavUrl();
                    $navItem->href = $href;
    
                    // Set the target based on the URL
                    $target = (strpos($href, 'http') !== false) ? '_blank' : '_self';
                    if (strpos($href, UrlUtils::getUrl(true)) !== false) {
                        $target = '_self';
                    }
                    $navItem->target = $target;
    
                    // Group navigation items by 'main' and 'footer'
                    if ($navItem->main == 1) $navItems['main'][$navItem->id] = $navItem;
                    if ($navItem->footer == 1) $navItems['footer'][$navItem->id] = $navItem;
                }
            }
        }

        return $navItems;
    }

    /**
     * Validates if a navigation item is valid based on its type and availability.
     *
     * @param array $row The navigation item data.
     * @return bool True if valid, otherwise false.
     */
    private function isValidNavItem($row)
    {
        if ($row['item_type'] == 'page') {

            $page = $this->siteContext->getPage($row['id_item']);
            return $page !== null && $page->checked == 1;
        }

        if ($row['item_type'] == 'article') {
            $article = $this->siteContext->getArticle($row['id_item']);
            return $article !== null;
        }

        return in_array($row['item_type'], ['url', 'none']);
    }

    /**
     * Loads all social links from the database.
     *
     * @return array List of social links.
     */
    public function getAllSocials()
    {
        $stmt = $this->pms_db->query('SELECT * FROM solutionsCMS_social WHERE checked = 1 ORDER BY `rank`');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Retrieve all navigation items for a given menu type.
     *
     * @param string $menuType The type of menu (e.g., 'main', 'footer').
     * @return array List of NavItem objects.
     */
    public function getMenuItems($menuType)
    {
        $stmt = $this->pms_db->prepare('SELECT * FROM solutionsCMS_menu WHERE checked = 1 AND lang = :lang ORDER BY `rank`');
        $stmt->execute(['lang' => PMS_LANG_ID]);
        $navItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($itemData) {
            $navItem = new NavItem($this->pms_db, $this->siteContext);
            $navItem->populateProperties($itemData);
            return $navItem;
        }, $navItems);
    }

    /**
     * Get top-level navigation items for a specific menu.
     *
     * @param string $menuType The type of the menu (e.g., 'main', 'footer').
     * @param array $allNavItems The full array of navigation items.
     * 
     * @return array List of top-level NavItem objects.
     */
    public function getTopLevelNavItems($menuType, array $allNavItems)
    {
        $topLevelItems = [];

        if (isset($allNavItems[$menuType])) {
            foreach ($allNavItems[$menuType] as $navItem) {
                // Check if the item is a top-level item (no parent or parent is the home page)
                if (empty($navItem->idParent) || (
                    isset($allNavItems[$menuType][$navItem->idParent]) &&
                    $allNavItems[$menuType][$navItem->idParent]->idItem == $this->getHomePage()->id
                )) {
                    $topLevelItems[] = $navItem;
                }
            }
        }

        return $topLevelItems;
    }

    /**
     * Retrieves the home page as a navigation item.
     *
     * @return NavItem|null The home page navigation item or null if not found.
     */
    public function getHomePage()
    {
        foreach ($this->siteContext->allPages as $page) {
            if ($page->home == 1) return $page;
        }
        return null;
    }
}
