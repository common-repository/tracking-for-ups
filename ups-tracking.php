<?php

/**
 * @link              https://github.com/aarsla/tracking-for-ups
 * @since             1.0.0
 * @package           UpsTracking
 *
 * @wordpress-plugin
 * Plugin Name:       Tracking for UPS
 * Plugin URI:        https://github.com/aarsla/tracking-for-ups
 * Description:       Track packages via UPS API
 * Version:           1.2
 * Author:            Byte Logic
 * Author URI:        https://byte-logic.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ups-tracking
 * Domain Path:       /Languages
 */

// In strict mode, only a variable of exact type of the type declaration will be accepted.
declare(strict_types=1);

namespace UpsTracking;

use UpsTracking\Includes\Activator;
use UpsTracking\Includes\Deactivator;
use UpsTracking\Includes\Updater;
use UpsTracking\Includes\Main;

// If this file is called directly, abort.
if (!defined('ABSPATH')) exit;

// Autoloader
require_once plugin_dir_path(__FILE__) . 'Autoloader.php';

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('UPS_TRACKING_VERSION', '1.2');

/**
 * The string used to uniquely identify this plugin.
 */
define('UPS_TRACKING_SLUG', 'tracking-for-ups');

/**
 * Configuration data
 *  - db-version:   Start with 0 and increment by 1. It should not be updated with every plugin update,
 *                  only when database update is required.
 */
$configuration = array(
    'version'       => UPS_TRACKING_VERSION,
    'db-version'    => 0
);

/**
 * The ID for the configuration options in the database.
 */
$configurationOptionName = UPS_TRACKING_SLUG . '-configuration';
    
/**
 * The code that runs during plugin activation.
 * This action is documented in Includes/Activator.php
 */
register_activation_hook(__FILE__, function() use($configuration, $configurationOptionName) {Activator::activate($configuration, $configurationOptionName);});

/**
 * The code that runs during plugin deactivation.
 * This action is documented in Includes/Deactivator.php
 */
register_deactivation_hook(__FILE__, function() {Deactivator::deactivate();});

/**
 * Update the plugin.
 * It runs every time, when the plugin is started.
 */
add_action('plugins_loaded', function() use ($configuration, $configurationOptionName) {Updater::update($configuration['db-version'], $configurationOptionName);}, 1);

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks
 * kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function runPlugin(): void
{
    $plugin = new Main();
    $plugin->run();
}
runPlugin();
