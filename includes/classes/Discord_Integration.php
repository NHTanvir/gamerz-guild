<?php
/**
 * Discord_Integration class
 */
namespace Codexpert\Gamerz_Guild\Classes;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Gamerz_Guild
 * @subpackage Discord_Integration
 * @author NH Tanvir <hi@tanvir.io>
 */
class Discord_Integration {

	/**
	 * Discord webhook URL
	 */
	public $webhook_url;

	/**
	 * Discord bot token (if using bot API)
	 */
	public $bot_token;

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the Discord integration
	 */
	public function init() {

		$this->webhook_url = get_option( 'gamerz_discord_webhook_url', '' );
		$this->bot_token   = get_option( 'gamerz_discord_bot_token', '' );


		if ( empty( $this->webhook_url ) || empty( $this->bot_token ) ) {
			$this->setup_default_discord_config();
			$this->webhook_url = get_option( 'gamerz_discord_webhook_url', '' );
			$this->bot_token   = get_option( 'gamerz_discord_bot_token', '' );
		}

		if ( ! $this->webhook_url && ! $this->bot_token ) {
			return;
		}
	}

	/**
	 * Add Discord field to user profile
	 */
	public function add_discord_field( $user ) {
		$discord_id       = get_user_meta( $user->ID, '_gamerz_discord_id', true );
		$discord_username = get_user_meta( $user->ID, '_gamerz_discord_username', true );
		?>
		<h3><?php _e( 'Discord Integration', 'gamerz-guild' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="gamerz_discord_id"><?php _e( 'Discord ID', 'gamerz-guild' ); ?></label></th>
				<td>
					<input type="text" name="gamerz_discord_id" id="gamerz_discord_id" value="<?php echo esc_attr( $discord_id ); ?>" class="regular-text" />
					<br />
					<span class="description"><?php _e( 'Your Discord user ID for integration with the Gamification system.', 'gamerz-guild' ); ?></span>
				</td>
			</tr>
			<tr>
				<th><label for="gamerz_discord_username"><?php _e( 'Discord Username', 'gamerz-guild' ); ?></label></th>
				<td>
					<input type="text" name="gamerz_discord_username" id="gamerz_discord_username" value="<?php echo esc_attr( $discord_username ); ?>" class="regular-text" />
					<br />
					<span class="description"><?php _e( 'Your Discord username#tag (e.g., user#1234).', 'gamerz-guild' ); ?></span>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Save Discord field
	 */
	public function save_discord_field( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		if ( isset( $_POST['gamerz_discord_id'] ) ) {
			update_user_meta( $user_id, '_gamerz_discord_id', sanitize_text_field( $_POST['gamerz_discord_id'] ) );
		}

		if ( isset( $_POST['gamerz_discord_username'] ) ) {
			update_user_meta( $user_id, '_gamerz_discord_username', sanitize_text_field( $_POST['gamerz_discord_username'] ) );
		}
	}

	/**
	 * Announce rank up to Discord
	 */
	public function announce_rank_up( $user_id, $old_rank, $new_rank ) {
		$user = get_user_by( 'ID', $user_id );
		if ( ! $user ) {
			return;
		}

		$discord_username = get_user_meta( $user_id, '_gamerz_discord_username', true );
		$discord_id       = get_user_meta( $user_id, '_gamerz_discord_id', true );

		$message = [
			'embeds' => [
				[
					'title'       => ':tada: Rank Up Achievement!',
					'description' => "<@" . ($discord_id ?: $user->user_login) . "> has ascended to **{$new_rank['name']}**! :medal:",
					'color'       => $this->get_rank_color( $new_rank['id'] ),
					'timestamp'   => date( 'c' ),
					'footer'     => [
						'text' => 'Scrub Gamerz - Level Up!'
					]
				]
			]
		];

		$this->send_discord_message( $message );
	}

	/**
	 * Announce badge award to Discord
	 */
	public function announce_badge_award( $user_id, $badge_id ) {
		$user = get_user_by( 'ID', $user_id );
		if ( ! $user ) {
			return;
		}

		$badge_system = new Badge_System();
		$badge = $badge_system->get_badge( $badge_id );
		if ( ! $badge ) {
			return;
		}

		$discord_username = get_user_meta( $user_id, '_gamerz_discord_username', true );
		$discord_id       = get_user_meta( $user_id, '_gamerz_discord_id', true );

		$message = [
			'embeds' => [
				[
					'title'       => ':medal: New Badge Earned!',
					'description' => "<@" . ($discord_id ?: $user->user_login) . "> earned the **{$badge['name']}** badge! :trophy:",
					'color'       => 15844367, // Golden color
					'timestamp'   => date( 'c' ),
					'footer'      => [
						'text' => $badge['description']
					]
				]
			]
		];

		$this->send_discord_message( $message );
	}

	/**
	 * Announce guild creation to Discord
	 */
	public function announce_guild_creation( $guild_id, $args ) {
		$guild = get_post( $guild_id );
		if ( ! $guild ) {
			return;
		}

		$user = get_user_by( 'ID', $args['creator_id'] );
		if ( ! $user ) {
			return;
		}

		$discord_username 	= get_user_meta( $args['creator_id'], '_gamerz_discord_username', true );
		$discord_id 		= get_user_meta( $args['creator_id'], '_gamerz_discord_id', true );

		$message = [
			'embeds' => [
				[
					'title'			 => ':crossed_swords: New Guild Formed!',
					'description' 	 => "<@" . ($discord_id ?: $user->user_login) . "> has created a new guild: **{$guild->post_title}**!",
					'color'          => 8454143, // Purple color
					'timestamp'      => date( 'c' ),
					'footer'         => [
						'text'       => 'Assemble your team!'
					]
				]
			]
		];

		$this->send_discord_message( $message );
	}

	/***
	 * Announce guild join to Discord
	 */
	public function announce_guild_join( $guild_id, $user_id, $role ) {
		$guild = get_post( $guild_id );
		$user  = get_user_by( 'ID', $user_id );
		
		if ( ! $guild || ! $user ) {
			return;
		}

		$discord_username = get_user_meta( $user_id, '_gamerz_discord_username', true );
		$discord_id       = get_user_meta( $user_id, '_gamerz_discord_id', true );

		$message = [
			'embeds' => [
				[
					'title' => ':busts_in_silhouette: New Guild Member!',
					'description' => "<@" . ($discord_id ?: $user->user_login) . "> has joined the guild **{$guild->post_title}**!",
					'color' => 8388863, // Blue color
					'timestamp' => date( 'c' ),
					'footer' => [
						'text' => 'Welcome to the team!'
					]
				]
			]
		];

		$this->send_discord_message( $message );
	}

	/***
	 * Announce guild member promotion to Discord
	 */
	public function announce_guild_member_promoted( $guild_id, $user_id, $new_role ) {
		$guild = get_post( $guild_id );
		$user = get_user_by( 'ID', $user_id );
		
		if ( ! $guild || ! $user ) {
			return;
		}

		$discord_username = get_user_meta( $user_id, '_gamerz_discord_username', true );
		$discord_id = get_user_meta( $user_id, '_gamerz_discord_id', true );

		$role_display_name = $this->get_guild_role_display_name( $new_role );

		$message = [
			'embeds' => [
				[
					'title' => ':arrow_up: Guild Promotion!',
					'description' => "<@" . ($discord_id ?: $user->user_login) . "> has been promoted to **{$role_display_name}** in **{$guild->post_title}**!",
					'color' => 16777045, // Gold color
					'timestamp' => date( 'c' ),
					'footer' => [
						'text' => 'Congratulations on your promotion!'
					]
				]
			]
		];

		$this->send_discord_message( $message );
	}

	/***
	 * Announce guild member demotion to Discord
	 */
	public function announce_guild_member_demoted( $guild_id, $user_id, $new_role ) {
		$guild = get_post( $guild_id );
		$user = get_user_by( 'ID', $user_id );
		
		if ( ! $guild || ! $user ) {
			return;
		}

		$discord_username = get_user_meta( $user_id, '_gamerz_discord_username', true );
		$discord_id = get_user_meta( $user_id, '_gamerz_discord_id', true );

		$role_display_name = $this->get_guild_role_display_name( $new_role );

		$message = [
			'embeds' => [
				[
					'title' => ':arrow_down: Guild Role Change',
					'description' => "<@" . ($discord_id ?: $user->user_login) . "> has been changed to **{$role_display_name}** in **{$guild->post_title}**.",
					'color' => 16711680, // Red color
					'timestamp' => date( 'c' ),
					'footer' => [
						'text' => 'Every role serves the guild!'
					]
				]
			]
		];

		$this->send_discord_message( $message );
	}

	/***
	 * Get guild role display name
	 */
	private function get_guild_role_display_name( $role ) {
		$roles = [
			'leader' => 'Guild Leader',
			'officer' => 'Guild Officer',
			'member' => 'Guild Member',
		];

		return isset( $roles[ $role ] ) ? $roles[ $role ] : $role;
	}

	/**
	 * Announce challenge completion to Discord
	 */
	public function announce_challenge_completion( $user_id, $badge_id, $awarded_by ) {
		$user = get_user_by( 'ID', $user_id );
		$awarded_by_user = get_user_by( 'ID', $awarded_by );
		
		if ( ! $user || ! $awarded_by_user ) {
			return;
		}

		$badge_system = new Badge_System();
		$badge = $badge_system->get_badge( $badge_id );
		if ( ! $badge ) {
			return;
		}

		$discord_username = get_user_meta( $user_id, '_gamerz_discord_username', true );
		$discord_id = get_user_meta( $user_id, '_gamerz_discord_id', true );

		$message = [
			'embeds' => [
				[
					'title' => ':checkered_flag: Challenge Completed!',
					'description' => "<@" . ($discord_id ?: $user->user_login) . "> completed a special challenge and earned the **{$badge['name']}** badge!",
					'color' => 16753920, // Orange color
					'timestamp' => date( 'c' ),
					'footer' => [
						'text' => 'Awarded by: ' . $awarded_by_user->display_name
					]
				]
			]
		];

		$this->send_discord_message( $message );
	}

	/**
	 * Send message to Discord via webhook
	 */
	public function send_discord_message( $message ) {
		if ( ! $this->webhook_url ) {
			return false;
		}

		$message['username'] = 'Scrub Gamerz Bot';
		$message['avatar_url'] = get_site_icon_url() ?: get_template_directory_uri() . '/img/discord-avatar.png';

		$args = [
			'method' => 'POST',
			'headers' => [
				'Content-Type' => 'application/json',
			],
			'body' => json_encode( $message ),
		];

		$response = wp_remote_post( $this->webhook_url, $args );

		if ( is_wp_error( $response ) ) {
			error_log( 'Discord Integration Error: ' . $response->get_error_message() );
			return false;
		}

		return true;
	}

	/**
	 * Update Discord role for user (requires Uncanny Automator or custom API)
	 */
	public function update_discord_role( $user_id, $rank_name ) {
		// This would typically be handled by Uncanny Automator
		// For a standalone solution, we'd need to have Discord bot permissions
		
		$discord_id = get_user_meta( $user_id, '_gamerz_discord_id', true );
		if ( ! $discord_id || ! $this->bot_token ) {
			return false;
		}

		// Map rank names to Discord role IDs (these would need to be configured in settings)
		$role_mapping = get_option( 'gamerz_discord_role_mapping', [] );
		if ( empty( $role_mapping ) ) {
			return false; // Roles not mapped yet
		}

		// Find the role ID for this rank
		$role_id = null;
		foreach ( $role_mapping as $rank => $role ) {
			if ( $rank === $rank_name ) {
				$role_id = $role['role_id'];
				break;
			}
		}

		if ( ! $role_id ) {
			return false;
		}

		// Get guild ID
		$guild_id = get_option( 'gamerz_discord_guild_id', '' );
		if ( ! $guild_id ) {
			return false;
		}

		// First, remove all rank roles from the user
		$this->remove_rank_roles_from_user( $discord_id, $guild_id );

		// Then add the new role
		$args = [
			'method' => 'PUT',
			'headers' => [
				'Authorization' => 'Bot ' . $this->bot_token,
				'Content-Type' => 'application/json',
			],
		];

		$url = "https://discord.com/api/v9/guilds/{$guild_id}/members/{$discord_id}/roles/{$role_id}";
		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			error_log( 'Discord Role Update Error: ' . $response->get_error_message() );
			return false;
		}

		return true;
	}

	/**
	 * Remove all rank roles from a user
	 */
	public function remove_rank_roles_from_user( $discord_id, $guild_id ) {
		$role_mapping = get_option( 'gamerz_discord_role_mapping', [] );
		if ( empty( $role_mapping ) ) {
			return false;
		}

		$args = [
			'method' => 'GET',
			'headers' => [
				'Authorization' => 'Bot ' . $this->bot_token,
			],
		];

		// Get current member roles
		$member_url = "https://discord.com/api/v9/guilds/{$guild_id}/members/{$discord_id}";
		$member_response = wp_remote_get( $member_url, $args );

		if ( is_wp_error( $member_response ) ) {
			return false;
		}

		$member_data = json_decode( wp_remote_retrieve_body( $member_response ), true );
		if ( ! isset( $member_data['roles'] ) ) {
			return false;
		}

		// Get rank role IDs to remove
		$rank_role_ids = [];
		foreach ( $role_mapping as $rank => $role_info ) {
			if ( in_array( $role_info['role_id'], $member_data['roles'] ) ) {
				$rank_role_ids[] = $role_info['role_id'];
			}
		}

		// Remove each rank role
		$remove_args = [
			'method' => 'DELETE',
			'headers' => [
				'Authorization' => 'Bot ' . $this->bot_token,
			],
		];

		foreach ( $rank_role_ids as $role_id ) {
			$remove_url = "https://discord.com/api/v9/guilds/{$guild_id}/members/{$discord_id}/roles/{$role_id}";
			wp_remote_request( $remove_url, $remove_args );
		}
	}

	/**
	 * Get Discord role from rank level
	 */
	public function get_discord_role_by_rank( $rank_level ) {
		$rank_system = new Rank_System();
		$rank = $rank_system->get_rank_by_level( $rank_level );
		if ( ! $rank ) {
			return null;
		}

		return $this->get_discord_role_by_rank_name( $rank['name'] );
	}

	/**
	 * Get Discord role from rank name
	 */
	public function get_discord_role_by_rank_name( $rank_name ) {
		$role_mapping = get_option( 'gamerz_discord_role_mapping', [] );
		
		foreach ( $role_mapping as $rank => $role ) {
			if ( $rank === $rank_name ) {
				return $role['role_id'];
			}
		}

		return null;
	}

	/**
	 * Get color based on rank level for Discord embeds
	 */
	private function get_rank_color( $rank_level ) {
		// Assign different colors based on rank level
		$colors = [
			1 => 12552212,  // Gray for Scrubling
			2 => 12552212,  // Gray for Scrub Recruit
			3 => 16776960,  // Yellow for Scrub Scout
			4 => 16776960,  // Yellow for Scrub Soldier
			5 => 8454143,   // Purple for Scrub Strategist
			6 => 8454143,   // Purple for Scrub Captain
			7 => 16753920,  // Orange for Scrub Champion
			8 => 16753920,  // Orange for Guild Officer
			9 => 16777215,  // White for Scrub Sage
			10 => 16777215, // White for Scrub Warlord
			11 => 16711680, // Red for Meme Master
			12 => 16711680, // Red for Scrub Overlord
			13 => 16711935, // Magenta for Nova Scrub
			14 => 8421504,  // Silver for Scrub Prime
			15 => 16777045, // Gold for Legendary Scrub
		];

		return isset( $colors[ $rank_level ] ) ? $colors[ $rank_level ] : 0xFFFFFF; // White default
	}

	/**
	 * Send leaderboard to Discord
	 */
	public function send_leaderboard_to_discord() {
		$leaderboard = ( new Leaderboard() )->get_global_leaderboard( 5 );

		if ( empty( $leaderboard ) ) {
			return false;
		}

		$leaderboard_text = "🏆 **Top 5 Scrubs This Week** 🏆\n\n";
		foreach ( $leaderboard as $i => $entry ) {
			$position = $i + 1;
			$emoji = $this->get_position_emoji( $position );
			$leaderboard_text .= "{$emoji} **{$position}.** {$entry['display_name']} - {$entry['xp']} XP ({$entry['rank_name']})\n";
		}

		$message = [
			'embeds' => [
				[
					'title' => 'Weekly Leaderboard',
					'description' => $leaderboard_text,
					'color' => 16753920, // Orange
					'timestamp' => date( 'c' ),
					'footer' => [
						'text' => 'Keep grinding to reach the top!'
					]
				]
			]
		];

		return $this->send_discord_message( $message );
	}

	/**
	 * Get emoji for position
	 */
	private function get_position_emoji( $position ) {
		$emojis = [
			1 => '🥇',
			2 => '🥈',
			3 => '🥉',
			4 => '4️⃣',
			5 => '5️⃣'
		];

		return isset( $emojis[ $position ] ) ? $emojis[ $position ] : "#{$position}";
	}

	/**
	 * Setup Discord integration settings
	 */
	public function setup_discord_settings() {
		// This would normally be added to a settings page
		// For now, we're just providing the methods to handle it
	}

	/**
	 * Set up default Discord configuration
	 */
	public function setup_default_discord_config() {
		// Only set defaults if options don't already exist
		if ( empty( get_option( 'gamerz_discord_webhook_url', '' ) ) ) {
			update_option( 'gamerz_discord_webhook_url', 'https://discord.com/api/webhooks/1443615355024179221/u5J7zMlnZL8HuK5Ux57_bz9GkjWuTWJ5oso3wuzRdPZlEJL3lktLw3rM4y_SdGfiw_3Z' );
		}
		
		if ( empty( get_option( 'gamerz_discord_bot_token', '' ) ) ) {
			update_option( 'gamerz_discord_bot_token', 'MTQ0MzYxNzE5MTQyMDIzNTk3MA.Gak63V.mOqcuCIxwl1Z2u0-u1cZLmF3qthcZJkqzXKj5k' );
		}
		
		if ( empty( get_option( 'gamerz_discord_guild_id', '' ) ) ) {
			update_option( 'gamerz_discord_guild_id', '1045083738754789386' );
		}
		
		// Only set role mappings if they don't already exist
		if ( false === get_option( 'gamerz_discord_role_mapping', false ) ) {
			$role_mappings = [
				'Scrubling' => [
					'role_id' => '',
					'role_name' => 'Scrubling'
				],
				'Scrub Recruit' => [
					'role_id' => '1443626808929943595',  // Rookie Scrub
					'role_name' => 'Rookie Scrub'
				],
				'Scrub Scout' => [
					'role_id' => '',
					'role_name' => 'Scrub Scout'
				],
				'Scrub Soldier' => [
					'role_id' => '1443626957228216340',  // Casual Scrub
					'role_name' => 'Casual Scrub'
				],
				'Scrub Strategist' => [
					'role_id' => '',
					'role_name' => 'Scrub Strategist'
				],
				'Scrub Captain' => [
					'role_id' => '',
					'role_name' => 'Scrub Captain'
				],
				'Scrub Champion' => [
					'role_id' => '1443627075520167957',  // Elite Scrub
					'role_name' => 'Elite Scrub'
				],
				'Guild Officer' => [
					'role_id' => '',
					'role_name' => 'Guild Officer'
				],
				'Scrub Sage' => [
					'role_id' => '',
					'role_name' => 'Scrub Sage'
				],
				'Scrub Warlord' => [
					'role_id' => '',
					'role_name' => 'Scrub Warlord'
				],
				'Meme Master' => [
					'role_id' => '',
					'role_name' => 'Meme Master'
				],
				'Scrub Overlord' => [
					'role_id' => '1443627282588762275',  // Mythic Scrub
					'role_name' => 'Mythic Scrub'
				],
				'Nova Scrub' => [
					'role_id' => '',
					'role_name' => 'Nova Scrub'
				],
				'Scrub Prime' => [
					'role_id' => '',
					'role_name' => 'Scrub Prime'
				],
				'Legendary Scrub' => [
					'role_id' => '1443627161578766461',  // Legendary Scrub
					'role_name' => 'Legendary Scrub'
				],
			];
			
			update_option( 'gamerz_discord_role_mapping', $role_mappings );
		}
	}
}