<?php
/**
 * Leaderboard class
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
 * @subpackage Leaderboard
 * @author NH Tanvir <hi@tanvir.io>
 */
class Leaderboard {

	/**
	 * Constructor function
	 */
	public function __construct() {}

	/**
	 * Get global XP leaderboard
	 */
	public function get_global_leaderboard( $limit = 10, $offset = 0 ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return [];
		}

		$users = get_users( [
			'number'  => $limit,
			'offset'  => $offset,
			'orderby' => 'meta_value_num',
			'order'   => 'DESC',
			'meta_query' => [
				[
					'key'     => $mycred->get_point_type_key(),
					'compare' => 'EXISTS'
				]
			],
			'meta_key' => $mycred->get_point_type_key(),
		] );

		$leaderboard = [];
		$rank = $offset + 1;

		foreach ( $users as $user ) {
			$user_xp = $mycred->get_users_cred( $user->ID );

			if ( $user_xp > 0 ) {
				$rank_system = new Rank_System();
				$rank_info   = $rank_system->get_user_rank( $user->ID );

				$leaderboard[] = [
					'rank' => $rank,
					'user_id' => $user->ID,
					'display_name' => $user->display_name,
					'user_login' => $user->user_login,
					'xp' => $user_xp,
					'rank_name' => $rank_info['name'],
					'avatar' => get_avatar_url( $user->ID, [ 'size' => 40 ] ),
				];
				$rank++;
			}
		}

		return $leaderboard;
	}

	/**
	 * Get guild-specific leaderboard
	 */
	public function get_guild_leaderboard( $guild_id, $limit = 10, $offset = 0 ) {
		$guild = new Guild();
		$member_ids = $guild->get_members( $guild_id );
		
		if ( empty( $member_ids ) ) {
			return [];
		}

		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return [];
		}

		// Get all members with their XP
		$members_with_xp = [];
		foreach ( $member_ids as $user_id ) {
			$user_xp = $mycred->get_users_cred( $user_id );
			$members_with_xp[] = [
				'user_id' => $user_id,
				'xp' => $user_xp,
			];
		}

		// Sort by XP descending
		usort( $members_with_xp, function( $a, $b ) {
			return $b['xp'] - $a['xp'];
		} );

		// Apply limit and offset
		$members_with_xp = array_slice( $members_with_xp, $offset, $limit );

		$leaderboard = [];
		$rank = $offset + 1;

		foreach ( $members_with_xp as $member_data ) {
			$user = get_user_by( 'ID', $member_data['user_id'] );
			if ( $user ) {
				$rank_system = new Rank_System();
				$rank_info = $rank_system->get_user_rank( $member_data['user_id'] );

				$leaderboard[] = [
					'rank' => $rank,
					'user_id' => $user->ID,
					'display_name' => $user->display_name,
					'user_login' => $user->user_login,
					'xp' => $member_data['xp'],
					'rank_name' => $rank_info['name'],
					'avatar' => get_avatar_url( $user->ID, [ 'size' => 40 ] ),
				];
				$rank++;
			}
		}

		return $leaderboard;
	}

	/**
	 * Get leaderboard for a specific time period (seasonal/monthly)
	 */
	public function get_time_based_leaderboard( $start_date, $end_date, $limit = 10, $offset = 0 ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return [];
		}

		// Get all users
		$all_users = get_users( [
			'fields' => [ 'ID', 'display_name', 'user_login' ]
		] );

		$time_based_xp = [];
		$rank = $offset + 1;

		foreach ( $all_users as $user ) {
			// Get user's XP earned in the specified time period
			// This is more complex and requires checking myCRED logs
			$logs = $mycred->get_log( [
				'user_id' => $user->ID,
				'ctype' => 'gamerz_xp',
				'date' => [
					'date' => $start_date,
					'compare' => '>='
				],
				'date_end' => [
					'date' => $end_date,
					'compare' => '<='
				]
			] );

			$total_xp = 0;
			foreach ( $logs as $log ) {
				$total_xp += $log['creds'];
			}

			if ( $total_xp > 0 ) {
				$time_based_xp[] = [
					'user_id' => $user->ID,
					'display_name' => $user->display_name,
					'user_login' => $user->user_login,
					'xp' => $total_xp,
					'avatar' => get_avatar_url( $user->ID, [ 'size' => 40 ] ),
				];
			}
		}

		// Sort by XP descending
		usort( $time_based_xp, function( $a, $b ) {
			return $b['xp'] - $a['xp'];
		} );

		// Apply limit and offset
		$time_based_xp = array_slice( $time_based_xp, $offset, $limit );

		$leaderboard = [];
		$rank = $offset + 1;

		foreach ( $time_based_xp as $user_data ) {
			$rank_system = new Rank_System();
			$rank_info = $rank_system->get_user_rank( $user_data['user_id'] );

			$leaderboard[] = [
				'rank' => $rank,
				'user_id' => $user_data['user_id'],
				'display_name' => $user_data['display_name'],
				'user_login' => $user_data['user_login'],
				'xp' => $user_data['xp'],
				'rank_name' => $rank_info['name'],
				'avatar' => $user_data['avatar'],
			];
			$rank++;
		}

		return $leaderboard;
	}

	/**
	 * Get user's position in global leaderboard
	 */
	public function get_user_global_position( $user_id ) {
		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return -1;
		}

		$user_xp = $mycred->get_users_cred( $user_id );
		if ( $user_xp <= 0 ) {
			return -1;
		}

		// Get all users with more XP than current user
		$users = get_users( [
			'fields' => [ 'ID' ],
			'meta_query' => [
				[
					'key' => $mycred->get_point_type_key(),
					'value' => $user_xp,
					'type' => 'NUMERIC',
					'compare' => '>'
				]
			]
		] );

		return count( $users ) + 1;
	}

	/**
	 * Get user's position in guild leaderboard
	 */
	public function get_user_guild_position( $user_id, $guild_id ) {
		$guild = new Guild();
		$member_ids = $guild->get_members( $guild_id );
		
		if ( ! in_array( $user_id, $member_ids ) ) {
			return -1;
		}

		$mycred = $this->get_mycred();
		if ( ! $mycred ) {
			return -1;
		}

		$user_xp = $mycred->get_users_cred( $user_id );

		// Get XP for all guild members
		$member_xp = [];
		foreach ( $member_ids as $member_id ) {
			$member_xp[ $member_id ] = $mycred->get_users_cred( $member_id );
		}

		// Count members with more XP than current user
		$higher_rank_count = 0;
		foreach ( $member_xp as $xp ) {
			if ( $xp > $user_xp ) {
				$higher_rank_count++;
			}
		}

		return $higher_rank_count + 1;
	}

	/**
	 * Get the myCred reference
	 */
	private function get_mycred() {
		if ( function_exists( 'mycred' ) ) {
			return mycred();
		}
		return false;
	}

	/**
	 * Render global leaderboard shortcode
	 */
	public function render_leaderboard_shortcode( $atts ) {
		$atts = shortcode_atts( [
			'type' => 'global',
			'limit' => 10,
			'guild_id' => 0,
			'title' => 'Top Scrubs',
		], $atts );


		ob_start();

		$leaderboard = [];
		$title = $atts['title'];

		switch ( $atts['type'] ) {
			case 'guild':
				if ( ! $atts['guild_id'] ) {
					// If no guild ID is provided, try to get from context
					$guild_id = get_post_meta( get_the_ID(), '_guild_id', true );
					if ( ! $guild_id ) {
						// Look for current user's guild
						$user_guilds = ( new Guild() )->get_user_guilds();
						if ( ! empty( $user_guilds ) ) {
							$guild_id = $user_guilds[0]->ID;
						}
					}
					$atts['guild_id'] = $guild_id;
				}

				if ( $atts['guild_id'] ) {
					$leaderboard = $this->get_guild_leaderboard( $atts['guild_id'], $atts['limit'] );
					$guild = get_post( $atts['guild_id'] );
					if ( $guild ) {
						$title = sprintf( __( '%s Leaderboard', 'gamerz-guild' ), $guild->post_title );
					}
				}
				break;

			default:
				$leaderboard = $this->get_global_leaderboard( $atts['limit'] );
		}

		?>
		<div class="gamerz-leaderboard-container">
			<h3 class="gamerz-leaderboard-title"><?php echo esc_html( $title ); ?></h3>
			<div class="gamerz-leaderboard">
				<div class="gamerz-leaderboard-header">
					<span class="gamerz-leaderboard-rank">#</span>
					<span class="gamerz-leaderboard-player">Player</span>
					<span class="gamerz-leaderboard-xp">XP</span>
					<span class="gamerz-leaderboard-rank-name">Rank</span>
				</div>
				<?php if ( ! empty( $leaderboard ) ) : ?>
					<?php foreach ( $leaderboard as $entry ) : ?>
						<div class="gamerz-leaderboard-item">
							<span class="gamerz-leaderboard-rank">
								<?php echo esc_html( $entry['rank'] ); ?>
							</span>
							<span class="gamerz-leaderboard-player">
								<img src="<?php echo esc_url( $entry['avatar'] ); ?>" alt="<?php echo esc_attr( $entry['display_name'] ); ?>" class="gamerz-player-avatar" width="30" height="30">
								<?php echo esc_html( $entry['display_name'] ); ?>
							</span>
							<span class="gamerz-leaderboard-xp">
								<?php echo number_format( $entry['xp'] ); ?>
							</span>
							<span class="gamerz-leaderboard-rank-name">
								<?php echo esc_html( $entry['rank_name'] ); ?>
							</span>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="gamerz-leaderboard-empty">
						<?php _e( 'No data available for this leaderboard.', 'gamerz-guild' ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<style>
		.gamerz-leaderboard-container {
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			padding: 20px;
			margin: 20px 0;
		}
		.gamerz-leaderboard-title {
			text-align: center;
			color: #333;
			margin-top: 0;
			margin-bottom: 15px;
		}
		.gamerz-leaderboard {
			border: 1px solid #eee;
			border-radius: 5px;
			overflow: hidden;
		}
		.gamerz-leaderboard-header {
			display: flex;
			background: #f5f5f5;
			padding: 10px;
			font-weight: bold;
			border-bottom: 1px solid #eee;
		}
		.gamerz-leaderboard-item {
			display: flex;
			padding: 10px;
			border-bottom: 1px solid #eee;
			align-items: center;
		}
		.gamerz-leaderboard-item:last-child {
			border-bottom: none;
		}
		.gamerz-leaderboard-rank {
			width: 15%;
			font-weight: bold;
		}
		.gamerz-leaderboard-player {
			width: 45%;
			display: flex;
			align-items: center;
			gap: 10px;
		}
		.gamerz-player-avatar {
			border-radius: 50%;
			vertical-align: middle;
		}
		.gamerz-leaderboard-xp {
			width: 20%;
			color: #007cba;
			font-weight: bold;
		}
		.gamerz-leaderboard-rank-name {
			width: 20%;
			color: #666;
			font-size: 0.9em;
		}
		.gamerz-leaderboard-empty {
			text-align: center;
			padding: 30px;
			color: #888;
		}
		</style>
		<?php

		return ob_get_clean();
	}

	/**
	 * Render user's XP progress shortcode
	 */
	public function render_xp_progress_shortcode( $atts ) {
		$atts = shortcode_atts( [
			'user_id' => get_current_user_id(),
			'show_rank' => 'true',
			'show_next_rank' => 'true',
			'title' => 'Your Progress',
		], $atts );


		$user_id = $atts['user_id'];
		if ( ! $user_id ) {
			return __( 'Please log in to view your progress.', 'gamerz-guild' );
		}

		$rank_system = new Rank_System();
		$progress_info = $rank_system->get_rank_progress( $user_id );

		ob_start();
		?>
		<div class="gamerz-xp-progress-container">
			<h3 class="gamerz-xp-progress-title"><?php echo esc_html( $atts['title'] ); ?></h3>
			<div class="gamerz-xp-progress-box">
				<?php if ( $atts['show_rank'] == 'true' ) : ?>
					<div class="gamerz-current-rank-info">
						<div class="gamerz-rank-display">
							<span class="gamerz-rank-name"><?php echo esc_html( $progress_info['current_rank']['name'] ); ?></span>
							<span class="gamerz-rank-level">Level <?php echo esc_html( $progress_info['current_rank']['id'] ); ?></span>
						</div>
						<div class="gamerz-current-xp">
							<span class="gamerz-xp-value"><?php echo number_format( $progress_info['current_xp'] ); ?> XP</span>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $atts['show_next_rank'] == 'true' && $progress_info['next_rank'] ) : ?>
					<div class="gamerz-next-rank-info">
						<div class="gamerz-next-rank">
							<span class="gamerz-next-rank-label">Next Rank:</span>
							<span class="gamerz-next-rank-name"><?php echo esc_html( $progress_info['next_rank']['name'] ); ?></span>
						</div>
						<div class="gamerz-xp-needed">
							<span class="gamerz-xp-needed-value"><?php echo number_format( $progress_info['xp_needed'] ); ?> XP needed</span>
						</div>
					</div>
				<?php endif; ?>

				<div class="gamerz-xp-progress-bar-container">
					<div class="gamerz-xp-progress-bar-bg">
						<div class="gamerz-xp-progress-bar-fill" style="width: <?php echo esc_attr( $progress_info['progress_percent'] ); ?>%"></div>
					</div>
					<div class="gamerz-progress-text">
						<span class="gamerz-progress-percent"><?php echo number_format( $progress_info['progress_percent'], 1 ); ?>%</span>
					</div>
				</div>

				<?php if ( $progress_info['next_rank'] ) : ?>
					<div class="gamerz-progress-details">
						<span class="gamerz-progress-range">
							<?php echo number_format( $progress_info['current_rank']['threshold'] ); ?> - 
							<?php echo number_format( $progress_info['next_rank']['threshold'] ); ?> XP
						</span>
						<span class="gamerz-progress-current">
							(<?php echo number_format( $progress_info['xp_in_current_rank'] ); ?>/<?php echo number_format( $progress_info['total_in_rank_range'] ); ?> in rank)
						</span>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<style>
		.gamerz-xp-progress-container {
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			padding: 20px;
			margin: 20px 0;
		}
		.gamerz-xp-progress-title {
			text-align: center;
			color: #333;
			margin-top: 0;
			margin-bottom: 15px;
		}
		.gamerz-xp-progress-box {
			text-align: center;
		}
		.gamerz-current-rank-info {
			margin-bottom: 15px;
		}
		.gamerz-rank-display {
			font-size: 1.5em;
			font-weight: bold;
			color: #007cba;
			margin-bottom: 5px;
		}
		.gamerz-rank-level {
			font-size: 0.8em;
			color: #666;
		}
		.gamerz-current-xp {
			font-size: 1.2em;
			color: #333;
		}
		.gamerz-next-rank-info {
			margin-bottom: 15px;
			padding: 10px;
			background: #f9f9f9;
			border-radius: 5px;
		}
		.gamerz-next-rank {
			margin-bottom: 5px;
		}
		.gamerz-next-rank-label {
			font-weight: bold;
			color: #666;
			margin-right: 5px;
		}
		.gamerz-next-rank-name {
			color: #007cba;
			font-weight: bold;
		}
		.gamerz-xp-needed-value {
			color: #e74c3c;
			font-weight: bold;
		}
		.gamerz-xp-progress-bar-container {
			margin: 20px 0;
			position: relative;
		}
		.gamerz-xp-progress-bar-bg {
			height: 20px;
			background: #eee;
			border-radius: 10px;
			overflow: hidden;
			position: relative;
		}
		.gamerz-xp-progress-bar-fill {
			height: 100%;
			background: linear-gradient(to right, #007cba, #00a0d2);
			border-radius: 10px;
			transition: width 0.5s ease;
		}
		.gamerz-progress-text {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			font-weight: bold;
			color: white;
			text-shadow: 1px 1px 1px rgba(0,0,0,0.5);
		}
		.gamerz-progress-details {
			font-size: 0.9em;
			color: #666;
			display: flex;
			justify-content: space-between;
		}
		</style>
		<?php

		return ob_get_clean();
	}

	/**
	 * AJAX handler to update leaderboard (for real-time updates)
	 */
	public function update_leaderboard_ajax() {
		$type = sanitize_text_field( $_POST['type'] ) ?: 'global';
		$limit = absint( $_POST['limit'] ) ?: 10;
		$guild_id = absint( $_POST['guild_id'] );

		$leaderboard = [];
		switch ( $type ) {
			case 'guild':
				if ( $guild_id ) {
					$leaderboard = $this->get_guild_leaderboard( $guild_id, $limit );
				}
				break;
			default:
				$leaderboard = $this->get_global_leaderboard( $limit );
		}

		wp_send_json_success( $leaderboard );
	}
}