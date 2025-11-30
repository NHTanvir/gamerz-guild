<?php
/**
 * Guild Activity hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Guild_Activity as Guild_Activity_Class;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Guild_Activity_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class Guild_Activity_Hooks extends Base {

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
		// Trigger guild activities
		add_action( 'gamerz_guild_created', [ new Guild_Activity_Class(), 'activity_guild_created' ], 10, 2 );
		add_action( 'gamerz_guild_member_added', [ new Guild_Activity_Class(), 'activity_member_joined' ], 10, 3 );
		add_action( 'gamerz_guild_member_removed', [ new Guild_Activity_Class(), 'activity_member_left' ], 10, 2 );
		add_action( 'gamerz_guild_member_promoted', [ new Guild_Activity_Class(), 'activity_member_promoted' ], 10, 3 );
		add_action( 'gamerz_guild_member_demoted', [ new Guild_Activity_Class(), 'activity_member_demoted' ], 10, 3 );
	}
}