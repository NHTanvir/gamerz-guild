<?php
/**
 * Guild class
 */
namespace Codexpert\Gamerz_Guild\Classes;

use WP_Post;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Guild
 * @author NH Tanvir <hi@tanvir.io>
 */
class Guild {

	public $post_type = 'guild';

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the guild system
	 */
	public function init() {
		// Hooks have been moved to app/Guild_Hooks.php
	}

	/**
	 * Add meta boxes for guild
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'guild_details',
			__( 'Guild Details', 'gamerz-guild' ),
			[ $this, 'render_meta_box' ],
			$this->post_type,
			'normal',
			'default'
		);
	}

	/**
	 * Enqueue Gutenberg editor assets
	 */
	public function enqueue_gutenberg_assets() {

		global $post;

		// Check if we're editing a guild post
		$is_guild_post_type = false;
		if ( $post && $post->post_type === $this->post_type ) {
			$is_guild_post_type = true;
		} else {
			// Check if post_type parameter in URL is 'guild'
			if ( isset( $_GET['post'] ) && get_post_type( intval( $_GET['post'] ) ) === $this->post_type ) {
				$is_guild_post_type = true;
			} elseif ( isset( $_GET['post_type'] ) && $_GET['post_type'] === $this->post_type ) {
				$is_guild_post_type = true;
			} elseif ( get_current_screen() && get_current_screen()->post_type === $this->post_type ) {
				$is_guild_post_type = true;
			}
		}

		if ( $is_guild_post_type ) {
			wp_enqueue_script(
				'guild-gutenberg-sidebar',
				plugin_dir_url( dirname( dirname( __DIR__ ) ) ) . 'assets/js/guild-gutenberg.js',
				array(
					'wp-plugins',
					'wp-edit-post',
					'wp-element',
					'wp-components',
					'wp-data',
					'wp-block-editor',
					'wp-compose',
					'wp-i18n'
				),
				filemtime( plugin_dir_path( dirname( dirname( __DIR__ ) ) ) . 'assets/js/guild-gutenberg.js' ),
				true
			);

			// Localize script to ensure i18n works properly
			wp_add_inline_script(
				'guild-gutenberg-sidebar',
				'wp.i18n.setLocaleData( { "gamerz-guild": { domain: "gamerz-guild" } }, "gamerz-guild" );',
				'after'
			);
		}
	}

	/**
	 * Render meta box
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'save_guild_details', 'guild_details_nonce' );

		$guild_tagline = get_post_meta( $post->ID, '_guild_tagline', true );
		$guild_description = get_post_meta( $post->ID, '_guild_description', true );
		$guild_max_members = get_post_meta( $post->ID, '_guild_max_members', true );
		$guild_creator_id = get_post_meta( $post->ID, '_guild_creator_id', true );
		$guild_status = get_post_meta( $post->ID, '_guild_status', true );

		?>
		<table class="form-table">
			<tr>
				<th><label for="guild_tagline"><?php _e( 'Guild Tagline', 'gamerz-guild' ); ?></label></th>
				<td><input type="text" id="guild_tagline" name="guild_tagline" value="<?php echo esc_attr( $guild_tagline ); ?>" class="regular-text" /></td>
			</tr>
			<tr>
				<th><label for="guild_description"><?php _e( 'Description', 'gamerz-guild' ); ?></label></th>
				<td><textarea id="guild_description" name="guild_description" class="large-text"><?php echo esc_textarea( $guild_description ); ?></textarea></td>
			</tr>
			<tr>
				<th><label for="guild_max_members"><?php _e( 'Max Members', 'gamerz-guild' ); ?></label></th>
				<td><input type="number" id="guild_max_members" name="guild_max_members" value="<?php echo esc_attr( $guild_max_members ? $guild_max_members : 20 ); ?>" min="1" max="100" /></td>
			</tr>
			<tr>
				<th><label for="guild_creator_id"><?php _e( 'Guild Creator ID', 'gamerz-guild' ); ?></label></th>
				<td><input type="number" id="guild_creator_id" name="guild_creator_id" value="<?php echo esc_attr( $guild_creator_id ); ?>" /></td>
			</tr>
			<tr>
				<th><label for="guild_status"><?php _e( 'Status', 'gamerz-guild' ); ?></label></th>
				<td>
					<select id="guild_status" name="guild_status">
						<option value="active" <?php selected( $guild_status, 'active' ); ?>><?php _e( 'Active', 'gamerz-guild' ); ?></option>
						<option value="inactive" <?php selected( $guild_status, 'inactive' ); ?>><?php _e( 'Inactive', 'gamerz-guild' ); ?></option>
						<option value="closed" <?php selected( $guild_status, 'closed' ); ?>><?php _e( 'Closed', 'gamerz-guild' ); ?></option>
					</select>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public function save_meta_box( $post_id ) {
		$is_classic_editor_save = isset( $_POST['guild_details_nonce'] );

		if ( $is_classic_editor_save ) {
			if ( ! isset( $_POST['guild_details_nonce'] ) || ! wp_verify_nonce( $_POST['guild_details_nonce'], 'save_guild_details' ) ) {
				return;
			}
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( get_post_type( $post_id ) !== $this->post_type ) {
			return;
		}

		if ( isset( $_POST['guild_tagline'] ) ) {
			update_post_meta( $post_id, '_guild_tagline', sanitize_text_field( $_POST['guild_tagline'] ) );
		}

		if ( isset( $_POST['guild_description'] ) ) {
			update_post_meta( $post_id, '_guild_description', sanitize_textarea_field( $_POST['guild_description'] ) );
		}

		if ( isset( $_POST['guild_max_members'] ) ) {
			update_post_meta( $post_id, '_guild_max_members', absint( $_POST['guild_max_members'] ) );
		}

		if ( isset( $_POST['guild_creator_id'] ) ) {
			update_post_meta( $post_id, '_guild_creator_id', absint( $_POST['guild_creator_id'] ) );
		}

		if ( isset( $_POST['guild_status'] ) ) {
			update_post_meta( $post_id, '_guild_status', sanitize_text_field( $_POST['guild_status'] ) );
		}
	}

	/**
	 * Create a new guild
	 */
	public function create_guild( $args = [] ) {
		$defaults = [
			'title'       => '',
			'description' => '',
			'tagline'     => '',
			'max_members' => 20,
			'creator_id'  => get_current_user_id(),
			'status'      => 'active',
			'meta'        => [],
		];

		$args = wp_parse_args( $args, $defaults );

		$guild_id = wp_insert_post( [
			'post_title'   => sanitize_text_field( $args['title'] ),
			'post_content' => wp_kses_post( $args['description'] ),
			'post_status'  => 'publish',
			'post_type'    => $this->post_type,
		] );

		if ( is_wp_error( $guild_id ) ) {
			return $guild_id;
		}

		// Add creator as guild leader
		$this->add_member( $guild_id, $args['creator_id'], 'leader' );

		// Add meta data
		update_post_meta( $guild_id, '_guild_tagline', sanitize_text_field( $args['tagline'] ) );
		update_post_meta( $guild_id, '_guild_description', wp_kses_post( $args['description'] ) );
		update_post_meta( $guild_id, '_guild_max_members', absint( $args['max_members'] ) );
		update_post_meta( $guild_id, '_guild_creator_id', absint( $args['creator_id'] ) );
		update_post_meta( $guild_id, '_guild_status', sanitize_text_field( $args['status'] ) );

		// Add any custom meta
		if ( ! empty( $args['meta'] ) ) {
			foreach ( $args['meta'] as $key => $value ) {
				update_post_meta( $guild_id, $key, $value );
			}
		}

		do_action( 'gamerz_guild_created', $guild_id, $args );

		return $guild_id;
	}

	/**
	 * Add a member to a guild
	 */
	public function add_member( $guild_id, $user_id, $role = 'member' ) {
		$members = $this->get_members( $guild_id );
		
		if ( in_array( $user_id, $members ) ) {
			return false; 
		}

		$max_members          = get_post_meta( $guild_id, '_guild_max_members', true );
		$current_member_count = count( $members );

		if ( $max_members && $current_member_count >= $max_members ) {
			return new \WP_Error( 'guild_full', __( 'Guild is at maximum capacity', 'gamerz-guild' ) );
		}

		$members[] = $user_id;
		update_post_meta( $guild_id, '_guild_members', $members );

		update_user_meta( $user_id, "_guild_role_{$guild_id}", $role );

		do_action( 'gamerz_guild_member_added', $guild_id, $user_id, $role );

		return true;
	}

	/**
	 * Remove a member from a guild
	 */
	public function remove_member( $guild_id, $user_id ) {
		$members = $this->get_members( $guild_id );
		
		if ( ! in_array( $user_id, $members ) ) {
			return false; 
		}

		$members = array_diff( $members, [ $user_id ] );
		update_post_meta( $guild_id, '_guild_members', $members );

		delete_user_meta( $user_id, "_guild_role_{$guild_id}" );

		do_action( 'gamerz_guild_member_removed', $guild_id, $user_id );

		return true;
	}

	/**
	 * Get all members of a guild
	 */
	public function get_members( $guild_id ) {
		$members = get_post_meta( $guild_id, '_guild_members', true );
		return ! empty( $members ) ? $members : [];
	}

	/**
	 * Get guild by ID
	 */
	public function get_guild( $guild_id ) {
		return get_post( $guild_id );
	}

	/**
	 * Get guild meta data
	 */
	public function get_guild_meta( $guild_id, $key = '', $single = true ) {
		return get_post_meta( $guild_id, $key, $single );
	}

	/**
	 * Check if user is a member of a guild
	 */
	public function is_member( $guild_id, $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$members = $this->get_members( $guild_id );
		return in_array( $user_id, $members );
	}

	/**
	 * Get user's role in a guild
	 */
	public function get_user_role( $guild_id, $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$role = get_user_meta( $user_id, "_guild_role_{$guild_id}", true );
		return $role ? $role : 'member';
	}

	/**
	 * Get guilds for a user
	 */
	public function get_user_guilds( $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$all_guilds = get_posts( [
			'post_type' => $this->post_type,
			'posts_per_page' => -1,
			'post_status' => 'publish',
		] );

		$user_guilds = [];

		foreach ( $all_guilds as $guild ) {
			if ( $this->is_member( $guild->ID, $user_id ) ) {
				$user_guilds[] = $guild;
			}
		}

		return $user_guilds;
	}
}