<?php

namespace Pandao\Services;

use Pandao\Models\Widget;

class WidgetService
{
    protected $pms_db;
    protected $siteContext;

    public function __construct($db, $siteContext)
    {
        $this->pms_db = $db;
        $this->siteContext = $siteContext;
    }

    /**
     * Loads the widgets for a given page.
     *
     * @param int $pageId The ID of the page.
     * @param string $lang The language for which to load the widgets.
     * 
     * @return array The list of widgets grouped by position.
     */
    public function loadWidgets($pageId, $lang)
    {
        $widgets = array();
        $stmt = $this->pms_db->prepare('SELECT * FROM solutionsCMS_widget WHERE (allpages = 1 OR FIND_IN_SET(:page_id, pages)) AND lang = :lang AND checked = 1 ORDER BY `rank`');
        $stmt->execute(['page_id' => $pageId, 'lang' => $lang]);

        foreach ($stmt as $row) {
            $widget = new Widget($this->pms_db, $this->siteContext);
            $widget->populateProperties($row);
            $widgets[$row['pos']][] = $widget;
        }

        return $widgets;
    }

    /**
     * Renders the widgets in the specified position.
     *
     * @param array $widgets The list of widgets to render.
     * @param string $position The position of the widgets.
     */
    public function renderWidgets(array $widgets, $position)
    {
        if (isset($widgets[$position])) {
            echo '<div class="widget-' . $position . '">';
            foreach ($widgets[$position] as $widget) {
                echo '<div id="widget-' . $widget->id . '" class="widget';
                if ($widget->class != '') {
                    echo ' ' . $widget->class;
                }
                echo '">';

                if ($widget->showtitle == 1) {
                    echo '<div class="wid-title">
                            <h5>' . $widget->subtitle . '</h5>
                            <h3>' . $widget->title . '</h3>
                          </div>';
                }

                echo '<div class="widget-content">';
                $path = SYSBASE . 'templates/' . PMS_TEMPLATE . '/widgets/';
                if ($widget->type != '' && is_file($path . $widget->type . '.php')) {
                    include($path . $widget->type . '.php');
                } else {
                    echo $widget->content;
                }
                echo '</div></div>';
            }
            echo '</div>';
        }
    }
}
