<?php
/**
 * Verification script for Gamerz Guild Discord Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function verify_gamerz_discord_setup() {
    $webhook_url  = get_option( 'gamerz_discord_webhook_url', '' );
    $bot_token    = get_option( 'gamerz_discord_bot_token', '' );
    $guild_id     = get_option( 'gamerz_discord_guild_id', '' );
    $role_mapping = get_option( 'gamerz_discord_role_mapping', [] );
    
    $verification_results = [
        'webhook_url_set'       =>  ! empty( $webhook_url ),
        'bot_token_set'         =>  ! empty( $bot_token ),
        'guild_id_set'          =>  ! empty( $guild_id ),
        'role_mapping_set'      =>  ! empty( $role_mapping ),
        'specific_roles_mapped' => false,
        'webhook_url'           => $webhook_url,
        'bot_token_exists'      =>  ! empty( $bot_token ) , 
        'guild_id'              => $guild_id,
        'role_mapping_count'    => count( $role_mapping )
    ];
    
    // Check if the specific role mappings we set up are present
    if ( ! empty( $role_mapping ) ) {
        $target_roles = [
            '1443626808929943595', // Rookie Scrub
            '1443626957228216340', // Casual Scrub
            '1443627075520167957', // Elite Scrub
            '1443627161578766461', // Legendary Scrub
            '1443627282588762275', // Mythic Scrub
        ];
        
        $found_roles = 0;
        foreach ( $role_mapping as $role_data ) {
            if ( in_array( $role_data['role_id'], $target_roles ) ) {
                $found_roles++;
            }
        }
        
        $verification_results['specific_roles_mapped'] = $found_roles >= 3; 
    }
    
    return $verification_results;
}


function display_gamerz_discord_verification() {
    $results = verify_gamerz_discord_setup();
    
    echo '<div class="notice notice-info is-dismissible" style="padding: 15px; border-left: 4px solid #0073aa;">';
    echo '<h3 style="margin-top: 0;">Gamerz Guild Discord Integration Verification</h3>';
    
    echo '<ul style="list-style-type: none; padding-left: 0;">';
    echo '<li style="margin: 5px 0;"><strong>✓ Webhook URL:</strong> ' . ( $results['webhook_url_set'] ? 'SET' : 'NOT SET' ) . '</li>';
    echo '<li style="margin: 5px 0;"><strong>✓ Bot Token:</strong> ' . ( $results['bot_token_set'] ? 'SET' : 'NOT SET' ) . '</li>';
    echo '<li style="margin: 5px 0;"><strong>✓ Guild ID:</strong> ' . ( $results['guild_id_set'] ? $results['guild_id'] : 'NOT SET' ) . '</li>';
    echo '<li style="margin: 5px 0;"><strong>✓ Role Mapping:</strong> ' . ( $results['role_mapping_set'] ? 'SET (' . $results['role_mapping_count'] . ' roles)' : 'NOT SET' ) . '</li>';
    echo '<li style="margin: 5px 0;"><strong>✓ Specific Roles:</strong> ' . ( $results['specific_roles_mapped'] ? 'VERIFIED' : 'NOT VERIFIED' ) . '</li>';
    echo '</ul>';
    
    if ( $results['webhook_url_set'] && $results['bot_token_set'] && $results['guild_id_set'] && $results['specific_roles_mapped'] ) {
        echo '<p style="color: #28a745; font-weight: bold;">✅ Discord integration is fully configured and ready to use!</p>';
    } else {
        echo '<p style="color: #dc3545; font-weight: bold;">❌ Discord integration needs attention - some settings are missing.</p>';
    }
    
    echo '</div>';
}

add_action('admin_notices', function() {
    $screen = get_current_screen();
    if ( $screen->id === 'plugins' || strpos( $screen->id, 'gamerz-guild' ) !== false ) {
        display_gamerz_discord_verification();
    }
});