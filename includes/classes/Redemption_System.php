<?php
/**
 * Redemption_System class
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
 * @subpackage Redemption_System
 * @author NH Tanvir <hi@tanvir.io>
 */
class Redemption_System {

	/**
	 * Redemption items structure
	 */
	public $redemption_items = [
		// Real-world rewards (merchandise)
		'merch_5_discount' => [
			'id' => 'merch_5_discount',
			'name' => '$5 Merch Discount',
			'description' => 'Redeem 1000 XP for a $5 discount on any merchandise purchase',
			'cost' => 1000,
			'type' => 'discount',
			'value' => 5,
			'restriction' => 'currency',
			'max_per_user' => 3,
			'min_rank' => 5, // Scrub Strategist
		],
		'merch_10_discount' => [
			'id' => 'merch_10_discount',
			'name' => '$10 Merch Discount',
			'description' => 'Redeem 2000 XP for a $10 discount on any merchandise purchase',
			'cost' => 2000,
			'type' => 'discount',
			'value' => 10,
			'restriction' => 'currency',
			'max_per_user' => 2,
			'min_rank' => 8, // Guild Officer
		],
		'merch_10_percent' => [
			'id' => 'merch_10_percent',
			'name' => '10% Off Merch',
			'description' => 'Redeem 1500 XP for 10% off any merchandise purchase',
			'cost' => 1500,
			'type' => 'discount',
			'value' => 10,
			'restriction' => 'percent',
			'max_per_user' => 1,
			'min_rank' => 10, // Scrub Warlord
		],

		// Digital rewards (cosmetics)
		'custom_avatar_frame' => [
			'id' => 'custom_avatar_frame',
			'name' => 'Custom Avatar Frame',
			'description' => 'Redeem 500 XP to unlock a special animated avatar frame for 30 days',
			'cost' => 500,
			'type' => 'cosmetic',
			'value' => 'animated_frame',
			'duration' => 30,
			'max_per_user' => 0, // Unlimited
			'min_rank' => 4, // Scrub Soldier
		],
		'username_glow' => [
			'id' => 'username_glow',
			'name' => 'Username Glow Effect',
			'description' => 'Redeem 200 XP to add a glow effect to your username for 30 days',
			'cost' => 200,
			'type' => 'cosmetic',
			'value' => 'glow_effect',
			'duration' => 30,
			'max_per_user' => 0, // Unlimited
			'min_rank' => 3, // Scrub Scout
		],
		'custom_forum_title' => [
			'id' => 'custom_forum_title',
			'name' => 'Custom Forum Title',
			'description' => 'Redeem 300 XP to set a custom title under your name in forums (one-time)',
			'cost' => 300,
			'type' => 'cosmetic',
			'value' => 'custom_title',
			'duration' => null,
			'max_per_user' => 1,
			'min_rank' => 5, // Scrub Strategist
		],

		// Access rewards
		'vip_voice_access' => [
			'id' => 'vip_voice_access',
			'name' => 'VIP Voice Channel Access',
			'description' => 'Redeem 1000 XP for 30 days access to VIP Discord voice channels',
			'cost' => 1000,
			'type' => 'access',
			'value' => 'vip_voice',
			'duration' => 30,
			'max_per_user' => 0, // Unlimited
			'min_rank' => 7, // Scrub Champion
		],
		'ad_free_month' => [
			'id' => 'ad_free_month',
			'name' => 'Ad-Free Browsing',
			'description' => 'Redeem 750 XP to browse the site ad-free for 30 days',
			'cost' => 750,
			'type' => 'access',
			'value' => 'ad_free',
			'duration' => 30,
			'max_per_user' => 0, // Unlimited
			'min_rank' => 6, // Scrub Captain
		],

		// Digital content
		'scrub_wallpapers' => [
			'id' => 'scrub_wallpapers',
			'name' => 'Scrub Gamerz Wallpapers',
			'description' => 'Redeem 500 XP to download a set of high-res Scrub Gamerz wallpapers',
			'cost' => 500,
			'type' => 'digital',
			'value' => 'wallpapers_pack',
			'download_url' => '',
			'max_per_user' => 1,
			'min_rank' => 5, // Scrub Strategist
		],
		'steam_gift_5' => [
			'id' => 'steam_gift_5',
			'name' => '$5 Steam Gift Card',
			'description' => 'Redeem 5000 XP for a $5 Steam gift card (during special promotions)',
			'cost' => 5000,
			'type' => 'digital',
			'value' => 5,
			'restriction' => 'currency',
			'max_per_user' => 1,
			'min_rank' => 12, // Scrub Overlord
		],
	];

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the redemption system
	 */
	public function init() {
		// Check if WooCommerce is active
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Hooks have been moved to app/Redemption_Hooks.php
	}

	/**
	 * Create WooCommerce products for redemption items
	 */
	public function create_redemption_products() {
		// Only create products if they don't exist
		foreach ( $this->redemption_items as $item_id => $item ) {
			if ( $item['type'] === 'discount' || $item['type'] === 'cosmetic' ) {
				$this->create_virtual_product( $item_id, $item );
			}
		}
	}

	/**
	 * Create a virtual WooCommerce product for redemption
	 */
	public function create_virtual_product( $item_id, $item ) {
		// Check if product already exists
		$existing_product = $this->get_redemption_product_by_item_id( $item_id );
		if ( $existing_product ) {
			return $existing_product;
		}

		// Create the product
		$product = new \WC_Product();
		$product->set_name( $item['name'] );
		$product->set_status( 'publish' );
		$product->set_virtual( true );
		$product->set_downloadable( true );
		$product->set_catalog_visibility( 'hidden' ); // Don't show in regular store
		$product->set_price( 0 ); // Price will be covered by XP deduction
		$product->set_regular_price( 0 );
		
		// Add custom field to identify as redemption item
		$product->update_meta_data( '_gamerz_redemption_item', $item_id );
		$product->update_meta_data( '_gamerz_xp_cost', $item['cost'] );
		$product->set_description( $item['description'] );

		$product_id = $product->save();
		
		return $product_id;
	}

	/**
	 * Get redemption product by item ID
	 */
	public function get_redemption_product_by_item_id( $item_id ) {
		$products = get_posts([
			'post_type' => 'product',
			'post_status' => 'any',
			'numberposts' => 1,
			'meta_query' => [
				[
					'key' => '_gamerz_redemption_item',
					'value' => $item_id,
					'compare' => '='
				]
			]
		]);

		return ! empty( $products ) ? $products[0]->ID : false;
	}

	/**
	 * Get all available redemption items
	 */
	public function get_available_redemption_items( $user_id = null ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( ! $user_id ) {
			return [];
		}

		$xp_system = new XP_System();
		$user_xp = $xp_system->get_user_xp( $user_id );
		
		$rank_system = new Rank_System();
		$user_rank_level = $rank_system->get_user_rank_level( $user_id );
		
		$available_items = [];
		
		foreach ( $this->redemption_items as $item_id => $item ) {
			// Check if user has enough XP
			$has_enough_xp = $user_xp >= $item['cost'];
			
			// Check if user meets rank requirement
			$meets_rank = $user_rank_level >= $item['min_rank'];
			
			// Check if user has reached max usage
			$max_reached = false;
			if ( $item['max_per_user'] > 0 ) {
				$usage_count = $this->get_user_item_usage_count( $user_id, $item_id );
				$max_reached = $usage_count >= $item['max_per_user'];
			}
			
			// Check if user has previously purchased item with limited uses
			if ( $item['type'] === 'discount' && $item['max_per_user'] > 0 ) {
				$has_used = $this->user_has_used_limited_item( $user_id, $item_id );
				if ( $has_used && $max_reached ) {
					continue;
				}
			} elseif ( $item['type'] === 'cosmetic' && $item['max_per_user'] === 1 ) {
				$has_cosmetic = $this->user_has_active_cosmetic( $user_id, $item['value'] );
				if ( $has_cosmetic ) {
					continue;
				}
			}

			if ( $has_enough_xp && $meets_rank && ! $max_reached ) {
				$item['user_can_afford'] = true;
				$available_items[] = $item;
			} else {
				$item['user_can_afford'] = false;
				$item['can_afford_reason'] = [];
				
				if ( ! $has_enough_xp ) {
					$item['can_afford_reason'][] = 'insufficient_xp';
					$item['xp_short'] = $item['cost'] - $user_xp;
				}
				
				if ( ! $meets_rank ) {
					$item['can_afford_reason'][] = 'insufficient_rank';
				}
				
				if ( $max_reached ) {
					$item['can_afford_reason'][] = 'max_usage_reached';
				}
				
				$available_items[] = $item;
			}
		}
		
		return $available_items;
	}

	/**
	 * Handle redemption request
	 */
	public function handle_redemption_request() {
		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['nonce'], 'gamerz_redeem_nonce' ) ) {
			wp_die( __( 'Security check failed', 'gamerz-guild' ) );
		}

		if ( ! is_user_logged_in() ) {
			wp_die( __( 'You must be logged in to redeem items', 'gamerz-guild' ) );
		}

		$item_id = sanitize_text_field( $_POST['item_id'] );
		$user_id = get_current_user_id();

		// Validate item exists
		if ( ! $this->item_exists( $item_id ) ) {
			wp_die( __( 'Invalid redemption item', 'gamerz-guild' ) );
		}

		$item = $this->redemption_items[ $item_id ];

		// Check if user has enough XP
		$xp_system = new XP_System();
		$user_xp = $xp_system->get_user_xp( $user_id );
		
		if ( $user_xp < $item['cost'] ) {
			wp_die( __( 'Insufficient XP to redeem this item', 'gamerz-guild' ) );
		}

		// Check rank requirement
		$rank_system = new Rank_System();
		$user_rank_level = $rank_system->get_user_rank_level( $user_id );
		
		if ( $user_rank_level < $item['min_rank'] ) {
			wp_die( __( 'You don\'t meet the rank requirement for this item', 'gamerz-guild' ) );
		}

		// Check max usage
		if ( $item['max_per_user'] > 0 ) {
			$usage_count = $this->get_user_item_usage_count( $user_id, $item_id );
			if ( $usage_count >= $item['max_per_user'] ) {
				wp_die( __( 'You have reached the maximum usage limit for this item', 'gamerz-guild' ) );
			}
		}

		// Deduct XP
		$mycred = $xp_system->get_mycred();
		if ( $mycred ) {
			$mycred->add_creds(
				'xp_redemption',
				$user_id,
				-1 * $item['cost'], // Negative to deduct
				sprintf( __( 'Redeemed item: %s', 'gamerz-guild' ), $item['name'] ),
				0,
				[],
				$xp_system->log_type
			);
		}

		// Process the redemption based on item type
		$success = $this->process_redemption( $user_id, $item_id, $item );

		if ( $success ) {
			// Record redemption
			$this->record_redemption( $user_id, $item_id, $item );
			
			wp_send_json_success( [
				'message' => __( 'Successfully redeemed item!', 'gamerz-guild' ),
				'item_id' => $item_id,
				'remaining_xp' => $xp_system->get_user_xp( $user_id ),
			] );
		} else {
			wp_send_json_error( [ 
				'message' => __( 'Failed to process redemption', 'gamerz-guild' ) 
			] );
		}
	}

	/**
	 * Process the redemption based on item type
	 */
	private function process_redemption( $user_id, $item_id, $item ) {
		switch ( $item['type'] ) {
			case 'discount':
				return $this->process_discount_redemption( $user_id, $item );
				
			case 'cosmetic':
				return $this->process_cosmetic_redemption( $user_id, $item );
				
			case 'access':
				return $this->process_access_redemption( $user_id, $item );
				
			case 'digital':
				return $this->process_digital_redemption( $user_id, $item );
				
			default:
				return false;
		}
	}

	/**
	 * Process discount redemption
	 */
	private function process_discount_redemption( $user_id, $item ) {
		// Create a WooCommerce coupon
		$coupon_code = 'XP_' . strtoupper( $item['id'] ) . '_' . $user_id . '_' . time();
		$coupon = array(
			'post_title' => $coupon_code,
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_type' => 'shop_coupon',
			'post_excerpt' => $item['name'] . ' - Redeemed with XP',
		);

		$new_coupon_id = wp_insert_post( $coupon );

		if ( is_wp_error( $new_coupon_id ) ) {
			return false;
		}

		// Set coupon meta
		if ( $item['restriction'] === 'currency' ) {
			update_post_meta( $new_coupon_id, 'discount_type', 'fixed_cart' );
			update_post_meta( $new_coupon_id, 'coupon_amount', $item['value'] );
		} else {
			update_post_meta( $new_coupon_id, 'discount_type', 'percent' );
			update_post_meta( $new_coupon_id, 'coupon_amount', $item['value'] );
		}

		// Set usage restrictions
		update_post_meta( $new_coupon_id, 'customer_email', [ get_userdata( $user_id )->user_email ] );
		update_post_meta( $new_coupon_id, 'usage_limit', 1 );
		update_post_meta( $new_coupon_id, 'individual_use', 'yes' );
		update_post_meta( $new_coupon_id, 'expiry_date', date( 'Y-m-d', strtotime( '+30 days' ) ) );

		// Save coupon to user meta for tracking
		$user_coupons = get_user_meta( $user_id, '_gamerz_xp_coupons', true );
		if ( ! is_array( $user_coupons ) ) {
			$user_coupons = [];
		}
		$user_coupons[] = [
			'id' => $new_coupon_id,
			'code' => $coupon_code,
			'item_id' => $item['id'],
			'created_at' => current_time( 'mysql' ),
			'redeemed_at' => current_time( 'mysql' ),
		];
		update_user_meta( $user_id, '_gamerz_xp_coupons', $user_coupons );

		return true;
	}

	/**
	 * Process cosmetic redemption
	 */
	private function process_cosmetic_redemption( $user_id, $item ) {
		if ( $item['duration'] ) {
			$end_time = strtotime( '+' . $item['duration'] . ' days' );
		} else {
			$end_time = null; // Permanent
		}

		$user_cosmetics = get_user_meta( $user_id, '_gamerz_cosmetics', true );
		if ( ! is_array( $user_cosmetics ) ) {
			$user_cosmetics = [];
		}

		$user_cosmetics[] = [
			'type' => $item['value'],
			'item_id' => $item['id'],
			'start_time' => current_time( 'mysql' ),
			'end_time' => $end_time ? date( 'Y-m-d H:i:s', $end_time ) : null,
			'active' => true,
		];

		update_user_meta( $user_id, '_gamerz_cosmetics', $user_cosmetics );

		return true;
	}

	/**
	 * Process access redemption
	 */
	private function process_access_redemption( $user_id, $item ) {
		$end_time = strtotime( '+' . $item['duration'] . ' days' );

		$user_access = get_user_meta( $user_id, '_gamerz_access_levels', true );
		if ( ! is_array( $user_access ) ) {
			$user_access = [];
		}

		$user_access[] = [
			'type' => $item['value'],
			'item_id' => $item['id'],
			'granted_at' => current_time( 'mysql' ),
			'expires_at' => date( 'Y-m-d H:i:s', $end_time ),
			'active' => true,
		];

		update_user_meta( $user_id, '_gamerz_access_levels', $user_access );

		return true;
	}

	/**
	 * Process digital redemption
	 */
	private function process_digital_redemption( $user_id, $item ) {
		// For digital downloads, we might just record the redemption
		$downloads = get_user_meta( $user_id, '_gamerz_digital_downloads', true );
		if ( ! is_array( $downloads ) ) {
			$downloads = [];
		}

		$downloads[] = [
			'item_id' => $item['id'],
			'granted_at' => current_time( 'mysql' ),
		];

		update_user_meta( $user_id, '_gamerz_digital_downloads', $downloads );

		return true;
	}

	/**
	 * Record redemption in user history
	 */
	private function record_redemption( $user_id, $item_id, $item ) {
		$history = get_user_meta( $user_id, '_gamerz_redemption_history', true );
		if ( ! is_array( $history ) ) {
			$history = [];
		}

		$history[] = [
			'item_id' => $item_id,
			'item_name' => $item['name'],
			'cost' => $item['cost'],
			'redeemed_at' => current_time( 'mysql' ),
			'xp_after' => ( new XP_System() )->get_user_xp( $user_id ),
		];

		update_user_meta( $user_id, '_gamerz_redemption_history', $history );
	}

	/**
	 * Get user redemption history
	 */
	public function get_user_redemption_history( $user_id ) {
		return get_user_meta( $user_id, '_gamerz_redemption_history', true );
	}

	/**
	 * Get user's used count for a specific item
	 */
	private function get_user_item_usage_count( $user_id, $item_id ) {
		$history = $this->get_user_redemption_history( $user_id );
		if ( ! is_array( $history ) ) {
			return 0;
		}

		$count = 0;
		foreach ( $history as $record ) {
			if ( $record['item_id'] === $item_id ) {
				$count++;
			}
		}

		return $count;
	}

	/**
	 * Check if user has used a limited item
	 */
	private function user_has_used_limited_item( $user_id, $item_id ) {
		$history = $this->get_user_redemption_history( $user_id );
		if ( ! is_array( $history ) ) {
			return false;
		}

		foreach ( $history as $record ) {
			if ( $record['item_id'] === $item_id ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check if user has active cosmetic
	 */
	private function user_has_active_cosmetic( $user_id, $cosmetic_type ) {
		$cosmetics = get_user_meta( $user_id, '_gamerz_cosmetics', true );
		if ( ! is_array( $cosmetics ) ) {
			return false;
		}

		foreach ( $cosmetics as $cosmetic ) {
			if ( $cosmetic['type'] === $cosmetic_type && $cosmetic['active'] ) {
				// Check if expired
				if ( $cosmetic['end_time'] && strtotime( $cosmetic['end_time'] ) < time() ) {
					// Mark as inactive
					$this->deactivate_expired_cosmetics( $user_id );
					return false;
				}
				return true;
			}
		}

		return false;
	}

	/***
	 * Deactivate expired cosmetics
	 */
	public function deactivate_expired_cosmetics( $user_id ) {
		$cosmetics = get_user_meta( $user_id, '_gamerz_cosmetics', true );
		if ( ! is_array( $cosmetics ) ) {
			return;
		}

		$updated = false;
		foreach ( $cosmetics as $key => $cosmetic ) {
			if ( $cosmetic['end_time'] && strtotime( $cosmetic['end_time'] ) < time() && $cosmetic['active'] ) {
				$cosmetics[ $key ]['active'] = false;
				$updated = true;
			}
		}

		if ( $updated ) {
			update_user_meta( $user_id, '_gamerz_cosmetics', $cosmetics );
		}
	}

	/**
	 * Apply user cosmetics in frontend
	 */
	public function apply_user_cosmetics() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user_id = get_current_user_id();
		$cosmetics = get_user_meta( $user_id, '_gamerz_cosmetics', true );
		if ( ! is_array( $cosmetics ) ) {
			return;
		}

		$css = '';

		foreach ( $cosmetics as $cosmetic ) {
			if ( ! $cosmetic['active'] ) {
				continue;
			}

			// Check if expired
			if ( $cosmetic['end_time'] && strtotime( $cosmetic['end_time'] ) < time() ) {
				continue;
			}

			switch ( $cosmetic['type'] ) {
				case 'glow_effect':
					$css .= '
					.display-name, .author-name, .bbp-author-name, .activity-header a, .user-nickname {
						text-shadow: 0 0 8px #00ffff, 0 0 12px #00ffff;
						animation: xp-glow-pulse 2s infinite;
					}
					@keyframes xp-glow-pulse {
						0% { text-shadow: 0 0 8px #00ffff, 0 0 12px #00ffff; }
						50% { text-shadow: 0 0 16px #00ffff, 0 0 20px #00ffff; }
						100% { text-shadow: 0 0 8px #00ffff, 0 0 12px #00ffff; }
					}
					';
					break;
					
				case 'animated_frame':
					$css .= '
					.avatar, .profile-avatar, .bbp-avatar {
						border: 3px solid #00ffff;
						border-radius: 10px;
						box-shadow: 0 0 15px #00ffff, 0 0 30px #ff00ff;
						animation: xp-frame-pulse 2s infinite;
					}
					@keyframes xp-frame-pulse {
						0% { box-shadow: 0 0 15px #00ffff, 0 0 30px #ff00ff; }
						50% { box-shadow: 0 0 25px #00ffff, 0 0 40px #ff00ff; }
						100% { box-shadow: 0 0 15px #00ffff, 0 0 30px #ff00ff; }
					}
					';
					break;
			}
		}

		if ( $css ) {
			echo '<style id="gamerz-user-cosmetics">' . $css . '</style>';
		}
	}

	/**
	 * Check if an item exists
	 */
	public function item_exists( $item_id ) {
		return isset( $this->redemption_items[ $item_id ] );
	}

	/**
	 * Get a specific redemption item
	 */
	public function get_redemption_item( $item_id ) {
		return isset( $this->redemption_items[ $item_id ] ) ? $this->redemption_items[ $item_id ] : null;
	}

	/**
	 * Add a custom redemption item
	 */
	public function add_redemption_item( $item_id, $item_data ) {
		$required_fields = [ 'name', 'description', 'cost', 'type', 'min_rank' ];
		foreach ( $required_fields as $field ) {
			if ( ! isset( $item_data[ $field ] ) ) {
				return false;
			}
		}

		$defaults = [
			'id' => $item_id,
			'max_per_user' => 0,
			'duration' => null,
			'value' => 1,
			'restriction' => null,
		];

		$this->redemption_items[ $item_id ] = wp_parse_args( $item_data, $defaults );
		return true;
	}

	/**
	 * Get available discounts for user
	 */
	public function get_available_discounts( $user_id ) {
		$discounts = [];
		$user_coupons = get_user_meta( $user_id, '_gamerz_xp_coupons', true );
		
		if ( is_array( $user_coupons ) ) {
			foreach ( $user_coupons as $coupon ) {
				if ( isset( $coupon['code'] ) ) {
					$wc_coupon = new \WC_Coupon( $coupon['code'] );
					if ( $wc_coupon->get_id() && ! $wc_coupon->get_date_expires() || $wc_coupon->get_date_expires()->getTimestamp() > time() ) {
						$discounts[] = $wc_coupon;
					}
				}
			}
		}

		return $discounts;
	}
}