<?php
/**
 * Challenges class
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
 * @subpackage Challenges
 * @author NH Tanvir <hi@tanvir.io>
 */
class Challenges {

	/**
	 * Current week's challenges
	 */
	public $current_challenges = [];

	/**
	 * Constructor function
	 */
	public function __construct() {
	}

	/**
	 * Register challenge post type.
	 */
	public function register_challenge_post_type() {
		$args = array(
			'labels' => array(
				'name'               => __( 'Challenges', 'gamerz-guild' ),
				'singular_name'      => __( 'Challenge', 'gamerz-guild' ),
				'add_new'            => __( 'Add New', 'gamerz-guild' ),
				'add_new_item'       => __( 'Add New Challenge', 'gamerz-guild' ),
				'edit_item'          => __( 'Edit Challenge', 'gamerz-guild' ),
				'new_item'           => __( 'New Challenge', 'gamerz-guild' ),
				'view_item'          => __( 'View Challenge', 'gamerz-guild' ),
				'search_items'       => __( 'Search Challenges', 'gamerz-guild' ),
				'not_found'          => __( 'No challenges found', 'gamerz-guild' ),
				'not_found_in_trash' => __( 'No challenges found in trash', 'gamerz-guild' ),
			),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => 'gamerz-guild-dashboard',
			'query_var'           => true,
			'rewrite'             => array(
				'slug' => 'challenge',
			),
			'capability_type'     => 'post',
			'has_archive'         => true,
			'hierarchical'        => false,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-awards',
			'supports'            => array(
				'title',
				'editor',
				'custom-fields',
			),
		);

		register_post_type( 'gamerz_challenge', $args );
	}

	/**
	 * Set up weekly challenges.
	 *
	 * @param array $challenges Weekly challenge definitions.
	 */
	public function setup_weekly_challenges( $challenges = array() ) {
		if ( empty( $challenges ) ) {
			$challenges = array(
				array(
					'title'       => 'Squad Up with a Newbie',
					'description' => 'Play at least one match this week teamed with a Scrub Gamerz member rank 1-3 (a newer member).',
					'reward_xp'   => 50,
					'badge_id'    => 'buddy_system',
					'type'        => 'social',
					'target'      => 1,
					'meta'        => array(
						'for_rank_range' => array( 1, 3 ),
					),
				),
				array(
					'title'       => 'Post a Build Guide',
					'description' => 'Write a detailed guide or strategy post in the forums (at least 300 words) for your favorite game.',
					'reward_xp'   => 100,
					'badge_id'    => 'guide_guru_bonus',
					'type'        => 'creative',
					'target'      => 1,
					'meta'        => array(
						'min_content_length' => 300,
					),
				),
				array(
					'title'       => 'Clip Contest – Trickshot',
					'description' => 'Submit a video clip of your best trickshot or epic play. Community votes on the best clip.',
					'reward_xp'   => 100,
					'badge_id'    => 'trickshot_master',
					'type'        => 'competitive',
					'target'      => 1,
					'meta'        => array(
						'submission_type' => 'video_clip',
					),
				),
			);
		}

		$week_number = date( 'W' );
		$year        = date( 'Y' );

		// Save challenges.
		foreach ( $challenges as $challenge ) {
			$post_id = wp_insert_post(
				array(
					'post_title'   => $challenge['title'],
					'post_content' => $challenge['description'],
					'post_status'  => 'publish',
					'post_type'    => 'gamerz_challenge',
					'post_date'    => current_time( 'mysql' ),
				)
			);

			if ( ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_challenge_reward_xp', $challenge['reward_xp'] );
				update_post_meta( $post_id, '_challenge_badge_id', $challenge['badge_id'] );
				update_post_meta( $post_id, '_challenge_type', $challenge['type'] );
				update_post_meta( $post_id, '_challenge_target', $challenge['target'] );
				update_post_meta( $post_id, '_challenge_meta', $challenge['meta'] );
				update_post_meta( $post_id, '_challenge_week', $week_number );
				update_post_meta( $post_id, '_challenge_year', $year );
			}
		}

		$this->current_challenges = $challenges;
	}

	/**
	 * Get active challenges for current week
	 */
	public function get_current_challenges() {
		$week_number = date( 'W' );
		$year        = date( 'Y' );

		if ( ! empty( $this->current_challenges ) ) {
			return $this->current_challenges;
		}

		$challenges = get_posts(
			array(
				'post_type'   => 'gamerz_challenge',
				'post_status' => 'publish',
				'numberposts' => 10,
				'meta_query'  => array(
					array(
						'key'     => '_challenge_week',
						'value'   => $week_number,
						'compare' => '=',
					),
					array(
						'key'     => '_challenge_year',
						'value'   => $year,
						'compare' => '=',
					),
				),
			)
		);


		$result = [];
		foreach ( $challenges as $challenge_post ) {
			$result[] = [
				'id' => $challenge_post->ID,
				'title' => $challenge_post->post_title,
				'description' => $challenge_post->post_content,
				'reward_xp' => get_post_meta( $challenge_post->ID, '_challenge_reward_xp', true ),
				'badge_id' => get_post_meta( $challenge_post->ID, '_challenge_badge_id', true ),
				'type' => get_post_meta( $challenge_post->ID, '_challenge_type', true ),
				'target' => get_post_meta( $challenge_post->ID, '_challenge_target', true ),
				'meta' => get_post_meta( $challenge_post->ID, '_challenge_meta', true ),
			];
		}

		$this->current_challenges = $result;
		return $result;
	}

	/**
	 * Check if user has completed a challenge this week
	 */
	public function has_user_completed_challenge( $user_id, $challenge_id ) {
		$completed = get_user_meta( $user_id, '_gamerz_completed_challenges', true );
		if ( ! is_array( $completed ) ) {
			$completed = [];
		}

		$week_key = date( 'Y-W' );
		
		if ( isset( $completed[ $week_key ] ) && in_array( $challenge_id, $completed[ $week_key ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Mark a challenge as completed for user
	 */
	public function mark_challenge_completed( $user_id, $challenge_id, $challenge_data = [] ) {
		$completed = get_user_meta( $user_id, '_gamerz_completed_challenges', true );
		if ( ! is_array( $completed ) ) {
			$completed = [];
		}

		$week_key = date( 'Y-W' );
		
		if ( ! isset( $completed[ $week_key ] ) ) {
			$completed[ $week_key ] = [];
		}

		if ( ! in_array( $challenge_id, $completed[ $week_key ] ) ) {
			$completed[ $week_key ][] = $challenge_id;
			update_user_meta( $user_id, '_gamerz_completed_challenges', $completed );

			$this->award_challenge_rewards( $user_id, $challenge_id, $challenge_data );

			$this->record_challenge_completion( $user_id, $challenge_id );

			return true;
		}

		return false;
	}

	/**
	 * Award rewards for completed challenge
	 */
	public function award_challenge_rewards( $user_id, $challenge_id, $challenge_data = [] ) {
		$xp_system = new XP_System();

		$reward_xp = isset( $challenge_data['reward_xp'] ) ? $challenge_data['reward_xp'] : 50;
		if ( $reward_xp > 0 ) {
			$mycred = $xp_system->get_mycred();
			if ( $mycred ) {
				$mycred->add_creds(
					'weekly_challenge',
					$user_id,
					$reward_xp,
					sprintf( __( 'Completed weekly challenge: %s', 'gamerz-guild' ),
						isset( $challenge_data['title'] ) ? $challenge_data['title'] : 'Weekly Challenge' ),
					$challenge_id,
					[],
					$xp_system->log_type
				);
			}
		}

		if ( isset( $challenge_data['badge_id'] ) ) {
			$badge_system = new Badge_System();
			$badge_system->award_weekly_challenge_badge( $user_id, $challenge_data['title'] );
		}

		$activity = new Guild_Activity();
		if ( $activity ) {

			$title = isset( $challenge_data['title'] ) ? $challenge_data['title'] : 'Weekly Challenge';

			$activity_data = array(
				'type'      => 'challenge_completed',
				'user_id'   => $user_id,
				'title'     => sprintf(
					/* translators: 1: user display name, 2: challenge title */
					__( '%1$s completed the challenge: %2$s!', 'gamerz-guild' ),
					$activity->get_user_display_name( $user_id ),
					$title
				),
				'content'   => sprintf(
					/* translators: 1: user display name, 2: XP earned, 3: challenge title */
					__( '%1$s earned %2$d XP for completing the challenge: %3$s', 'gamerz-guild' ),
					$activity->get_user_display_name( $user_id ),
					$reward_xp,
					$title
				),
				'timestamp' => current_time( 'mysql' ),
			);
		}
	}

	/**
	 * Record challenge completion
	 */
	private function record_challenge_completion( $user_id, $challenge_id ) {
		$log = get_user_meta( $user_id, '_gamerz_challenge_completion_log', true );
		if ( ! is_array( $log ) ) {
			$log = [];
		}

		$log[] = [
			'challenge_id' => $challenge_id,
			'completed_at' => current_time( 'mysql' ),
		];

		update_user_meta( $user_id, '_gamerz_challenge_completion_log', $log );
	}

	/**
	 * Handle challenge completion request
	 */
	public function handle_challenge_completion() {
		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'gamerz_challenge_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to complete challenges', 'gamerz-guild' ) );
		}

		$challenge_id = absint( $_POST['challenge_id'] );
		$user_id      = get_current_user_id();
		$challenges   = $this->get_current_challenges();
		$challenge    = null;
		foreach ( $challenges as $chall ) {
			if ( $chall['id'] == $challenge_id ) {
				$challenge = $chall;
				break;
			}
		}

		if ( ! $challenge ) {
			wp_die( __( 'Invalid challenge', 'gamerz-guild' ) );
		}

		if ( $this->has_user_completed_challenge( $user_id, $challenge_id ) ) {
			wp_die( __( 'You have already completed this challenge', 'gamerz-guild' ) );
		}

		$validation = $this->validate_challenge_completion( $user_id, $challenge );
		if ( ! $validation['valid'] ) {
			wp_die( $validation['message'] );
		}

		$result = $this->mark_challenge_completed( $user_id, $challenge_id, $challenge );

		if ( $result ) {
			wp_send_json_success( [
				'message' => __( 'Challenge completed successfully!', 'gamerz-guild' ),
				'xp_earned' => $challenge['reward_xp'],
			] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to complete challenge', 'gamerz-guild' ) ] );
		}
	}

	/**
	 * Handle challenge proof submission
	 */
	public function handle_challenge_proof_submission() {

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'gamerz_challenge_proof_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to submit challenge proof', 'gamerz-guild' ) );
		}

		$challenge_id = absint( $_POST['challenge_id'] );
		$user_id      = get_current_user_id();
		$proof        = sanitize_textarea_field( $_POST['proof'] );

		if ( empty( $proof ) ) {
			wp_die( __( 'Proof is required', 'gamerz-guild' ) );
		}

		$submission = array(
			'challenge_id' => $challenge_id,
			'user_id'      => $user_id,
			'proof'        => $proof,
			'submitted_at' => current_time( 'mysql' ),
			'status'       => 'pending',
		);

		$pending_submissions = get_option( '_gamerz_challenge_submissions', array() );
		$pending_submissions[] = $submission;
		update_option( '_gamerz_challenge_submissions', $pending_submissions );

		wp_send_json_success(
			array(
				'message' => __( 'Proof submitted successfully! Awaiting admin review.', 'gamerz-guild' ),
			)
		);
	}

	/**
	 * Validate challenge completion
	 */
	private function validate_challenge_completion( $user_id, $challenge ) {
		$valid = true;
		$message = '';

		switch ( $challenge['type'] ) {
			case 'social':
				$message = 'Challenge validated.';
				break;

			case 'creative':
				$message = 'Challenge validated.';
				break;

			case 'competitive':
				$valid = false;
				$message = 'This challenge requires proof submission for verification.';
				break;

			default:
				$message = 'Challenge validated.';
				break;
		}

		return [
			'valid' => $valid,
			'message' => $message,
		];
	}

	/**
	 * Render weekly challenges shortcode
	 */
	public function render_weekly_challenges_shortcode( $atts ) {

		error_log('render weekly challenges');
		$atts = shortcode_atts( [
			'title' => 'Weekly Challenges',
		], $atts );


		$challenges = $this->get_current_challenges();
		$user_id = get_current_user_id();

		ob_start();
		?>
		<div class="gamerz-weekly-challenges-container">
			<h3 class="gamerz-weekly-challenges-title"><?php echo esc_html( $atts['title'] ); ?></h3>
			<p class="gamerz-weekly-challenges-intro">
				<?php _e( 'Complete these challenges for bonus XP and exclusive badges!', 'gamerz-guild' ); ?>
			</p>

			<?php if ( ! empty( $challenges ) ) : ?>
				<div class="gamerz-weekly-challenges-list">
					<?php foreach ( $challenges as $challenge ) : ?>
						<div class="gamerz-challenge-item">
							<div class="gamerz-challenge-header">
								<h4 class="gamerz-challenge-title">
									<?php echo esc_html( $challenge['title'] ); ?>
									<?php if ( $this->has_user_completed_challenge( $user_id, $challenge['id'] ) ) : ?>
										<span class="gamerz-challenge-completed-badge">✓ Completed</span>
									<?php endif; ?>
								</h4>
								<span class="gamerz-challenge-xp-reward">+<?php echo esc_html( $challenge['reward_xp'] ); ?> XP</span>
							</div>
							
							<div class="gamerz-challenge-description">
								<?php echo wp_kses_post( wpautop( $challenge['description'] ) ); ?>
							</div>
							
							<div class="gamerz-challenge-actions">
								<?php if ( $this->has_user_completed_challenge( $user_id, $challenge['id'] ) ) : ?>
									<button class="gamerz-challenge-btn completed" disabled>
										<span class="dashicons dashicons-yes"></span> Completed!
									</button>
								<?php else : ?>
									<?php if ( $challenge['type'] === 'competitive' ) : ?>
										<!-- For challenges that require proof -->
										<button class="gamerz-challenge-proof-btn" data-challenge-id="<?php echo esc_attr( $challenge['id'] ); ?>">
											<span class="dashicons dashicons-edit"></span> Submit Proof
										</button>
									<?php else : ?>
										<button class="gamerz-challenge-complete-btn" data-challenge-id="<?php echo esc_attr( $challenge['id'] ); ?>">
											<span class="dashicons dashicons-yes"></span> Mark Complete
										</button>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="gamerz-no-challenges">
					<p><?php _e( 'No weekly challenges available at this time. Check back next week!', 'gamerz-guild' ); ?></p>
				</div>
			<?php endif; ?>

			<div id="gamerz-challenge-proof-modal" style="display:none;">
				<div class="gamerz-proof-modal-content">
					<h4>Submit Proof for Challenge</h4>
					<form id="gamerz-challenge-proof-form">
						<input type="hidden" id="gamerz-proof-challenge-id" value="">
						<p>Please provide proof of completing the challenge:</p>
						<textarea id="gamerz-proof-textarea" placeholder="Describe what you did, include links to posts, screenshots, etc." rows="5"></textarea>
						<div class="gamerz-proof-actions">
							<button type="button" id="gamerz-submit-proof-btn">Submit Proof</button>
							<button type="button" id="gamerz-cancel-proof-btn">Cancel</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			// Handle challenge complete button
			$('.gamerz-challenge-complete-btn').on('click', function() {
				var challengeId = $(this).data('challenge-id');
				var btn = $(this);
				
				$.post(ajaxurl, {
					action: 'complete_challenge',
					challenge_id: challengeId,
					nonce: '<?php echo wp_create_nonce( 'gamerz_challenge_nonce' ); ?>'
				}, function(response) {
					if (response.success) {
						btn.html('<span class="dashicons dashicons-yes"></span> Completed!').prop('disabled', true);
						btn.parent().prev().find('.gamerz-challenge-completed-badge').show();
						alert('Challenge completed! ' + response.data.xp_earned + ' XP earned.');
					} else {
						alert('Error: ' + response.data.message);
					}
				});
			});

			// Handle proof submission button
			$('.gamerz-challenge-proof-btn').on('click', function() {
				var challengeId = $(this).data('challenge-id');
				$('#gamerz-proof-challenge-id').val(challengeId);
				$('#gamerz-challenge-proof-modal').show();
			});

			// Handle proof submission
			$('#gamerz-submit-proof-btn').on('click', function() {
				var challengeId = $('#gamerz-proof-challenge-id').val();
				var proof = $('#gamerz-proof-textarea').val();

				$.post(ajaxurl, {
					action: 'submit_challenge_proof',
					challenge_id: challengeId,
					proof: proof,
					nonce: '<?php echo wp_create_nonce( 'gamerz_challenge_proof_nonce' ); ?>'
				}, function(response) {
					if (response.success) {
						alert(response.data.message);
						$('#gamerz-challenge-proof-modal').hide();
						$('#gamerz-proof-textarea').val('');
					} else {
						alert('Error: ' + response.data.message);
					}
				});
			});

			// Close modal
			$('#gamerz-cancel-proof-btn').on('click', function() {
				$('#gamerz-challenge-proof-modal').hide();
			});

			// Click outside modal to close
			$(document).mouseup(function(e) {
				var container = $('#gamerz-challenge-proof-modal .gamerz-proof-modal-content');
				if (!container.is(e.target) && container.has(e.target).length === 0) {
					$('#gamerz-challenge-proof-modal').hide();
				}
			});
		});
		</script>

		<style>
		.gamerz-weekly-challenges-container {
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			padding: 25px;
			margin: 20px 0;
		}
		.gamerz-weekly-challenges-title {
			text-align: center;
			color: #333;
			margin-top: 0;
			margin-bottom: 15px;
		}
		.gamerz-weekly-challenges-intro {
			text-align: center;
			color: #666;
			margin-bottom: 25px;
			font-style: italic;
		}
		.gamerz-weekly-challenges-list {
			display: grid;
			gap: 20px;
		}
		.gamerz-challenge-item {
			border: 1px solid #eee;
			border-radius: 5px;
			padding: 15px;
			background: #fafafa;
		}
		.gamerz-challenge-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 10px;
		}
		.gamerz-challenge-title {
			margin: 0;
			color: #333;
			font-size: 1.2em;
		}
		.gamerz-challenge-completed-badge {
			background: #28a745;
			color: white;
			padding: 2px 8px;
			border-radius: 12px;
			font-size: 0.8em;
			margin-left: 10px;
		}
		.gamerz-challenge-xp-reward {
			background: #007cba;
			color: white;
			padding: 5px 10px;
			border-radius: 15px;
			font-size: 0.9em;
		}
		.gamerz-challenge-description {
			margin-bottom: 15px;
			color: #555;
		}
		.gamerz-challenge-actions {
			text-align: right;
		}
		.gamerz-challenge-btn, .gamerz-challenge-proof-btn, .gamerz-challenge-complete-btn {
			padding: 8px 16px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 0.9em;
			display: inline-flex;
			align-items: center;
			gap: 5px;
		}
		.gamerz-challenge-btn.completed {
			background: #28a745;
			color: white;
		}
		.gamerz-challenge-btn:not(.completed) {
			background: #007cba;
			color: white;
		}
		.gamerz-challenge-proof-btn {
			background: #ffc107;
			color: #212529;
		}
		.gamerz-no-challenges {
			text-align: center;
			padding: 30px;
			color: #888;
		}

		/* Proof modal */
		#gamerz-challenge-proof-modal {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0,0,0,0.7);
			z-index: 9999;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.gamerz-proof-modal-content {
			background: white;
			padding: 25px;
			border-radius: 8px;
			width: 90%;
			max-width: 500px;
			position: relative;
		}
		#gamerz-proof-textarea {
			width: 100%;
			padding: 10px;
			border: 1px solid #ddd;
			border-radius: 4px;
			margin: 10px 0;
		}
		.gamerz-proof-actions {
			text-align: right;
			margin-top: 15px;
		}
		.gamerz-proof-actions button {
			padding: 8px 16px;
			margin-left: 10px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
		}
		#gamerz-submit-proof-btn {
			background: #28a745;
			color: white;
		}
		#gamerz-cancel-proof-btn {
			background: #6c757d;
			color: white;
		}
		</style>
		<?php

		return ob_get_clean();
	}

	/**
	 * Render user's challenge history shortcode
	 */
	public function render_my_challenges_shortcode( $atts ) {
		$atts = shortcode_atts( [
			'title' => 'My Challenge History',
		], $atts );


		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return __( 'Please log in to view your challenge history.', 'gamerz-guild' );
		}

		$log = get_user_meta( $user_id, '_gamerz_challenge_completion_log', true );
		$log = is_array( $log ) ? $log : [];

		ob_start();
		?>
		<div class="gamerz-my-challenges-container">
			<h3 class="gamerz-my-challenges-title"><?php echo esc_html( $atts['title'] ); ?></h3>
			<?php if ( ! empty( $log ) ) : ?>
				<div class="gamerz-challenges-history">
					<?php foreach ( array_reverse( $log ) as $entry ) : ?>
						<div class="gamerz-challenge-history-item">
							<span class="gamerz-challenge-date"><?php echo date( 'M j, Y', strtotime( $entry['completed_at'] ) ); ?></span>
							<span class="gamerz-challenge-name">#<?php echo esc_html( $entry['challenge_id'] ); ?> Completed</span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="gamerz-no-challenges-history">
					<p><?php _e( 'You haven\'t completed any challenges yet. Complete this week\'s challenges to get started!', 'gamerz-guild' ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<style>
		.gamerz-my-challenges-container {
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.1);
			padding: 20px;
			margin: 20px 0;
		}
		.gamerz-my-challenges-title {
			text-align: center;
			color: #333;
			margin-top: 0;
			margin-bottom: 15px;
		}
		.gamerz-challenges-history {
			display: flex;
			flex-direction: column;
			gap: 10px;
		}
		.gamerz-challenge-history-item {
			display: flex;
			justify-content: space-between;
			padding: 10px;
			border-bottom: 1px solid #eee;
		}
		.gamerz-challenge-history-item:last-child {
			border-bottom: none;
		}
		.gamerz-challenge-date {
			color: #666;
			font-size: 0.9em;
		}
		.gamerz-challenge-name {
			font-weight: bold;
			color: #333;
		}
		.gamerz-no-challenges-history {
			text-align: center;
			padding: 30px;
			color: #888;
		}
		</style>
		<?php

		return ob_get_clean();
	}

	/**
	 * Reset weekly challenges
	 */
	public function reset_weekly_challenges() {
		// This is run by cron to reset everything for the new week
		// In a real implementation, you might want to archive the old challenges
		// and set up new ones, but for now we'll just let the system generate new ones
		
		// Clean up very old challenge data (older than 3 months)
		$this->cleanup_old_challenge_data();
	}

	/**
	 * Clean up old challenge data
	 */
	private function cleanup_old_challenge_data() {
		// Remove challenge completion data older than 3 months
		$users = get_users( [ 'fields' => [ 'ID' ] ] );
		foreach ( $users as $user ) {
			$completed = get_user_meta( $user->ID, '_gamerz_completed_challenges', true );
			if ( is_array( $completed ) ) {
				// Keep only recent weeks (last 12 weeks)
				$twelve_weeks_ago = date( 'Y-W', strtotime( '-12 weeks' ) );
				$recent_completed = [];
				
				foreach ( $completed as $week => $challenges ) {
					if ( $week >= $twelve_weeks_ago ) {
						$recent_completed[ $week ] = $challenges;
					}
				}
				
				update_user_meta( $user->ID, '_gamerz_completed_challenges', $recent_completed );
			}
		}
	}
}