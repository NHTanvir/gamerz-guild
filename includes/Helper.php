<?php
namespace Codexpert\Gamerz_Guild;

if( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

/**
 * Gets the site's base URL
 *
 * @uses get_bloginfo()
 *
 * @return string $url the site URL
 */
if( ! function_exists( 'its_plugin_site_url' ) ) :
function its_plugin_site_url() {
	$url = get_bloginfo( 'url' );

	return $url;
}
endif;

/**
 * Get posts for select field
 */
if( ! function_exists( 'gamerz_get_posts' ) ) :
function gamerz_get_posts( $args = [], $show_none = false, $show_id = false ) {
	$defaults = [
		'post_type' => 'post',
		'posts_per_page' => -1,
		'post_status' => 'publish'
	];

	$args = wp_parse_args( $args, $defaults );

	$posts = get_posts( $args );

	$options = $show_none ? [ '' => __( 'None', 'gamerz-guild' ) ] : [];

	foreach ( $posts as $post ) {
		$key = $show_id ? $post->ID : $post->post_name;
		$options[ $key ] = $show_id ? $post->post_title . ' (' . $post->ID . ')' : $post->post_title;
	}

	return $options;
}
endif;