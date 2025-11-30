<?php
/**
 * Visual_Enhancements class
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
 * @subpackage Visual_Enhancements
 * @author NH Tanvir <hi@tanvir.io>
 */
class Visual_Enhancements {

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the visual enhancements
	 */
	public function init() {
		// Hooks have been moved to app/Visual_Hooks.php
	}

	/**
	 * Enqueue frontend styles
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 
			'gamerz-visual-enhancements', 
			plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/visual-enhancements.css', 
			[], 
			filemtime( dirname( dirname( __DIR__ ) ) . '/assets/css/visual-enhancements.css' ) 
		);
	}

	/**
	 * Enqueue frontend scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 
			'gamerz-visual-enhancements-js', 
			plugin_dir_url( dirname( __DIR__ ) ) . 'assets/js/visual-enhancements.js', 
			[ 'jquery' ], 
			filemtime( dirname( dirname( __DIR__ ) ) . '/assets/js/visual-enhancements.js' ), 
			true 
		);

		// Localize script with data
		wp_localize_script( 'gamerz-visual-enhancements-js', 'gamerz_vars', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'gamerz_visual_enhancements' ),
		] );
	}

	/**
	 * Enqueue admin styles
	 */
	public function enqueue_admin_styles( $hook ) {
		wp_enqueue_style( 
			'gamerz-admin-visual-enhancements', 
			plugin_dir_url( dirname( __DIR__ ) ) . 'assets/css/admin-visual-enhancements.css', 
			[], 
			filemtime( dirname( dirname( __DIR__ ) ) . '/assets/css/admin-visual-enhancements.css' ) 
		);
	}

	/**
	 * Add XP display to BuddyPress profiles
	 */
	public function add_profile_xp_display() {
		if ( ! function_exists( 'bp_displayed_user_id' ) ) {
			return;
		}

		$user_id = bp_displayed_user_id();
		$rank_system = new Rank_System();
		$xp_system = new XP_System();
		$badge_system = new Badge_System();

		$rank = $rank_system->get_user_rank( $user_id );
		$user_xp = $xp_system->get_user_xp( $user_id );
		$badges = $badge_system->get_user_badges( $user_id );

		if ( ! $rank ) {
			return;
		}

		$progress_info = $rank_system->get_rank_progress( $user_id );
		$is_current_user = bp_displayed_user_id() === bp_loggedin_user_id();

		?>
		<div class="gamerz-profile-stats">
			<div class="gamerz-rank-display">
				<div class="gamerz-rank-badge" style="color: <?php echo $this->get_rank_color( $rank['id'] ); ?>;">
					<?php echo esc_html( $rank['name'] ); ?>
				</div>
				<div class="gamerz-rank-level">
					Level <?php echo esc_html( $rank['id'] ); ?>
				</div>
			</div>

			<div class="gamerz-xp-display">
				<div class="gamerz-xp-amount">
					<?php echo number_format( $user_xp ); ?> XP
				</div>
				<div class="gamerz-xp-progress">
					<div class="gamerz-xp-bar">
						<div class="gamerz-xp-fill" style="width: <?php echo $progress_info['progress_percent']; ?>%;"></div>
					</div>
					<div class="gamerz-xp-text">
						<?php if ( $progress_info['next_rank'] ) : ?>
							<?php echo number_format( $progress_info['xp_in_current_rank'] ); ?> / <?php echo number_format( $progress_info['total_in_rank_range'] ); ?> XP to <?php echo esc_html( $progress_info['next_rank']['name'] ); ?>
						<?php else : ?>
							Max Rank Reached!
						<?php endif; ?>
					</div>
				</div>
			</div>

			<?php if ( ! empty( $badges ) ) : ?>
				<div class="gamerz-badges-display">
					<div class="gamerz-badges-label">Badges:</div>
					<div class="gamerz-badges-list">
						<?php
						$count = 0;
						foreach ( array_slice( $badges, 0, 5 ) as $badge ) : // Show first 5 badges
							?>
							<span class="gamerz-badge" title="<?php echo esc_attr( $badge['description'] ); ?>">
								<span class="gamerz-badge-icon"><?php echo esc_html( $badge['name'][0] ); ?></span>
							</span>
							<?php
							$count++;
						?>
						<?php endforeach; ?>
						<?php if ( count( $badges ) > 5 ) : ?>
							<span class="gamerz-badge more">+<?php echo count( $badges ) - 5; ?></span>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $is_current_user ) : ?>
				<a href="<?php echo esc_url( home_url( '/my-progress' ) ); ?>" class="gamerz-view-full-progress">
					View Full Progress
				</a>
			<?php endif; ?>
		</div>

		<style>
		.gamerz-profile-stats {
			background: linear-gradient(135deg, #0073aa, #00a0d2);
			color: white;
			padding: 20px;
			border-radius: 8px;
			margin: 15px 0;
			box-shadow: 0 4px 6px rgba(0,0,0,0.1);
		}
		.gamerz-rank-display {
			text-align: center;
			margin-bottom: 15px;
		}
		.gamerz-rank-badge {
			font-size: 1.5em;
			font-weight: bold;
			display: block;
			text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
		}
		.gamerz-rank-level {
			font-size: 0.9em;
			opacity: 0.9;
		}
		.gamerz-xp-display {
			margin-bottom: 15px;
		}
		.gamerz-xp-amount {
			font-size: 1.8em;
			font-weight: bold;
			text-align: center;
			margin-bottom: 10px;
		}
		.gamerz-xp-progress {
			text-align: center;
		}
		.gamerz-xp-bar {
			background: rgba(255,255,255,0.3);
			height: 10px;
			border-radius: 5px;
			margin-bottom: 5px;
			overflow: hidden;
		}
		.gamerz-xp-fill {
			height: 100%;
			background: rgba(255,255,255,0.8);
			border-radius: 5px;
			transition: width 0.5s ease;
		}
		.gamerz-xp-text {
			font-size: 0.8em;
			opacity: 0.9;
		}
		.gamerz-badges-display {
			margin-top: 10px;
		}
		.gamerz-badges-label {
			font-weight: bold;
			margin-bottom: 5px;
			text-align: center;
		}
		.gamerz-badges-list {
			display: flex;
			gap: 5px;
			justify-content: center;
			flex-wrap: wrap;
		}
		.gamerz-badge {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			background: rgba(255,255,255,0.2);
			border-radius: 4px;
			padding: 2px 6px;
			font-size: 0.8em;
			cursor: help;
		}
		.gamerz-badge.more {
			font-size: 0.7em;
		}
		.gamerz-badge-icon {
			width: 16px;
			height: 16px;
			display: flex;
			align-items: center;
			justify-content: center;
			background: rgba(255,255,255,0.3);
			border-radius: 50%;
			font-size: 0.7em;
			margin-right: 4px;
		}
		.gamerz-view-full-progress {
			display: block;
			background: rgba(255,255,255,0.2);
			color: white;
			text-align: center;
			padding: 8px;
			border-radius: 4px;
			text-decoration: none;
			margin-top: 15px;
		}
		.gamerz-view-full-progress:hover {
			background: rgba(255,255,255,0.3);
		}
		</style>
		<?php
	}

	/**
	 * Add XP display to Youzify profiles
	 */
	public function add_youzify_profile_xp_display() {
		// Similar to the BuddyPress function but styled for Youzify
		$this->add_profile_xp_display();
	}

	/**
	 * Add XP bar to site header
	 */
	public function add_xp_bar_to_header() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		$rank_system = new Rank_System();
		$xp_system = new XP_System();

		$rank = $rank_system->get_user_rank( $user_id );
		$progress_info = $rank_system->get_rank_progress( $user_id );

		if ( ! $rank ) {
			return;
		}

		?>
		<div id="gamerz-xp-header-bar" style="position: fixed; bottom: 20px; left: 20px; z-index: 9999; display: flex; align-items: center; gap: 10px; background: rgba(0,0,0,0.8); color: white; padding: 8px 15px; border-radius: 20px; backdrop-filter: blur(5px); box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
			<div class="gamerz-xp-rank-indicator">
				<span style="font-weight: bold;"><?php echo esc_html( $rank['name'] ); ?></span>
			</div>
			<div class="gamerz-xp-progress-container" style="width: 150px;">
				<div class="gamerz-xp-bar" style="background: #333; height: 8px; border-radius: 4px; overflow: hidden;">
					<div class="gamerz-xp-fill" style="background: linear-gradient(to right, #0073aa, #00a0d2); height: 100%; width: <?php echo $progress_info['progress_percent']; ?>%; transition: width 0.5s ease;"></div>
				</div>
			</div>
			<div class="gamerz-xp-amount" style="font-size: 0.9em; min-width: 60px;">
				<?php echo number_format( $progress_info['current_xp'] ); ?> XP
			</div>
		</div>

		<script>
		// Animate the XP bar when the user gains XP (would be triggered by AJAX)
		jQuery(document).ready(function($) {
			// Listen for XP gain events
			$(document).on('gamerz_xp_gained', function(e, xp_amount) {
				$('#gamerz-xp-header-bar').effect('pulsate', {times: 2}, 300);
				
				// Show XP gain notification
				var notification = $('<div class="gamerz-xp-notification">+' + xp_amount + ' XP</div>');
				notification.css({
					'position': 'fixed',
					'bottom': '80px',
					'left': '50%',
					'transform': 'translateX(-50%)',
					'background': '#00a0d2',
					'color': 'white',
					'padding': '10px 20px',
					'border-radius': '20px',
					'z-index': '10000',
					'box-shadow': '0 4px 15px rgba(0,0,0,0.3)',
					'font-weight': 'bold',
					'animation': 'xpNotification 1s ease-out forwards'
				});
				
				$('body').append(notification);
				
				setTimeout(function() {
					notification.remove();
				}, 1000);
			});
		});
		</script>

		<style>
		@keyframes xpNotification {
			0% { transform: translateX(-50%) translateY(0); opacity: 1; }
			100% { transform: translateX(-50%) translateY(-50px); opacity: 0; }
		}
		</style>
		<?php
	}

	/**
	 * Add achievement animations to footer
	 */
	public function add_achievement_animations() {
		?>
		<script>
		jQuery(document).ready(function($) {
			// Confetti effect for achievements
			function gamerzConfetti() {
				// This would require a confetti library, simplified version:
				// In a real implementation, you'd use a library like canvas-confetti
				var particles = 50;
				var container = $('body');
				
				for (var i = 0; i < particles; i++) {
					var particle = $('<div class="gamerz-confetti"></div>');
					particle.css({
						position: 'fixed',
						top: '0',
						left: Math.random() * 100 + 'vw',
						width: '8px',
						height: '8px',
						background: '#f0f0f0',
						borderRadius: '50%',
						zIndex: 99999,
						opacity: 0.8
					});
					
					container.append(particle);
					
					// Animate
					particle.animate({
						top: '100vh',
						left: (parseFloat(particle.css('left')) + (Math.random() * 100 - 50)) + 'px',
						opacity: 0
					}, 2000, 'linear', function() {
						$(this).remove();
					});
				}
			}
			
			// Listen for achievement unlock
			$(document).on('gamerz_achievement_unlocked', function(e, badgeName) {
				// Show achievement notification
				var achievement = $('<div class="gamerz-achievement-notification">Achievement Unlocked: ' + badgeName + '!</div>');
				achievement.css({
					'position': 'fixed',
					'top': '20px',
					'right': '20px',
					'background': 'linear-gradient(135deg, #0073aa, #00a0d2)',
					'color': 'white',
					'padding': '15px 25px',
					'border-radius': '8px',
					'z-index': '10000',
					'box-shadow': '0 4px 15px rgba(0,0,0,0.3)',
					'font-weight': 'bold',
					'animation': 'achievementSlideIn 0.5s ease-out, achievementSlideOut 0.5s ease-out 4.5s forwards'
				});
				
				$('body').append(achievement);
				
				// Trigger confetti
				gamerzConfetti();
				
				setTimeout(function() {
					achievement.css({
						'animation': 'achievementSlideOut 0.5s ease-out forwards'
					});
					
					setTimeout(function() {
						achievement.remove();
					}, 500);
				}, 5000);
			});
		});
		</script>

		<style>
		@keyframes achievementSlideIn {
			from { transform: translateX(100%); opacity: 0; }
			to { transform: translateX(0); opacity: 1; }
		}
		@keyframes achievementSlideOut {
			from { transform: translateX(0); opacity: 1; }
			to { transform: translateX(100%); opacity: 0; }
		}
		.gamerz-confetti {
			position: fixed;
			width: 10px;
			height: 10px;
			border-radius: 50%;
			background: #ffd700;
		}
		.gamerz-achievement-notification {
			position: fixed;
			top: 20px;
			right: 20px;
			background: linear-gradient(135deg, #0073aa, #00a0d2);
			color: white;
			padding: 15px 25px;
			border-radius: 8px;
			z-index: 10000;
			box-shadow: 0 4px 15px rgba(0,0,0,0.3);
			font-weight: bold;
		}
		</style>
		<?php
	}

	/**
	 * Add rank indicator to avatars
	 */
	public function add_rank_avatar_indicator( $avatar, $id_or_email, $size, $default, $alt, $args ) {
		// Determine user ID from the input
		if ( is_numeric( $id_or_email ) ) {
			$user_id = (int) $id_or_email;
		} elseif ( is_object( $id_or_email ) ) {
			if ( ! empty( $id_or_email->user_id ) ) {
				$user_id = (int) $id_or_email->user_id;
			} elseif ( ! empty( $id_or_email->ID ) ) {
				$user_id = (int) $id_or_email->ID;
			} else {
				return $avatar;
			}
		} elseif ( is_email( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
			$user_id = $user ? $user->ID : false;
		} else {
			return $avatar;
		}

		if ( ! $user_id ) {
			return $avatar;
		}

		$rank_system = new Rank_System();
		$rank = $rank_system->get_user_rank( $user_id );

		if ( ! $rank ) {
			return $avatar;
		}

		// Get the rank color
		$rank_color = $this->get_rank_color( $rank['id'] );

		// Add a border that represents the rank level
		$border_size = 2;
		if ( $rank['id'] >= 10 ) {
			$border_size = 3; // Thicker border for high ranks
		} elseif ( $rank['id'] >= 5 ) {
			$border_size = 2.5; // Medium thick border
		}

		$avatar = str_replace(
			'avatar-'.$size.'"', 
			'avatar-'.$size.'" style="border: '.$border_size.'px solid '.$rank_color.' !important; border-radius: 50%;"', 
			$avatar
		);

		// Add a rank badge overlay for larger avatars
		if ( $size >= 64 ) {
			$badge_html = '<div class="gamerz-rank-badge-overlay" style="position: absolute; bottom: 0; right: 0; background: '.$rank_color.'; color: white; font-size: '.($size/8).'px; width: '.($size/4).'px; height: '.($size/4).'px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transform: translate(25%, 25%); font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">';
			$badge_html .= substr($rank['name'], 0, 1);
			$badge_html .= '</div>';

			$avatar = '<div style="position: relative; display: inline-block;">' . $avatar . $badge_html . '</div>';
		}

		return $avatar;
	}

	/**
	 * Get color based on rank level
	 */
	public function get_rank_color( $rank_level ) {
		$colors = [
			1 => '#808080',  // Gray for Scrubling
			2 => '#A9A9A9',  // Dark Gray for Scrub Recruit
			3 => '#FFD700',  // Gold for Scrub Scout
			4 => '#FFA500',  // Orange for Scrub Soldier
			5 => '#9932CC',  // Purple for Scrub Strategist
			6 => '#8A2BE2',  // Blue Violet for Scrub Captain
			7 => '#FF4500',  // Orange Red for Scrub Champion
			8 => '#DC143C',  // Crimson for Guild Officer
			9 => '#FF69B4',  // Hot Pink for Scrub Sage
			10 => '#FF1493', // Deep Pink for Scrub Warlord
			11 => '#00FF00', // Green for Meme Master
			12 => '#7CFC00', // Lawn Green for Scrub Overlord
			13 => '#00FFFF', // Cyan for Nova Scrub
			14 => '#00BFFF', // Deep Sky Blue for Scrub Prime
			15 => '#FFD700', // Gold for Legendary Scrub
		];

		return isset( $colors[ $rank_level ] ) ? $colors[ $rank_level ] : '#FFFFFF'; // White default
	}

	/**
	 * Add custom CSS for gamified elements
	 */
	public function add_custom_css() {
		?>
		<style>
		/* Gamified UI Elements */
		.gamerz-gamified-element {
			background: linear-gradient(135deg, #0073aa, #00a0d2);
			border-radius: 8px;
			padding: 20px;
			margin: 10px 0;
			box-shadow: 0 4px 12px rgba(0,0,0,0.1);
			color: white;
		}

		.gamerz-progress-bar {
			height: 20px;
			background: rgba(255,255,255,0.2);
			border-radius: 10px;
			overflow: hidden;
			margin: 10px 0;
		}

		.gamerz-progress-fill {
			height: 100%;
			background: linear-gradient(to right, #0073aa, #00a0d2);
			border-radius: 10px;
			transition: width 0.5s ease;
		}

		.gamerz-badge-capsule {
			display: inline-block;
			background: rgba(255,255,255,0.1);
			padding: 5px 12px;
			border-radius: 20px;
			font-size: 0.85em;
			margin: 2px;
		}

		.gamerz-rank-chip {
			display: inline-block;
			padding: 4px 10px;
			border-radius: 15px;
			font-size: 0.8em;
			font-weight: bold;
			text-transform: uppercase;
		}

		/* Animated elements */
		.gamerz-level-up {
			animation: levelUpAnimation 0.5s ease;
		}

		@keyframes levelUpAnimation {
			0% { transform: scale(1); }
			50% { transform: scale(1.1); }
			100% { transform: scale(1); }
		}

		/* Tooltip for badges */
		.gamerz-badge-tooltip {
			position: relative;
			display: inline-block;
		}

		.gamerz-badge-tooltip .gamerz-tooltip-text {
			visibility: hidden;
			width: 200px;
			background-color: black;
			color: #fff;
			text-align: center;
			border-radius: 6px;
			padding: 10px;
			position: absolute;
			z-index: 1;
			bottom: 125%;
			left: 50%;
			margin-left: -100px;
			opacity: 0;
			transition: opacity 0.3s;
		}

		.gamerz-badge-tooltip:hover .gamerz-tooltip-text {
			visibility: visible;
			opacity: 1;
		}
		</style>
		<?php
	}

	/**
	 * Add neon/glow effects for special elements
	 */
	public function add_neon_effects( $element_class = 'gamerz-neon-element' ) {
		?>
		<style>
		.<?php echo $element_class; ?> {
			position: relative;
			border-radius: 8px;
			box-shadow: 0 0 15px currentColor;
			animation: glow-pulse 2s infinite alternate;
		}

		@keyframes glow-pulse {
			from {
				box-shadow: 0 0 15px currentColor;
			}
			to {
				box-shadow: 0 0 25px currentColor, 0 0 35px currentColor;
			}
		}

		.gamerz-hud-element {
			background: rgba(0, 0, 0, 0.7);
			border: 1px solid #00ffff;
			box-shadow: 0 0 15px rgba(0, 255, 255, 0.5);
			border-radius: 4px;
			color: #00ffff;
			font-family: 'Courier New', monospace;
			text-shadow: 0 0 5px #00ffff;
		}
		</style>
		<?php
	}

	/**
	 * Add CSS for all visual enhancements
	 */
	public function add_all_css() {
		$this->add_custom_css();
		$this->add_neon_effects();
	}
}