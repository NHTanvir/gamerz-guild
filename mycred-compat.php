<?php
/**
 * Ensure myCRED point type is set up for Gamerz Guild
 */

// Make sure we're in WordPress context
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Function to ensure Gamerz XP point type is available in myCRED
function ensure_gamerz_xp_point_type() {
    // Only run if myCRED is active
    if ( ! class_exists( 'myCRED' ) ) {
        return;
    }
    
    // Check if the 'gamerz_xp' point type exists
    $point_types = mycred_get_types();
    
    // If 'gamerz_xp' doesn't exist, we need to create it or use the default
    if ( ! array_key_exists( 'gamerz_xp', $point_types ) ) {
        // If not found, we'll use the default point type
        // Get the default point type slug
        $default_type = mycred_get_default_type();
        
        // Update the XP_System class to use the default type if gamerz_xp doesn't exist
        // For now, let's make sure a default point type is available
        if ( empty( $default_type ) ) {
            error_log( "Gamerz Guild: No myCRED point type available for XP system" );
        } else {
            error_log( "Gamerz Guild: Using default myCRED point type: " . $default_type );
        }
    }
}

// Hook this to run after plugins are loaded
add_action( 'plugins_loaded', 'ensure_gamerz_xp_point_type' );

// Also check for common issue where myCRED hooks might not fire
function check_mycred_hooks() {
    if ( class_exists( 'myCRED' ) ) {
        // Make sure the hooks are properly registered
        add_action( 'init', function() {
            // Ensure the Gamerz Guild XP hooks are active
            if ( has_action( 'wp_login', [ new \Codexpert\Gamerz_Guild\Classes\XP_System(), 'award_daily_login' ] ) === false ) {
                add_action( 'wp_login', [ new \Codexpert\Gamerz_Guild\Classes\XP_System(), 'award_daily_login' ], 10, 2 );
            }
        }, 20 ); // Run after the plugin's own hooks are set up
    }
}

add_action( 'plugins_loaded', 'check_mycred_hooks' );

// Add a function to test XP awarding
function test_xp_awarding( $user_id ) {
    if ( class_exists( 'myCRED' ) ) {
        $mycred = mycred();
        $xp_system = new \Codexpert\Gamerz_Guild\Classes\XP_System();
        
        // Test awarding a small amount of XP
        $mycred->add_creds(
            'test_award',
            $user_id,
            10,
            'Test XP award to verify system is working',
            0,
            array(),
            $xp_system->log_type  // This will use 'gamerz_xp' or fallback
        );
    }
}