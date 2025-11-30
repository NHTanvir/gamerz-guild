<?php
/**
 * Event Integration hooks for Gamerz Guild
 */
namespace Codexpert\Gamerz_Guild\App;

use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Event_Integration as Event_Class;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Event_Hooks
 * @author NH Tanvir <hi@tanvir.io>
 */
class Event_Hooks extends Base {

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

		$this->init();
	}

	/**
	 * Initialize the event integration hooks
	 */
	public function init() {
		// Check if The Events Calendar is active
		if ( ! class_exists( 'Tribe__Events__Main' ) ) {
			return;
		}

		$this->setup_hooks();
	}

	/**
	 * Setup hooks
	 */
	public function setup_hooks() {
		// Hook into event attendance
		add_action( 'tribe_events_attendee_created', [ new Event_Class(), 'handle_event_attendance' ], 10, 2 );

		// Hook into event creation by guild members
		add_action( 'tribe_events_new_event', [ new Event_Class(), 'handle_event_creation' ], 10, 1 );

		// Add XP for event participation and victories
		add_action( 'transition_post_status', [ new Event_Class(), 'handle_event_status_change' ], 10, 3 );
	}
}