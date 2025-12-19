<?php
/**
 * Ensure myCRED point type is set up for Gamerz Guild
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ensure_gamerz_xp_point_type() {
    if ( ! class_exists( 'myCRED' ) ) {
        return;
    }

    $point_types = mycred_get_types();

    if ( ! array_key_exists( 'gamerz_xp', $point_types ) ) {

        $default_type = mycred_get_default_type();
    
        if ( empty( $default_type ) ) {
            error_log( "Gamerz Guild: No myCRED point type available for XP system" );
        } else {
            error_log( "Gamerz Guild: Using default myCRED point type: " . $default_type );
        }
    }
}


add_action( 'plugins_loaded', 'ensure_gamerz_xp_point_type' );

function check_mycred_hooks() {
    if ( class_exists( 'myCRED' ) ) {
        add_action( 'init', function() {
            // Ensure the Gamerz Guild XP hooks are active
            if ( has_action( 'wp_login', [ new \Codexpert\Gamerz_Guild\Classes\XP_System(), 'award_daily_login' ] ) === false ) {
                add_action( 'wp_login', [ new \Codexpert\Gamerz_Guild\Classes\XP_System(), 'award_daily_login' ], 10, 2 );
            }
        }, 20 ); 
    }
}

add_action( 'plugins_loaded', 'check_mycred_hooks' );

function test_xp_awarding( $user_id ) {
    if ( class_exists( 'myCRED' ) ) {
        $mycred = mycred();
        $xp_system = new \Codexpert\Gamerz_Guild\Classes\XP_System();
        
        $mycred->add_creds(
            'test_award',
            $user_id,
            10,
            'Test XP award to verify system is working',
            0,
            array(),
            $xp_system->log_type  
        );
    }
}