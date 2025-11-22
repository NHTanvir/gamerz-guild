# Gamerz Guild Plugin - Complete Documentation

## Overview
The Gamerz Guild plugin implements a comprehensive gamification system for your gaming community website. It integrates with myCred, BuddyPress, bbPress, and The Events Calendar to provide XP earning, rank progression, guild management, and badge achievements across your entire platform.

## Installation Requirements

Before installing the Gamerz Guild plugin, ensure you have the following:

- **WordPress 5.0+**
- **myCred Plugin** (required for XP system) - Version 2.9.0 or higher
- **BuddyPress** (for guild functionality) - Version 10.0 or higher
- **bbPress** (for forum integration) - Version 2.6 or higher
- **The Events Calendar** (for event integration) - Version 6.0 or higher (optional)
- **WooCommerce** (for redemption system) - Version 7.0 or higher (optional)

## Installation

1. Upload the `gamerz-guild` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Ensure all required plugins are activated before using Gamerz Guild

## Configuration

### 1. Initial Setup
After activation, navigate to **Gamerz Guild → Settings** to configure:

#### Discord Integration (Optional)
- **Webhook URL**: For sending announcements to your Discord server
- **Bot Token**: For assigning Discord roles based on ranks (requires bot with Manage Roles permission)
- **Guild ID**: Your Discord server ID
- **Role Mapping**: Map Gamerz Guild ranks to Discord roles

### 2. myCred Configuration
Set up your XP point type and earning hooks in myCred:

1. Go to **myCRED → Settings → Point Types**
2. Create or select a point type for "Gamerz XP" (or similar)
3. Configure earning hooks for the actions you want to reward

### 3. Rank & XP Values
The system uses these default XP values (can be filtered):

Daily Actions:
- Daily login: 5 XP
- Daily login streaks: Bonus rewards for 7, 14, 30 days

Social Actions:
- Forum reply: 5 XP
- Forum topic: 8 XP
- Like/reaction: 1 XP (capped daily)
- Add BuddyPress friend: 2 XP

Creative Actions:
- Content submission: 20 XP
- Guide post: 15 XP

Competitive Actions:
- Event participation: 15 XP
- Event victory: 50 XP
- Guild activity: 5-50 XP

Leadership Actions:
- Guild creation: 50 XP
- Mentoring: 30 XP

## Core Features

### 1. Guild Management System
Users can create, join, and manage guilds with these features:
- Create new guilds (requires 50 XP)
- Join existing guilds
- Leave guilds
- Guild member management
- Guild activity feed
- Guild forums (integrated with existing bbPress)

#### XP Integration with Guilds:
- Creating a guild: 50 XP
- Joining a guild: 10 XP
- Guild-specific activities: Variable XP

### 2. Rank Progression System
Members advance through 15 levels based on XP earned:

1. **Scrubling** (0 XP) - Entry level
2. **Scrub Recruit** (50 XP) - Custom avatar access
3. **Scrub Scout** (100 XP) - Create topics
4. **Scrub Soldier** (200 XP) - Forum signatures
5. **Scrub Strategist** (300 XP) - Host events
6. **Scrub Captain** (450 XP) - Moderate content
7. **Scrub Champion** (600 XP) - Custom banners, 5% merch discount
8. **Guild Officer** (800 XP) - Start polls, special invites
9. **Scrub Sage** (1100 XP) - Mentor status, secret forums
10. **Scrub Warlord** (1400 XP) - Custom titles, priority support
11. **Meme Master** (1800 XP) - Custom emojis, trophy case
12. **Scrub Overlord** (2300 XP) - Golden commendations, free merch
13. **Nova Scrub** (2900 XP) - Animated avatars, beta access
14. **Scrub Prime** (3600 XP) - Community Hero badge, 10% discount
15. **Legendary Scrub** (4500 XP) - All customizations, personal emoji

### 3. Badge Achievement System
The system includes 20+ auto and manual badges:

#### Engagement & Social:
- Forum Newbie: First forum post
- Social Butterfly: 5 friends or 20 likes
- Daily Grinder: 30-day login streak
- Helpful Scrub: Consistently helpful

#### Content & Creative:
- Content Creator: First content submission
- Guide Guru: 3 guides posted
- Meme Master: Popular content
- Bug Squisher: Issues reported

#### Event & Competitive:
- Squad Up: First team-up
- Event Enthusiast: 5 events attended
- Tournament Champion: Event winner
- Party Starter: Event host

#### Streaming & Media:
- Streamer: First stream connected
- Clip Champ: 10 clips shared
- Streamer of the Month: Featured

#### Community:
- Recruiter: 3 members to rank 2+
- Mentor: Newbie mentoring
- Peacemaker: Conflict resolution
- Good Samaritan: Kindness
- OG Member: Founding member (manual)

### 4. XP Redemption System

#### Real-World Rewards:
- $5 discount (1000 XP)
- $10 discount (2000 XP) 
- 10% off merch (1500 XP)

#### Digital Rewards:
- Custom avatar frame (500 XP)
- Username glow (200 XP) 
- Custom forum title (300 XP)

#### Access Rewards:
- VIP Discord access (1000 XP)
- Ad-free browsing (750 XP)

#### Digital Content:
- Wallpaper packs (500 XP)
- Steam gift cards (5000 XP)

### 5. Leaderboards
- Global XP leaderboard
- Guild-specific leaderboards
- Time-based (weekly/monthly) leaderboards
- Category leaderboards (forum, event, etc.)

### 6. Weekly Challenges
The system runs 3 weekly challenges:
- Social challenge (50 XP reward)
- Creative challenge (100 XP reward) 
- Competitive challenge (100 XP reward)

Challenges are announced Monday and reset Sunday.

## Shortcodes

### [gamerz_leaderboard]
Display leaderboards throughout your site:
```
[gamerz_leaderboard type="global" limit="10" title="Top Scrubs"]
[gamerz_leaderboard type="guild" guild_id="123" title="Guild Leaders"]
```

### [gamerz_xp_progress]
Show user's progress:
```
[gamerz_xp_progress title="Your Progress"]
```

### [gamerz_weekly_challenges]
Display weekly challenges:
```
[gamerz_weekly_challenges title="This Week's Challenges"]
```

### [gamerz_my_challenges]
Show user's challenge history:
```
[gamerz_my_challenges title="My Challenge History"]
```

## Template Integration

To show XP and rank in your theme files:

```php
// Get current user's rank
$rank_system = new \Codexpert\Gamerz_Guild\Classes\Rank_System();
$rank = $rank_system->get_user_rank(get_current_user_id());
echo 'Rank: ' . $rank['name'];

// Get current user's XP
$xp_system = new \Codexpert\Gamerz_Guild\Classes\XP_System();
$user_xp = $xp_system->get_user_xp(get_current_user_id());
echo 'XP: ' . $user_xp;

// Get user's progress to next rank
$progress = $rank_system->get_rank_progress(get_current_user_id());
echo 'Progress: ' . round($progress['progress_percent'], 1) . '%';
```

## Testing the System

### Admin Testing:
1. Go to **Gamerz Guild → Dashboard** to view system stats
2. Go to **Gamerz Guild → Settings** to configure Discord integration
3. Manage guilds at **Guilds** submenu
4. Manage challenges at **Challenges** submenu

### User Testing:
1. Create a test guild and verify XP is rewarded
2. Post in forums and verify XP is awarded
3. Check if rank progression works correctly
4. Verify badge earning system
5. Test redemption system with available XP
6. Verify challenge completion
7. Check Discord integration if configured

### XP Testing:
- Daily login: Go to your site daily for 5 XP
- Forum activity: Make posts/replies to earn XP
- Guild activities: Create/join guilds for XP
- Events: Attend events if The Events Calendar is active
- Content: Submit content if enabled

## Hooks and Filters

### Filters:
- `gamerz_daily_login_xp` - Modify daily login XP reward
- `gamerz_new_topic_xp` - Modify new topic XP reward
- `gamerz_new_reply_xp` - Modify reply XP reward
- `gamerz_daily_action_limits` - Modify daily action limits

### Actions:
- `gamerz_rank_up` - Triggered when user ranks up
- `gamerz_badge_awarded` - Triggered when badge is earned
- `gamerz_guild_created` - Triggered when guild is created

## Troubleshooting

### Common Issues:

1. **XP not awarding**: 
   - Ensure myCred is active and configured
   - Check myCred hooks are active
   - Verify point type is named correctly

2. **Discord integration not working**:
   - Verify webhook URL is correct
   - Check bot token and permissions
   - Ensure Discord IDs are correct

3. **Rank progression not working**:
   - Confirm XP system is awarding points
   - Check rank thresholds are set correctly

4. **Shortcodes not displaying**:
   - Ensure user is logged in for personal shortcodes
   - Check if required plugins are active

### Plugin Compatibility:
- Fully compatible with BuddyPress and Youzify
- Integrates with bbPress for forum features
- Works with The Events Calendar for event tracking
- Compatible with WooCommerce for redemption system

## Visual Elements

### Rank Colors:
- Scrubling to Recruit: Gray
- Scout to Soldier: Gold/Orange
- Strategist to Captain: Purple
- Champion to Officer: Orange/Red
- Sage to Warlord: Pink
- Meme Master to Overlord: Green
- Nova Scrub: Cyan
- Scrub Prime: Blue
- Legendary Scrub: Gold

### Animations:
- Achievement unlock notifications
- XP gain animations
- Level up celebrations
- Progress bar animations
- Confetti effects

## Security and Performance

### Security Features:
- All user inputs are sanitized
- Nonces used for AJAX requests
- Permission checks on all actions
- Proper capability verification

### Performance Considerations:
- Caching for rank calculations
- Optimized database queries
- Efficient activity logging
- Minimal impact on site performance

## Updates and Maintenance

The plugin is designed to be maintenance-friendly:
- All data stored in WordPress standard format
- Easy to backup and restore
- Upgrade path for new features
- Clear upgrade notices

## Support and Contact

For support, please contact:
- Developer: NH Tanvir (hi@tanvir.io)
- Plugin page: Available in WordPress admin

## Changelog

### Version 1.0.0
- Initial release of complete Gamerz Guild system
- Full gamification implementation with XP, ranks, badges
- Guild management with all features
- Discord integration
- Visual enhancements and animations
- Redemption system with WooCommerce
- Leaderboards and challenges
- Complete integration with myCred, BuddyPress, bbPress

---

**Important**: This system is designed for gaming communities and should be used in conjunction with the specified plugins to get the full experience. Always backup your site before installing new plugins.