<?php
/**
 * All Shortcode related functions
 */
namespace Codexpert\Gamerz_Guild\App;
use Codexpert\Plugin\Base;
use Codexpert\Gamerz_Guild\Classes\Challenges as Challenges_Class;
use Codexpert\Gamerz_Guild\Classes\Leaderboard as Leaderboard_Class;
use Codexpert\Gamerz_Guild\Classes\Rank_System;
use Codexpert\Gamerz_Guild\Classes\Guild;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @package Plugin
 * @subpackage Shortcode
 * @author Codexpert <hi@tanvir.io>
 */
class Shortcode extends Base {

    public $plugin;
    public $slug;
    public $name;
    public $version;

    /**
     * Constructor function
     */
    public function __construct( $plugin ) {
        $this->plugin   = $plugin;
        $this->slug     = $this->plugin['TextDomain'];
        $this->name     = $this->plugin['Name'];
        $this->version  = $this->plugin['Version'];
    }

    /**
     * Render weekly challenges shortcode
     */
    public function render_weekly_challenges_shortcode( $atts ) {
        $challenges_class = new Challenges_Class();
        $atts = shortcode_atts( [
            'title' => 'Weekly Challenges',
        ], $atts );


        $challenges = $challenges_class->get_current_challenges();
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
                                    <?php if ( $challenges_class->has_user_completed_challenge( $user_id, $challenge['id'] ) ) : ?>
                                        <span class="gamerz-challenge-completed-badge">✓ Completed</span>
                                    <?php endif; ?>
                                </h4>
                                <span class="gamerz-challenge-xp-reward">+<?php echo esc_html( $challenge['reward_xp'] ); ?> XP</span>
                            </div>

                            <div class="gamerz-challenge-description">
                                <?php echo wp_kses_post( wpautop( $challenge['description'] ) ); ?>
                            </div>

                            <div class="gamerz-challenge-actions">
                                <?php if ( $challenges_class->has_user_completed_challenge( $user_id, $challenge['id'] ) ) : ?>
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
        $challenges_class = new Challenges_Class();
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
     * Render global leaderboard shortcode
     */
    public function render_leaderboard_shortcode( $atts ) {
        $leaderboard_class = new Leaderboard_Class();
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
                    $leaderboard = $leaderboard_class->get_guild_leaderboard( $atts['guild_id'], $atts['limit'] );
                    $guild = get_post( $atts['guild_id'] );
                    if ( $guild ) {
                        $title = sprintf( __( '%s Leaderboard', 'gamerz-guild' ), $guild->post_title );
                    }
                }
                break;

            default:
                $leaderboard = $leaderboard_class->get_global_leaderboard( $atts['limit'] );
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
        $leaderboard_class = new Leaderboard_Class();
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
     * Render guild management shortcode
     */
    public function render_guild_management_shortcode( $atts ) {
        $atts = shortcode_atts( [
            'title' => 'Guild Management',
            'show_list' => 'true',
            'show_create' => 'true',
        ], $atts );

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            return __( 'Please log in to manage guilds.', 'gamerz-guild' );
        }

        $guild_class = new Guild();
        $user_guilds = $guild_class->get_user_guilds( $user_id );
        $current_guild = ! empty( $user_guilds ) ? $user_guilds[0] : null;

        ob_start();
        ?>
        <div class="gamerz-guild-management-container">
            <h3 class="gamerz-guild-management-title"><?php echo esc_html( $atts['title'] ); ?></h3>

            <?php if ( $current_guild ) : ?>
                <!-- Current Guild Information -->
                <div class="gamerz-current-guild-section">
                    <h4>Current Guild: <?php echo esc_html( $current_guild->post_title ); ?></h4>
                    <div class="gamerz-guild-details">
                        <p><strong>Description:</strong> <?php echo esc_html( $current_guild->post_content ?: 'No description' ); ?></p>
                        <p><strong>Tagline:</strong> <?php echo esc_html( get_post_meta( $current_guild->ID, '_guild_tagline', true ) ?: 'No tagline' ); ?></p>
                        <p><strong>Members:</strong> <?php echo count( $guild_class->get_members( $current_guild->ID ) ); ?>/<?php echo get_post_meta( $current_guild->ID, '_guild_max_members', true ) ?: 20; ?></p>
                        <p><strong>Status:</strong> <?php echo esc_html( get_post_meta( $current_guild->ID, '_guild_status', true ) ?: 'active' ); ?></p>
                    </div>

                    <?php
                    $user_role = $guild_class->get_user_role( $current_guild->ID, $user_id );
                    $can_manage = $user_role === 'leader';
                    ?>

                    <?php if ( $can_manage ) : ?>
                        <div class="gamerz-guild-admin-actions">
                            <h5>Guild Administration</h5>
                            <button class="gamerz-guild-edit-btn" data-guild-id="<?php echo $current_guild->ID; ?>">
                                <span class="dashicons dashicons-edit"></span> Edit Guild
                            </button>
                            <button class="gamerz-guild-member-management-btn" data-guild-id="<?php echo $current_guild->ID; ?>">
                                <span class="dashicons dashicons-groups"></span> Manage Members
                            </button>
                        </div>
                    <?php endif; ?>

                    <div class="gamerz-guild-member-actions">
                        <?php if ( $user_role === 'leader' && count( $guild_class->get_members( $current_guild->ID ) ) > 1 ) : ?>
                            <p>Transfer leadership before leaving.</p>
                        <?php else : ?>
                            <button class="gamerz-guild-leave-btn" data-guild-id="<?php echo $current_guild->ID; ?>" data-nonce="<?php echo wp_create_nonce( 'guild_leave_nonce' ); ?>">
                                <span class="dashicons dashicons-exit"></span> Leave Guild
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else : ?>
                <!-- Guild Options -->
                <div class="gamerz-guild-options">
                    <?php if ( $atts['show_create'] === 'true' ) : ?>
                        <div class="gamerz-create-guild-section">
                            <h4>Create New Guild</h4>
                            <form id="gamerz-create-guild-form">
                                <p>
                                    <label for="guild_title">Guild Name *</label>
                                    <input type="text" id="guild_title" name="guild_title" required />
                                </p>
                                <p>
                                    <label for="guild_description">Description</label>
                                    <textarea id="guild_description" name="guild_description"></textarea>
                                </p>
                                <p>
                                    <label for="guild_tagline">Tagline</label>
                                    <input type="text" id="guild_tagline" name="guild_tagline" />
                                </p>
                                <p>
                                    <label for="guild_max_members">Max Members (5-100)</label>
                                    <input type="number" id="guild_max_members" name="guild_max_members" min="5" max="100" value="20" />
                                </p>
                                <button type="submit">
                                    <span class="dashicons dashicons-plus"></span> Create Guild
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    <?php if ( $atts['show_list'] === 'true' ) : ?>
                        <div class="gamerz-guild-list-section">
                            <h4>Available Guilds</h4>
                            <div id="gamerz-guild-list">
                                <?php
                                $all_guilds = get_posts( [
                                    'post_type' => 'guild',
                                    'post_status' => 'publish',
                                    'posts_per_page' => 10,
                                ] );

                                if ( ! empty( $all_guilds ) ) :
                                ?>
                                    <table class="gamerz-guild-list-table">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Members</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ( $all_guilds as $guild ) : ?>
                                                <?php
                                                $members = $guild_class->get_members( $guild->ID );
                                                $is_member = in_array( $user_id, $members );
                                                ?>
                                                <tr>
                                                    <td><?php echo esc_html( $guild->post_title ); ?></td>
                                                    <td><?php echo wp_trim_words( wp_strip_all_tags( $guild->post_content ), 10 ); ?></td>
                                                    <td><?php echo count( $members ); ?>/<?php echo get_post_meta( $guild->ID, '_guild_max_members', true ) ?: 20; ?></td>
                                                    <td><?php echo esc_html( get_post_meta( $guild->ID, '_guild_status', true ) ?: 'active' ); ?></td>
                                                    <td>
                                                        <?php if ( ! $is_member && count( $members ) < ( get_post_meta( $guild->ID, '_guild_max_members', true ) ?: 20 ) ) : ?>
                                                            <button class="gamerz-guild-join-btn" data-guild-id="<?php echo $guild->ID; ?>" data-nonce="<?php echo wp_create_nonce( 'guild_join_nonce' ); ?>">
                                                                Join
                                                            </button>
                                                        <?php elseif ( $is_member ) : ?>
                                                            <span class="gamerz-already-member">Already Member</span>
                                                        <?php else : ?>
                                                            <span class="gamerz-guild-full">Guild Full</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else : ?>
                                    <p>No guilds available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Modals -->
            <div id="gamerz-guild-member-management-modal" class="gamerz-modal" style="display: none;">
                <div class="gamerz-modal-content">
                    <span class="gamerz-modal-close">&times;</span>
                    <h3>Manage Guild Members</h3>
                    <div id="gamerz-guild-members-list"></div>
                </div>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Guild Join Button
            $(document).on('click', '.gamerz-guild-join-btn', function(e) {
                e.preventDefault();
                var guildId = $(this).data('guild-id');
                var nonce = $(this).data('nonce');

                $.post(ajaxurl, {
                    action: 'guild_join',
                    guild_id: guildId,
                    nonce: nonce
                }, function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload(); // Refresh to show new guild status
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                }).fail(function() {
                    alert('Connection error. Please try again.');
                });
            });

            // Guild Leave Button
            $(document).on('click', '.gamerz-guild-leave-btn', function(e) {
                e.preventDefault();
                var guildId = $(this).data('guild-id');
                var nonce = $(this).data('nonce');

                if (confirm('Are you sure you want to leave this guild?')) {
                    $.post(ajaxurl, {
                        action: 'guild_leave',
                        guild_id: guildId,
                        nonce: nonce
                    }, function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            location.reload(); // Refresh to show updated status
                        } else {
                            alert('Error: ' + response.data.message);
                        }
                    }).fail(function() {
                        alert('Connection error. Please try again.');
                    });
                }
            });

            // Guild Create Form
            $('#gamerz-create-guild-form').on('submit', function(e) {
                e.preventDefault();

                var formData = {
                    action: 'guild_create',
                    title: $('#guild_title').val(),
                    description: $('#guild_description').val(),
                    tagline: $('#guild_tagline').val(),
                    max_members: $('#guild_max_members').val(),
                    nonce: '<?php echo wp_create_nonce( 'guild_create_nonce' ); ?>'
                };

                if (!formData.title) {
                    alert('Guild name is required.');
                    return;
                }

                $.post(ajaxurl, formData, function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload(); // Refresh to show new guild
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                }).fail(function() {
                    alert('Connection error. Please try again.');
                });
            });

            // Member Management Button
            $(document).on('click', '.gamerz-guild-member-management-btn', function() {
                var guildId = $(this).data('guild-id');
                loadGuildMembers(guildId);
                $('#gamerz-guild-member-management-modal').show();
            });

            // Modal Close
            $(document).on('click', '.gamerz-modal-close', function() {
                $('#gamerz-guild-member-management-modal').hide();
            });

            $(document).on('click', function(e) {
                if (e.target.id === 'gamerz-guild-member-management-modal') {
                    $('#gamerz-guild-member-management-modal').hide();
                }
            });

            // Load guild members function
            function loadGuildMembers(guildId) {
                $.post(ajaxurl, {
                    action: 'get_guild_members',
                    guild_id: guildId,
                    nonce: '<?php echo wp_create_nonce( 'guild_members_nonce' ); ?>'
                }, function(response) {
                    if (response.success) {
                        var membersHtml = '<ul class="gamerz-guild-members-list">';
                        $.each(response.data, function(index, member) {
                            membersHtml += '<li class="gamerz-guild-member-item">' +
                                          '<strong>' + member.display_name + '</strong> ' +
                                          '<span class="gamerz-member-role">(' + member.role + ')</span>';

                            if (member.role !== 'leader') {
                                membersHtml += ' <button class="gamerz-kick-member-btn button button-secondary button-small" data-guild-id="' + guildId + '" data-member-id="' + member.id + '" data-nonce="<?php echo wp_create_nonce( 'guild_kick_nonce' ); ?>">Kick</button>';

                                if (member.role === 'member') {
                                    membersHtml += ' <button class="gamerz-promote-member-btn button button-primary button-small" data-guild-id="' + guildId + '" data-member-id="' + member.id + '" data-nonce="<?php echo wp_create_nonce( 'guild_promote_nonce' ); ?>">Promote</button>';
                                } else if (member.role === 'officer') {
                                    membersHtml += ' <button class="gamerz-demote-member-btn button button-secondary button-small" data-guild-id="' + guildId + '" data-member-id="' + member.id + '" data-nonce="<?php echo wp_create_nonce( 'guild_demote_nonce' ); ?>">Demote</button>';
                                }
                            }
                            membersHtml += '</li>';
                        });
                        membersHtml += '</ul>';
                        $('#gamerz-guild-members-list').html(membersHtml);
                    } else {
                        $('#gamerz-guild-members-list').html('<p class="error">Error loading members: ' + response.data.message + '</p>');
                    }
                }).fail(function() {
                    $('#gamerz-guild-members-list').html('<p class="error">Connection error. Please try again.</p>');
                });
            }

            // Member management actions
            $(document).on('click', '.gamerz-kick-member-btn', function() {
                var guildId = $(this).data('guild-id');
                var memberId = $(this).data('member-id');
                var nonce = $(this).data('nonce');

                if (confirm('Are you sure you want to kick this member?')) {
                    $.post(ajaxurl, {
                        action: 'guild_kick_member',
                        guild_id: guildId,
                        member_id: memberId,
                        nonce: nonce
                    }, function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            loadGuildMembers(guildId);
                        } else {
                            alert('Error: ' + response.data.message);
                        }
                    }).fail(function() {
                        alert('Connection error. Please try again.');
                    });
                }
            });

            $(document).on('click', '.gamerz-promote-member-btn', function() {
                var guildId = $(this).data('guild-id');
                var memberId = $(this).data('member-id');
                var nonce = $(this).data('nonce');

                $.post(ajaxurl, {
                    action: 'guild_promote_member',
                    guild_id: guildId,
                    member_id: memberId,
                    nonce: nonce
                }, function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        loadGuildMembers(guildId);
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                }).fail(function() {
                    alert('Connection error. Please try again.');
                });
            });

            $(document).on('click', '.gamerz-demote-member-btn', function() {
                var guildId = $(this).data('guild-id');
                var memberId = $(this).data('member-id');
                var nonce = $(this).data('nonce');

                $.post(ajaxurl, {
                    action: 'guild_demote_member',
                    guild_id: guildId,
                    member_id: memberId,
                    nonce: nonce
                }, function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        loadGuildMembers(guildId);
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                }).fail(function() {
                    alert('Connection error. Please try again.');
                });
            });
        });
        </script>

        <style>
        .gamerz-guild-management-container {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 25px;
            margin: 20px 0;
            clear: both;
            max-width: 100%;
            box-sizing: border-box;
        }

        .gamerz-guild-management-container *,
        .gamerz-guild-management-container *:before,
        .gamerz-guild-management-container *:after {
            box-sizing: inherit;
        }
        .gamerz-guild-management-title {
            text-align: center;
            color: #333;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .gamerz-guild-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        #gamerz-create-guild-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        #gamerz-create-guild-form p {
            margin-bottom: 15px;
        }
        #gamerz-create-guild-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        #gamerz-create-guild-form input,
        #gamerz-create-guild-form textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        #gamerz-create-guild-form button {
            background: #0073aa;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        #gamerz-create-guild-form button:hover {
            background: #005a87;
        }
        .gamerz-guild-list-table {
            width: 100%;
            border-collapse: collapse;
        }
        .gamerz-guild-list-table th,
        .gamerz-guild-list-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .gamerz-guild-list-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .gamerz-guild-join-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .gamerz-guild-leave-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .gamerz-already-member,
        .gamerz-guild-full {
            color: #666;
            font-style: italic;
        }
        .gamerz-current-guild-section {
            background: #f0f8ff;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .gamerz-guild-admin-actions {
            margin: 15px 0;
            padding: 15px;
            background: #e7f3ff;
            border-radius: 5px;
        }
        .gamerz-guild-member-actions {
            margin-top: 15px;
            text-align: right;
        }
        .gamerz-modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .gamerz-modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
            position: relative;
        }
        .gamerz-modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 10px;
        }
        .gamerz-modal-close:hover {
            color: #000;
        }

        /* Theme compatibility improvements */
        .gamerz-guild-management-container input[type="text"],
        .gamerz-guild-management-container input[type="number"],
        .gamerz-guild-management-container textarea {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        .gamerz-guild-management-container button,
        .gamerz-guild-management-container input[type="submit"],
        .gamerz-guild-management-container input[type="button"] {
            font-size: 14px;
            line-height: 1.4;
            padding: 6px 12px;
            border: 1px solid transparent;
        }

        .gamerz-guild-management-container table {
            width: 100%;
            max-width: 100%;
            margin: 10px 0;
        }

        .gamerz-guild-management-container table th,
        .gamerz-guild-management-container table td {
            word-break: break-word;
            padding: 8px;
        }

        .gamerz-guild-management-container ul,
        .gamerz-guild-management-container ol {
            padding-left: 20px;
        }

        /* Clean up potential conflicts with theme styles */
        .gamerz-guild-management-container h1,
        .gamerz-guild-management-container h2,
        .gamerz-guild-management-container h3,
        .gamerz-guild-management-container h4,
        .gamerz-guild-management-container h5,
        .gamerz-guild-management-container h6 {
            margin-top: 0;
            margin-bottom: 15px;
        }

        .gamerz-guild-management-container p {
            margin: 10px 0;
        }

        .gamerz-guild-management-container img {
            max-width: 100%;
            height: auto;
        }
        </style>
        <?php

        return ob_get_clean();
    }

    public function my_shortcode() {
        return __( 'My Shortcode', 'gamerz-guild' );
    }
}