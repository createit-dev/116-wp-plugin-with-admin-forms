<?php
// /wp-content/plugins/ct-wp-admin-form/ct-wp-admin-form.php
/**
 * Plugin Name:       CT WP Admin Form Example
 * Description:       Create custom wp-admin forms
 * Version:           1.0.0
 * Text Domain:       ct-admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'CT_WP_ADMIN_VERSION', '1.0.0' );

define( 'CT_WP_ADMIN_DIR', 'ct-wp-admin-form' );

/**
 * Helpers
 */
require plugin_dir_path( __FILE__ ) . 'includes/helpers.php';


/**
 * The core plugin class
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ct-wp-admin-form.php';


function run_ct_wp_admin_form() {

    $plugin = new Ct_Admin_Form();
    $plugin->init();

}
run_ct_wp_admin_form();

