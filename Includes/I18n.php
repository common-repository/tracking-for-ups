<?php

declare( strict_types=1 );

namespace UpsTracking\Includes;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/aarsla/tracking-for-ups
 * @since      1.0.0
 * @package    UpsTracking
 * @subpackage UpsTracking/Includes
 * @author     Aid Arslanagic <aarsla@gmail.com>
 */
class I18n {
	/**
	 * Unique identifier for retrieving translated strings.
	 *
	 * @since    1.0.0
	 */
	protected string $domain;

	/**
	 * Initialize the text domain for i18n.
	 *
	 * @param   $domain     Textdomain ID.
	 *
	 * @since    1.0.0
	 */
	public function __construct( string $domain ) {
		$this->domain = $domain;
	}

	/**
	 * Register all the hooks of this class.
	 *
	 * @since    1.0.0
	 */
	public function initializeHooks(): void {
		add_action( 'plugins_loaded', array( $this, 'loadPluginTextdomain' ), 10 );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function loadPluginTextdomain(): void {
		if ( load_plugin_textdomain( $this->domain, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/Languages/' ) === false ) {
			//exit('Textdomain could not be loaded from: ' . dirname(dirname(plugin_basename(__FILE__ ))) . '/Languages/');
		}
	}
}
