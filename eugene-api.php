<?php

/**
 * Plugin Name: Eugene
 * Description: API Based Plugin
 * Version: 1.0.0
 * Author: Eugene Alegiojo
 * Copyright: (c) 2024
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: eugene-api
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Composer class autoloader
 */
require_once __DIR__ . '/vendor/autoload.php';

new Eugene\API\Main();
