<?php
/**
 * Main class to load all Gamerz Guild functionality
 */
namespace Codexpert\Gamerz_Guild;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Main
 * @author NH Tanvir <hi@tanvir.io>
 */
class Main {

	/**
	 * Plugin instance
	 *
	 * @access private
	 *
	 * @var Main
	 */
	private static $_instance;

	/**
	 * The constructor method
	 *
	 * @access private
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		/**
		 * Includes required files
		 */
		$this->include();

		/**
		 * Runs actual hooks
		 */
		$this->hook();
	}

	/**
	 * Includes files
	 *
	 * @access private
	 *
	 * @uses composer
	 * @uses psr-4
	 */
	private function include() {
		$classes_dir = dirname( __FILE__ ) . '/classes/';

		// Load classes and log if files exist
		$classes_to_load = [
			'Guild.php',
			'Guild_Member.php',
			'Guild_Activity.php',
			'XP_System.php',
			'Rank_System.php',
			'Badge_System.php',
			'Redemption_System.php',
			'Leaderboard.php',
			'Challenges.php',
			'Discord_Integration.php',
			'Forum_Integration.php',
			'Event_Integration.php',
			'Visual_Enhancements.php'
		];

		foreach ( $classes_to_load as $class_file ) {
			$file_path = $classes_dir . $class_file;
			if ( file_exists( $file_path ) ) {
				require_once( $file_path );
			} else {
				error_log( "Gamerz Guild: Missing class file - " . $file_path );
			}
		}
	}

	/**
	 * Hooks
	 *
	 * @access private
	 *
	 * Executes main plugin features
	 */
	private function hook() {
		error_log( "Gamerz Guild: Plugin initialized" );
		// Initialize all systems - always initialize basic functionality
		add_action( 'init', [ $this, 'initialize_systems' ] );

		// Add our custom post types and taxonomies
		add_action( 'init', [ $this, 'register_post_types' ] );

		// Add admin menu
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
	}

	/**
	 * Initialize all systems
	 */
	public function initialize_systems() {
		// Initialize systems - always initialize core functionality (including shortcodes)
		try {
			new Classes\Leaderboard();
			error_log( "Gamerz Guild: Leaderboard initialized" );
		} catch (Exception $e) {
			error_log( "Gamerz Guild: Error initializing Leaderboard - " . $e->getMessage() );
		}
		try {
			new Classes\Challenges();
			error_log( "Gamerz Guild: Challenges initialized" );
		} catch (Exception $e) {
			error_log( "Gamerz Guild: Error initializing Challenges - " . $e->getMessage() );
		}

		// Initialize systems that require myCRED only if myCRED is active
		if ( class_exists( 'myCRED' ) ) {
			error_log( "Gamerz Guild: Initializing myCRED-dependent systems" );
			try {
				new Classes\XP_System();
				error_log( "Gamerz Guild: XP_System initialized" );
			} catch (Exception $e) {
				error_log( "Gamerz Guild: Error initializing XP_System - " . $e->getMessage() );
			}
			try {
				new Classes\Rank_System();
				error_log( "Gamerz Guild: Rank_System initialized" );
			} catch (Exception $e) {
				error_log( "Gamerz Guild: Error initializing Rank_System - " . $e->getMessage() );
			}
			try {
				new Classes\Badge_System();
				error_log( "Gamerz Guild: Badge_System initialized" );
			} catch (Exception $e) {
				error_log( "Gamerz Guild: Error initializing Badge_System - " . $e->getMessage() );
			}
			try {
				new Classes\Redemption_System();
				error_log( "Gamerz Guild: Redemption_System initialized" );
			} catch (Exception $e) {
				error_log( "Gamerz Guild: Error initializing Redemption_System - " . $e->getMessage() );
			}
		} else {
			error_log( "Gamerz Guild: myCRED not active, skipping dependent systems" );
		}

		if ( class_exists( 'bbPress' ) ) {
			try {
				new Classes\Forum_Integration();
				error_log( "Gamerz Guild: Forum_Integration initialized" );
			} catch (Exception $e) {
				error_log( "Gamerz Guild: Error initializing Forum_Integration - " . $e->getMessage() );
			}
		}

		if ( class_exists( 'Tribe__Events__Main' ) ) {
			try {
				new Classes\Event_Integration();
				error_log( "Gamerz Guild: Event_Integration initialized" );
			} catch (Exception $e) {
				error_log( "Gamerz Guild: Error initializing Event_Integration - " . $e->getMessage() );
			}
		}

		// Initialize core guild functionality
		try {
			new Classes\Guild();
			error_log( "Gamerz Guild: Guild initialized" );
		} catch (Exception $e) {
			error_log( "Gamerz Guild: Error initializing Guild - " . $e->getMessage() );
		}
		try {
			new Classes\Guild_Member();
			error_log( "Gamerz Guild: Guild_Member initialized" );
		} catch (Exception $e) {
			error_log( "Gamerz Guild: Error initializing Guild_Member - " . $e->getMessage() );
		}
		try {
			new Classes\Guild_Activity();
			error_log( "Gamerz Guild: Guild_Activity initialized" );
		} catch (Exception $e) {
			error_log( "Gamerz Guild: Error initializing Guild_Activity - " . $e->getMessage() );
		}

		// Initialize visual enhancements
		try {
			new Classes\Visual_Enhancements();
			error_log( "Gamerz Guild: Visual_Enhancements initialized" );
		} catch (Exception $e) {
			error_log( "Gamerz Guild: Error initializing Visual_Enhancements - " . $e->getMessage() );
		}

		// Initialize Discord integration (if configured)
		try {
			new Classes\Discord_Integration();
			error_log( "Gamerz Guild: Discord_Integration initialized" );
		} catch (Exception $e) {
			error_log( "Gamerz Guild: Error initializing Discord_Integration - " . $e->getMessage() );
		}
	}

	/**
	 * Register custom post types
	 */
	public function register_post_types() {
		// Register Guild post type
		$guild_labels = array(
			'name'                  => _x( 'Guilds', 'Post type general name', 'gamerz-guild' ),
			'singular_name'         => _x( 'Guild', 'Post type singular name', 'gamerz-guild' ),
			'menu_name'             => _x( 'Guilds', 'Admin Menu text', 'gamerz-guild' ),
			'name_admin_bar'        => _x( 'Guild', 'Add New on Toolbar', 'gamerz-guild' ),
			'add_new'               => __( 'Add New', 'gamerz-guild' ),
			'add_new_item'          => __( 'Add New Guild', 'gamerz-guild' ),
			'new_item'              => __( 'New Guild', 'gamerz-guild' ),
			'edit_item'             => __( 'Edit Guild', 'gamerz-guild' ),
			'view_item'             => __( 'View Guild', 'gamerz-guild' ),
			'all_items'             => __( 'All Guilds', 'gamerz-guild' ),
			'search_items'          => __( 'Search Guilds', 'gamerz-guild' ),
			'parent_item_colon'     => __( 'Parent Guilds:', 'gamerz-guild' ),
			'not_found'             => __( 'No guilds found.', 'gamerz-guild' ),
			'not_found_in_trash'    => __( 'No guilds found in Trash.', 'gamerz-guild' ),
			'featured_image'        => _x( 'Guild Cover Image', 'Overrides the “Featured Image” phrase', 'gamerz-guild' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase', 'gamerz-guild' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase', 'gamerz-guild' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase', 'gamerz-guild' ),
			'archives'              => _x( 'Guild archives', 'The post type archive label used in nav menus', 'gamerz-guild' ),
			'insert_into_item'      => _x( 'Insert into guild', 'Overrides the “Insert into post” phrase', 'gamerz-guild' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this guild', 'Overrides the “Uploaded to this post” phrase', 'gamerz-guild' ),
			'filter_items_list'     => _x( 'Filter guilds list', 'Screen reader text for the filter links', 'gamerz-guild' ),
			'items_list_navigation' => _x( 'Guilds list navigation', 'Screen reader text for the pagination', 'gamerz-guild' ),
			'items_list'            => _x( 'Guilds list', 'Screen reader text for the items list', 'gamerz-guild' ),
		);

		$guild_args = array(
			'labels'             => $guild_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'guild' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'comments' ),
			'menu_icon'          => 'dashicons-groups',
			'show_in_rest'       => true,
		);

		register_post_type( 'guild', $guild_args );

		// Register meta fields for Gutenberg compatibility
		register_post_meta( 'guild', '_guild_tagline', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'string',
			'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
		] );

		register_post_meta( 'guild', '_guild_description', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'string',
			'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
		] );

		register_post_meta( 'guild', '_guild_max_members', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'integer',
			'default' => 20,
			'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
		] );

		register_post_meta( 'guild', '_guild_creator_id', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'integer',
			'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
		] );

		register_post_meta( 'guild', '_guild_status', [
			'show_in_rest' => true,
			'single' => true,
			'type' => 'string',
			'default' => 'active',
			'auth_callback' => function() { return current_user_can( 'edit_posts' ); },
		] );

		// Register Challenges post type
		$challenge_labels = array(
			'name'                  => _x( 'Challenges', 'Post type general name', 'gamerz-guild' ),
			'singular_name'         => _x( 'Challenge', 'Post type singular name', 'gamerz-guild' ),
			'menu_name'             => _x( 'Challenges', 'Admin Menu text', 'gamerz-guild' ),
			'name_admin_bar'        => _x( 'Challenge', 'Add New on Toolbar', 'gamerz-guild' ),
			'add_new'               => __( 'Add New', 'gamerz-guild' ),
			'add_new_item'          => __( 'Add New Challenge', 'gamerz-guild' ),
			'new_item'              => __( 'New Challenge', 'gamerz-guild' ),
			'edit_item'             => __( 'Edit Challenge', 'gamerz-guild' ),
			'view_item'             => __( 'View Challenge', 'gamerz-guild' ),
			'all_items'             => __( 'All Challenges', 'gamerz-guild' ),
			'search_items'          => __( 'Search Challenges', 'gamerz-guild' ),
			'parent_item_colon'     => __( 'Parent Challenges:', 'gamerz-guild' ),
			'not_found'             => __( 'No challenges found.', 'gamerz-guild' ),
			'not_found_in_trash'    => __( 'No challenges found in Trash.', 'gamerz-guild' ),
			'featured_image'        => _x( 'Challenge Thumbnail', 'Overrides the “Featured Image” phrase', 'gamerz-guild' ),
			'set_featured_image'    => _x( 'Set thumbnail image', 'Overrides the “Set featured image” phrase', 'gamerz-guild' ),
			'remove_featured_image' => _x( 'Remove thumbnail image', 'Overrides the “Remove featured image” phrase', 'gamerz-guild' ),
			'use_featured_image'    => _x( 'Use as thumbnail image', 'Overrides the “Use as featured image” phrase', 'gamerz-guild' ),
			'archives'              => _x( 'Challenge archives', 'The post type archive label used in nav menus', 'gamerz-guild' ),
			'insert_into_item'      => _x( 'Insert into challenge', 'Overrides the “Insert into post” phrase', 'gamerz-guild' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this challenge', 'Overrides the “Uploaded to this post” phrase', 'gamerz-guild' ),
			'filter_items_list'     => _x( 'Filter challenges list', 'Screen reader text for the filter links', 'gamerz-guild' ),
			'items_list_navigation' => _x( 'Challenges list navigation', 'Screen reader text for the pagination', 'gamerz-guild' ),
			'items_list'            => _x( 'Challenges list', 'Screen reader text for the items list', 'gamerz-guild' ),
		);

		$challenge_args = array(
			'labels'             => $challenge_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'challenge' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'custom-fields' ),
			'menu_icon'          => 'dashicons-awards',
			'show_in_rest'       => true,
		);

		register_post_type( 'gamerz_challenge', $challenge_args );
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			'Gamerz Guild Dashboard',
			'Gamerz Guild',
			'manage_options',
			'gamerz-guild-dashboard',
			[ $this, 'admin_dashboard_page' ],
			'dashicons-groups',
			30
		);

		add_submenu_page(
			'gamerz-guild-dashboard',
			'Guild Management',
			'Guilds',
			'manage_options',
			'edit.php?post_type=guild'
		);

		add_submenu_page(
			'gamerz-guild-dashboard',
			'Challenges',
			'Challenges',
			'manage_options',
			'edit.php?post_type=gamerz_challenge'
		);

		add_submenu_page(
			'gamerz-guild-dashboard',
			'Gamerz Guild Settings',
			'Settings',
			'manage_options',
			'gamerz-guild-settings',
			[ $this, 'admin_settings_page' ]
		);
	}

	/**
	 * Admin dashboard page
	 */
	public function admin_dashboard_page() {
		?>
		<div class="wrap">
			<h1>Gamerz Guild Dashboard</h1>
			
			<div class="gamerz-dashboard-grid">
				<div class="gamerz-dashboard-card">
					<h3><span class="dashicons dashicons-groups"></span> Guilds</h3>
					<?php $guild_counts = wp_count_posts( 'guild' ); ?>
					<p>Total Guilds: <?php echo isset( $guild_counts->publish ) ? $guild_counts->publish : 0; ?></p>
					<a href="<?php echo admin_url( 'edit.php?post_type=guild' ); ?>" class="button button-primary">Manage Guilds</a>
				</div>

				<div class="gamerz-dashboard-card">
					<h3><span class="dashicons dashicons-awards"></span> Challenges</h3>
					<?php $challenge_counts = wp_count_posts( 'gamerz_challenge' ); ?>
					<p>Active Challenges: <?php echo isset( $challenge_counts->publish ) ? $challenge_counts->publish : 0; ?></p>
					<a href="<?php echo admin_url( 'edit.php?post_type=gamerz_challenge' ); ?>" class="button button-primary">Manage Challenges</a>
				</div>
				
				<div class="gamerz-dashboard-card">
					<h3><span class="dashicons dashicons-star-filled"></span> Top Scrubs</h3>
					<?php
					if ( class_exists( 'myCRED' ) ) {
						$leaderboard = new Classes\Leaderboard();
						$top_scrubs = $leaderboard->get_global_leaderboard( 3 );
						if ( ! empty( $top_scrubs ) ) {
							echo '<ul>';
							foreach ( $top_scrubs as $scrub ) {
								echo '<li>' . esc_html( $scrub['display_name'] ) . ' - ' . $scrub['xp'] . ' XP (' . esc_html( $scrub['rank_name'] ) . ')</li>';
							}
							echo '</ul>';
						} else {
							echo '<p>No data yet</p>';
						}
					} else {
						echo '<p>Requires myCRED plugin</p>';
					}
					?>
				</div>
				
				<div class="gamerz-dashboard-card">
					<h3><span class="dashicons dashicons-chart-line"></span> Stats</h3>
					<?php
					$guild = new Classes\Guild();
					$all_users = get_users( [ 'fields' => 'ID' ] );
					$users_with_xp = 0;
					
					if ( class_exists( 'myCRED' ) ) {
						$xp_system = new Classes\XP_System();
						foreach ( $all_users as $user ) {
							if ( $xp_system->get_user_xp( $user->ID ) > 0 ) {
								$users_with_xp++;
							}
						}
					}
					
					echo '<p>Total Users: ' . count( $all_users ) . '</p>';
					echo '<p>Active Members: ' . $users_with_xp . '</p>';
					$stats_guild_counts = wp_count_posts( 'guild' );
					echo '<p>Active Guilds: ' . ( isset( $stats_guild_counts->publish ) ? $stats_guild_counts->publish : 0 ) . '</p>';
					?>
				</div>
			</div>
		</div>

		<style>
		.gamerz-dashboard-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
			gap: 20px;
			margin-top: 20px;
		}
		.gamerz-dashboard-card {
			background: #fff;
			border: 1px solid #ccd0d4;
			border-radius: 4px;
			padding: 20px;
			box-shadow: 0 1px 3px rgba(0,0,0,0.1);
		}
		.gamerz-dashboard-card h3 {
			margin-top: 0;
			display: flex;
			align-items: center;
			gap: 10px;
		}
		</style>
		<?php
	}

	/**
	 * Admin settings page
	 */
	public function admin_settings_page() {
		// Check if form was submitted
		if ( isset( $_POST['gamerz_settings_nonce'] ) && wp_verify_nonce( $_POST['gamerz_settings_nonce'], 'gamerz_save_settings' ) ) {
			// Save Discord settings if provided
			if ( isset( $_POST['gamerz_discord_webhook_url'] ) ) {
				update_option( 'gamerz_discord_webhook_url', sanitize_url( $_POST['gamerz_discord_webhook_url'] ) );
			}
			
			if ( isset( $_POST['gamerz_discord_bot_token'] ) ) {
				update_option( 'gamerz_discord_bot_token', sanitize_text_field( $_POST['gamerz_discord_bot_token'] ) );
			}
			
			if ( isset( $_POST['gamerz_discord_guild_id'] ) ) {
				update_option( 'gamerz_discord_guild_id', sanitize_text_field( $_POST['gamerz_discord_guild_id'] ) );
			}
			
			if ( isset( $_POST['gamerz_discord_role_mapping'] ) ) {
				$role_mapping = [];
				foreach ( $_POST['gamerz_discord_role_mapping'] as $rank_name => $role_data ) {
					$role_mapping[ sanitize_text_field( $rank_name ) ] = [
						'role_id' => sanitize_text_field( $role_data['role_id'] ),
						'role_name' => sanitize_text_field( $role_data['role_name'] ),
					];
				}
				update_option( 'gamerz_discord_role_mapping', $role_mapping );
			}
			
			echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully!</p></div>';
		}

		// Get current settings
		$discord_webhook = get_option( 'gamerz_discord_webhook_url', '' );
		$discord_bot_token = get_option( 'gamerz_discord_bot_token', '' );
		$discord_guild_id = get_option( 'gamerz_discord_guild_id', '' );
		$discord_role_mapping = get_option( 'gamerz_discord_role_mapping', [] );
		
		// Get rank system to show available ranks
		$rank_system = new Classes\Rank_System();
		$ranks = $rank_system->get_all_ranks();
		?>
		<div class="wrap">
			<h1>Gamerz Guild Settings</h1>
			
			<form method="post" action="">
				<?php wp_nonce_field( 'gamerz_save_settings', 'gamerz_settings_nonce' ); ?>
				
				<table class="form-table">
					<tr>
						<th scope="row">Discord Integration</th>
						<td>
							<h3>Discord Webhook</h3>
							<input type="url" name="gamerz_discord_webhook_url" value="<?php echo esc_attr( $discord_webhook ); ?>" class="regular-text" placeholder="https://discord.com/api/webhooks/..." />
							<p class="description">Enter your Discord webhook URL to send announcements to your server</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">Discord Bot Token</th>
						<td>
							<input type="password" name="gamerz_discord_bot_token" value="<?php echo esc_attr( $discord_bot_token ); ?>" class="regular-text" />
							<p class="description">Enter your Discord bot token to assign roles based on ranks (optional)</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">Discord Guild ID</th>
						<td>
							<input type="text" name="gamerz_discord_guild_id" value="<?php echo esc_attr( $discord_guild_id ); ?>" class="regular-text" />
							<p class="description">Enter your Discord server ID to manage roles</p>
						</td>
					</tr>
					
					<tr>
						<th scope="row">Discord Role Mapping</th>
						<td>
							<p>Map Gamerz Guild ranks to Discord roles:</p>
							<table class="widefat">
								<thead>
									<tr>
										<th>Rank</th>
										<th>Discord Role ID</th>
										<th>Role Name (for display)</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $ranks as $rank ) : ?>
										<?php
										$role_id = isset( $discord_role_mapping[ $rank['name'] ]['role_id'] ) ? $discord_role_mapping[ $rank['name'] ]['role_id'] : '';
										$role_name = isset( $discord_role_mapping[ $rank['name'] ]['role_name'] ) ? $discord_role_mapping[ $rank['name'] ]['role_name'] : '';
										?>
										<tr>
											<td><?php echo esc_html( $rank['name'] ); ?></td>
											<td><input type="text" name="gamerz_discord_role_mapping[<?php echo esc_attr( $rank['name'] ); ?>][role_id]" value="<?php echo esc_attr( $role_id ); ?>" placeholder="Role ID" /></td>
											<td><input type="text" name="gamerz_discord_role_mapping[<?php echo esc_attr( $rank['name'] ); ?>][role_name]" value="<?php echo esc_attr( $role_name ); ?>" placeholder="Role Display Name" /></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
				
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @access public
	 */
	public function __clone() { }

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @access public
	 */
	public function __wakeup() { }

	/**
	 * Instantiate the plugin
	 *
	 * @access public
	 *
	 * @return $_instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

// Initialize the main plugin
Main::instance();