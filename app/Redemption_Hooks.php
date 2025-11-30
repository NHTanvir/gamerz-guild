<?php
/**
 * Redemption System hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Redemption_System as Redemption_Class;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Redemption_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class Redemption_Hooks extends Base {

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
		// This first one needs to use the base class method since it needs to call a public method on this class
		$this->action( 'init', 'create_redemption_products' );

		// The rest use direct add_action to call methods from the Redemption class
		add_action( 'wp_ajax_redeem_xp_item', [ new Redemption_Class(), 'handle_redemption_request' ] );
		add_action( 'wp_ajax_nopriv_redeem_xp_item', [ new Redemption_Class(), 'handle_redemption_request' ] );
		add_action( 'wp_head', [ new Redemption_Class(), 'apply_user_cosmetics' ] );
	}

	/**
	 * Create redemption products
	 */
	public function create_redemption_products() {
		$redemption = new Redemption_Class();
		$redemption->create_redemption_products();
	}
}