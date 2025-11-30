<?php
/**
 * Guild hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Guild as Guild_Class;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Guild_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class Guild_Hooks extends Base {

	public $plugin;
	public $slug;
	public $name;
	public $version;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->version	= $this->plugin['Version'];

		$this->setup_hooks();
	}

	/**
	 * Setup hooks
	 */
	public function setup_hooks() {
		// Add guild meta fields and Gutenberg support
		add_action( 'add_meta_boxes', [ new Guild_Class(), 'add_meta_boxes' ] );
		// For save_post, we can use our local wrapper method
		$this->action( 'save_post', 'save_guild_meta_box', 10, 1 );
		// Use higher priority to ensure it runs after core Gutenberg scripts are loaded
		add_action( 'enqueue_block_editor_assets', [ new Guild_Class(), 'enqueue_gutenberg_assets' ], 20 );
	}

	/**
	 * Wrapper for saving guild meta box
	 */
	public function save_guild_meta_box( $post_id ) {
		$guild = new Guild_Class();
		$guild->save_meta_box( $post_id );
	}
}