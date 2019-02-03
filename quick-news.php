<?php

/*
Plugin Name: Quick News
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: terekhin
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

namespace QNews;

use QNews\src\TD_QNews;

define('TD_QNEWS_VERSION','1.0.0');
define('TD_QNEWS_ROOT_PATH',__FILE__);
define('TD_QNEWS_PLUGIN_URL',plugin_dir_url(TD_QNEWS_ROOT_PATH));
define('TD_QNEWS_PLUGIN_DIR',dirname(__FILE__)."/");
require_once TD_QNEWS_PLUGIN_DIR.'src/TD_QNews.php';
TD_QNews::getInstance();