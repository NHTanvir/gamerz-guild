<?php
/**
 * All public facing functions
 */
namespace Codexpert\Gamerz_Guild\App;
use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Helper;
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Front
 * @author Codexpert <hi@tanvir.io>
 */
class Front extends Base {

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
	}

	public function head() {
	
//     $mycred = \mycred();
    
//     // Additional safety check
//     if ( $mycred && is_object( $mycred ) ) {
//         // Award 50 XP for creating a guild
//         $xp_amount = apply_filters( 'gamerz_guild_creation_xp', 50 );
//         $reference = 'guild_creation';
//         $entry = apply_filters( 
//             'gamerz_guild_creation_log_entry', 
//             sprintf( __( 'Created guild "%s"', 'gamerz-guild' ), $args['title'] ), 
//             7952, 
//             $args 
//         );
//         		$user_id = get_current_user_id();
//         $mycred->add_creds( 
//             $reference, 
//             $user_id, // creator_id
//             $xp_amount, 
//             $entry, 
//             7952, // ref_id
//             [], // data
//             'gamerz_xp' // point type
//         );
// }
	}
	
	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		$min = defined( 'ITS_DEBUG' ) && ITS_DEBUG ? '' : '.min';

		wp_enqueue_style( $this->slug, plugins_url( "/assets/css/front{$min}.css", ITS ), '', $this->version, 'all' );

		wp_enqueue_script( $this->slug, plugins_url( "/assets/js/front{$min}.js", ITS ), [ 'jquery' ], $this->version, true );
		
		$localized = [
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			'_wpnonce'	=> wp_create_nonce(),
		];
		wp_localize_script( $this->slug, 'ITS', apply_filters( "{$this->slug}-localized", $localized ) );
	}

	public function modal() {
		echo '
		<div id="gamerz-guild-modal" style="display: none">
			<img id="gamerz-guild-modal-loader" src="' . esc_attr( ITS_ASSET . '/img/loader.gif' ) . '" />
		</div>';
	}
}