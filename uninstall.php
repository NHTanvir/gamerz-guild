<?php
/**
 * Perform when the plugin is being uninstalled
 */

// If uninstall is not called, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

$deletable_options = [ 'gamerz-guild_version', 'gamerz-guild_install_time', 'gamerz-guild_docs_json', 'tanvir-blog-json' ];
foreach ( $deletable_options as $option ) {
    delete_option( $option );
}