<?php
/**
 * Set up Discord integration settings for Gamerz Guild plugin
 */

// Make sure we're in WordPress context
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Function to set up the Discord integration settings
function setup_gamerz_discord_integration() {
    // Set the Discord webhook URL
    update_option( 'gamerz_discord_webhook_url', 'https://discord.com/api/webhooks/1443615355024179221/u5J7zMlnZL8HuK5Ux57_bz9GkjWuTWJ5oso3wuzRdPZlEJL3lktLw3rM4y_SdGfiw_3Z' );
    
    // Set the Discord bot token
    update_option( 'gamerz_discord_bot_token', 'MTQ0MzYxNzE5MTQyMDIzNTk3MA.Gak63V.mOqcuCIxwl1Z2u0-u1cZLmF3qthcZJkqzXKj5k' );
    
    // Set the Discord guild ID
    update_option( 'gamerz_discord_guild_id', '1045083738754789386' );
    
    // Set up the role mappings based on rank levels
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
    
    // Update the role mappings option
    update_option( 'gamerz_discord_role_mapping', $role_mappings );
    
    return true;
}

// Hook this function to run during plugin initialization
add_action( 'init', 'setup_gamerz_discord_integration' );