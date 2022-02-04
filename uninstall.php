<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * @link       https://www.vickycodeaddict.com/extreme-search-suggestion-for-woocommerce/
 * @since      1.0.0
 *
 * @package    Extreme_Search
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'wc_settings_tab_extreme_search_selector' );
delete_option( 'wc_settings_tab_extreme_search_section_display_field' );
delete_option( 'wc_settings_tab_extreme_search_popup_search' );
delete_option( 'wc_settings_tab_extreme_search_section_display_terms' );
delete_option( 'wc_settings_tab_extreme_search_section_search_field' );
delete_option( 'wc_settings_tab_extreme_search_section_search_terms' );
delete_option( 'wc_settings_tab_extreme_search_exclude_product' );
delete_option( 'wc_settings_tab_extreme_search_section_search_min_char' );
delete_option( 'wc_settings_tab_extreme_search_redirect' );