<?php

if (is_file('../config/config.php')) require_once '../config/config.php';
if(!defined('PMS_ADMIN_FOLDER')) define('PMS_ADMIN_FOLDER', 'admin');
require_once '../common/core/Autoloader.php';

use Pandao\Admin\Core\AdminBootstrap;
use Pandao\Admin\Core\Router;

define('ADMIN', true);

$bootstrap = new AdminBootstrap();
$bootstrap->init();

$db = $bootstrap->getDb();

$router = new Router($db);
$router->route();
