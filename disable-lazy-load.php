<?php
/**
 * Plugin Name
 *
 * @package           Disable_Lazy_Load
 * @author            sachyya sachet
 * @copyright         2020 Sachyya
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Disable Lazy Load
 * Plugin URI:        
 * Description:       Simple plugin to disable lazy loading feature on the site or only on specific images.
 * Version:           1.0.2
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * Author:            Sachyya
 * Author URI:        https://sachyya.github.io
 * Text Domain:       disable-lazy-load
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'ABSPATH' ) or die( 'Script Kiddies Go Away' );

if ( !defined( 'DLL_FILE_PATH' ) ) {
    define( 'DLL_FILE_PATH', __FILE__ );
}

if ( !defined( 'DLL_URL' ) ) {
    define( 'DLL_URL', plugin_dir_url( __FILE__ ) );
}

if ( !defined( 'DLL_DIR_PATH' ) ) {
    define( 'DLL_PATH', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'DLL_VERSION' ) ) {
    define( 'DLL_VERSION', '1.0.0' );
}

require_once( DLL_PATH . '/src/class-disable-lazy-load.php' );
