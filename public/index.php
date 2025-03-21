<?php
if (is_file('../config/config.php')) require_once '../config/config.php';
if(!defined('PMS_ADMIN_FOLDER')) define('PMS_ADMIN_FOLDER', 'admin');
require_once '../common/core/Autoloader.php';

use Pandao\Common\Core\Bootstrap;
use Pandao\Core\Router;

define('ADMIN', false);

$bootstrap = new Bootstrap();
$bootstrap->init();

$db = $bootstrap->getDb();

$router = new Router($db, $bootstrap);
$router->route();