<?php

namespace Pandao\Controllers;

use Pandao\Common\Utils\UrlUtils;
use Pandao\Common\Services\Csrf;
use Pandao\Services\SiteContext;

class Controller
{
    protected $pms_db;
    protected $viewData = [];
    protected $assets;

    /**
     * Constructor for the Controller class.
     * Initializes the database connection.
     *
     * @param Database $db Database connection object.
     */
    public function __construct($db)
    {
        $this->pms_db = $db;
        SiteContext::get($db);
    }

    /**
     * Renders a view with associated data and includes the layout structure.
     *
     * @param string $view The name of the view file to render.
     */
    public function render($view)
    {
        $this->viewData['csrf_token'] = Csrf::generateToken();

        extract($this->viewData);
        ob_start();
        $viewPath = UrlUtils::getFromTemplate('views/' . $view . '.php', false);
        if(is_file($viewPath))
            require_once $viewPath;
        else
        UrlUtils::err404('View ' . $view . ' not found');

        $content = ob_get_clean();
        require_once UrlUtils::getFromTemplate('views/partials/head.php', false);
        require_once UrlUtils::getFromTemplate('views/partials/header.php', false);
        echo $content;
        require_once UrlUtils::getFromTemplate('views/partials/footer.php', false);
    }

    /**
     * Loads and initializes a model with the database connection.
     *
     * @param string $model The name of the model class to load.
     * @return object The initialized model instance.
     */
    protected function loadModel($model)
    {
        return new $model($this->pms_db);
    }
}