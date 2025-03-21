<?php

namespace Pandao\Controllers;

use Pandao\Core\Services\WidgetData;

class HomeController extends PageController
{
    /**
     * Displays the homepage by loading and rendering the corresponding data.
     *
     * @param object $myPage The page object representing the homepage.
     * @param bool $autoRender Whether to automatically render the view (default is true).
     */
    public function view($myPage, $autoRender = true)
    {
        parent::view($myPage, false);

        $this->render($myPage->getPageTemplate());
    }
}
