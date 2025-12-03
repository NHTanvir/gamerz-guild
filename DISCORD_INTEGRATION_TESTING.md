# Discord Integration Testing Guide for Gamerz Guild

## Overview
This document provides comprehensive testing procedures for the Discord integration features in the Gamerz Guild plugin, including rank synchronization, announcements, and role management.

## Prerequisites
- WordPress with Gamerz Guild plugin active
- myCRED plugin installed and active (for XP system)
- Discord server with administrator access
- Discord webhook URL
- Discord bot token (for role management)
- Discord server ID

## Discord Configuration Setup

### Step 1: Configure Discord Settings in WordPress
**Location:** WordPress Admin → Gamerz Guild → Settings
**Steps:**
1. Log into WordPress admin dashboard
2. Navigate to `Gamerz Guild` → `Settings` menu
3. Locate "Discord Integration" section
4. Enter your Discord Webhook URL: `https://discord.com/api/webhooks/...`
5. Enter your Discord Bot Token (for role management): `M...`
6. Enter your Discord Guild ID (server ID): `...`
7. Map all 15 ranks to Discord roles using the role mapping table
8. Click "Save Changes"

**Expected Result:** Settings saved successfully, Discord integration enabled.

### Step 2: Verify Role Mappings
**Location:** WordPress Admin → Gamerz Guild → Settings
**Steps:**
1. In the "Discord Role Mapping" section
2. Verify all 15 ranks are mapped to Discord role IDs:
   - Scrubling → (no specific role or basic member)
   - Scrub Recruit → "Rookie Scrub" role ID: `1443626808929943595`
   - Scrub Scout → (role ID if applicable)
   - Scrub Soldier → "Casual Scrub" role ID: `1443626957228216340`
   - Scrub Strategist → (role ID if applicable)
   - Scrub Captain → (role ID if applicable)
   - Scrub Champion → "Elite Scrub" role ID: `1443627075520167957`
   - Guild Officer → (role ID if applicable)
   - Scrub Sage → (role ID if applicable)
   - Scrub Warlord → (role ID if applicable)
   - Meme Master → (role ID if applicable)
   - Scrub Overlord → "Mythic Scrub" role ID: `1443627282588762275`
   - Nova Scrub → (role ID if applicable)
   - Scrub Prime → (role ID if applicable)
   - Legendary Scrub → "Legendary Scrub" role ID: `1443627161578766461`

**Expected Result:** All rank-role mappings configured and saved.

---

## Discord User Profile Linking

### Test 1: User Discord Profile Fields
**Location:** User Profile editing (both admin and user side)
**Steps:**
1. Log in as a user to access profile settings
2. Navigate to profile editing page (usually `/wp-admin/profile.php` or similar)
3. Locate "Discord Integration" section (should appear with plugin active)
4. Enter Discord User ID (e.g., `123456789012345678`)
5. Enter Discord Username#Tag (e.g., `scrub_warrior#1234`)
6. Save profile changes
7. Test with another user account

**Expected Result:** Discord information saved to user profile and accessible in user meta data.

### Test 2: Admin User Profile Management
**Location:** WordPress Admin → Users → Edit User
**Steps:**
1. Log into WordPress admin
2. Go to `Users` → Select any user
3. Scroll down to "Discord Integration" section
4. Verify Discord fields appear (Discord ID and Discord Username)
5. Update fields if needed
6. Save user profile
7. Test with multiple user accounts

**Expected Result:** Admins can set Discord information for users.

---

## Discord Announcements Testing

### Test 3: Rank Up Announcements
**Location:** WordPress site + Discord server
**Steps:**
1. Ensure Discord webhook is properly configured in settings
2. Have a test user earn enough XP to reach a new rank (e.g., from 49→50 XP for "Scrub Recruit")
3. Monitor the Discord channel where webhook posts
4. Verify you see a message like: `:tada: Rank Up Achievement! @user has ascended to Scrub Recruit! :medal:`
5. Test multiple rank ups to different levels
6. Verify embed styling and color coding by rank level

**Expected Result:** Automatic rank up announcement appears in Discord with proper formatting.

### Test 4: Badge Award Announcements
**Location:** WordPress site + Discord server
**Steps:**
1. Configure Discord webhook in settings
2. Have a user earn a badge (auto or manual) - e.g., first forum post → Forum Newbie badge
3. Monitor Discord for badge announcement
4. Verify message format: `:medal: New Badge Earned! @user earned the Forum Newbie badge! :trophy:`
5. Test with different badge types (auto and manual)
6. Verify embed styling with badge description in footer

**Expected Result:** Automatic badge achievement announcement in Discord.

### Test 5: Guild Event Announcements
**Location:** WordPress site + Discord server
**Steps:**
1. Have a user create a new guild via `[gamerz_guild_management]` shortcode page
2. Monitor Discord for "New Guild Formed!" announcement
3. Have other users join the guild
4. Verify "New Guild Member!" announcements appear
5. Test with multiple guilds and joins

**Expected Result:** Guild creation and member join announcements appear in Discord.

---

## Discord Role Management Testing

### Test 6: Automatic Role Assignment on Rank Up
**Location:** Discord server + WordPress site
**Steps:**
1. Ensure Discord bot token and guild ID are configured
2. Configure role mappings between ranks and Discord role IDs
3. Have a user reach XP threshold for a mapped role (e.g., 50 XP → Scrub Recruit → role ID `1443626808929943595`)
4. Monitor user's Discord roles - the appropriate role should be added
5. Verify old lower-level rank roles are properly removed
6. Test with multiple rank progressions (50→100→200 XP, etc.)

**Expected Result:** Discord role automatically assigned based on site rank, with old roles removed.

### Test 7: Role Removal and Changes
**Location:** Discord server + WordPress site
**Steps:**
1. Have a user with a Discord role-attached rank (e.g., Scrub Recruit with "Rookie Scrub" role)
2. Ensure they have only the appropriate current rank role
3. If user drops in XP (hypothetically), verify role management (typically roles only upgrade)
4. Test that multiple rank roles aren't accumulated
5. Verify only one rank role is active at a time

**Expected Result:** Proper role management with only current rank role active.

### Test 8: Role Color and Permissions
**Location:** Discord server
**Steps:**
1. After role assignment, verify role has correct color based on rank level
2. Check that role permissions align with rank privileges
3. Verify role appears in proper position in member list
4. Test role inheritance and hierarchy

**Expected Result:** Rank roles have appropriate visual appearance and permissions.

---

## Discord Webhook Message Testing

### Test 9: Announcement Message Formatting
**Location:** Discord server
**Steps:**
1. Trigger each type of announcement (rank up, badge, guild events)
2. Check message embed formatting:
   - Title should be appropriately labeled
   - Description should tag user and mention achievement
   - Color should match rank level
   - Footer should provide additional context
3. Verify timestamp is accurate
4. Test message length limits and truncation

**Expected Result:** Well-formatted, professional embed messages in Discord.

### Test 10: Discord Message Content Verification
**Location:** Discord server
**Steps:**
1. For rank up: Message should mention old → new rank
2. For badge: Message should name the specific badge earned
3. For guild events: Message should name the guild and action
4. Verify user tagging works properly (using Discord ID if available, fallback to username)
5. Check that emojis appear correctly

**Expected Result:** Accurate, informative messages with proper user identification.

---

## Discord Integration Security Testing

### Test 11: Webhook Security
**Location:** Discord server + WordPress site
**Steps:**
1. Verify webhook URL is not publicly accessible
2. Ensure webhook can't be triggered by unauthorized requests
3. Test that invalid requests to webhook endpoints are rejected
4. Check that sensitive information isn't exposed in logs

**Expected Result:** Secure webhook communication with proper validation.

### Test 12: Bot Token Security
**Location:** WordPress Admin
**Steps:**
1. Verify bot token is stored securely (not in plain text where users can see)
2. Check that token is properly validated before use
3. Test that invalid tokens fail gracefully
4. Verify token permissions are appropriate (not excessive)

**Expected Result:** Secure and validated bot token usage.

---

## Discord Integration Performance Testing

### Test 13: Announcement Delivery Speed
**Location:** Discord server + WordPress site
**Steps:**
1. Perform rank up, badge earn, and guild join actions
2. Measure time from action completion to Discord announcement
3. Verify announcements appear within 1-3 seconds
4. Test during high-traffic periods if possible

**Expected Result:** Near real-time Discord announcements.

### Test 14: Concurrent Announcement Handling
**Location:** Discord server + WordPress site
**Steps:**
1. Simulate multiple users achieving milestones simultaneously
2. Monitor Discord for rate limiting or message queuing
3. Verify no announcements are lost during high activity
4. Check that user mentions don't conflict

**Expected Result:** Reliable handling of multiple concurrent announcements.

---

## Discord Integration Error Handling

### Test 15: Webhook Failure Handling
**Location:** WordPress site
**Steps:**
1. Temporarily disable Discord webhook or make it invalid
2. Perform actions that would trigger announcements
3. Verify system handles webhook failures gracefully
4. Check that errors are logged appropriately
5. Restore webhook and verify functionality resumes

**Expected Result:** Graceful handling of webhook failures with proper logging.

### Test 16: Bot Token Failure Handling
**Location:** WordPress site
**Steps:**
1. Use invalid or expired bot token
2. Trigger actions that would change Discord roles
3. Verify system handles token failures without breaking
4. Check error logging for token issues
5. Restore valid token and verify role management resumes

**Expected Result:** Graceful handling of bot token issues.

---

## Discord Integration Verification Checklist

### Configuration Verification
- [ ] Discord webhook URL properly configured in settings
- [ ] Discord bot token properly configured in settings (for role management)
- [ ] Discord guild ID properly configured in settings
- [ ] All 15 rank-role mappings configured
- [ ] Settings saved and persist after saving

### Announcement Verification
- [ ] Rank up announcements appear in Discord
- [ ] Badge award announcements appear in Discord
- [ ] Guild creation announcements appear in Discord
- [ ] Guild join announcements appear in Discord
- [ ] Messages formatted correctly with proper embeds

### Role Management Verification
- [ ] Discord roles assigned when ranks are achieved
- [ ] Old roles properly removed when new ones assigned
- [ ] Only appropriate rank roles are assigned
- [ ] Role colors and permissions correct
- [ ] No duplicate or conflicting roles

### Security Verification
- [ ] Webhook URL not accessible to unauthorized users
- [ ] Bot token stored securely
- [ ] Proper authentication for all Discord interactions
- [ ] Error handling for security failures

### Performance Verification
- [ ] Announcements delivered in near real-time
- [ ] No performance degradation from Discord integration
- [ ] System handles concurrent Discord operations
- [ ] Message delivery reliable during high usage

### Error Handling Verification
- [ ] Webhook failures handled gracefully
- [ ] Bot token issues handled gracefully
- [ ] Error logs provide useful debugging information
- [ ] System recovers from temporary Discord issues

---

## Troubleshooting Common Discord Issues

### Issue 1: Discord Messages Not Appearing
**Symptoms:** No announcements in Discord despite triggering events
**Troubleshooting Steps:**
1. Verify webhook URL in settings is correct and current
2. Check Discord server permissions allow webhook posts
3. Verify channel is not under rate limiting
4. Check WordPress error logs for webhook failures
5. Test webhook directly with a test message

### Issue 2: Roles Not Assigning
**Symptoms:** User ranks update but Discord roles don't change
**Troubleshooting Steps:**
1. Verify bot token has "Manage Roles" permission in Discord
2. Check that role IDs in mapping are valid for your server
3. Verify bot role is positioned above the roles it needs to assign
4. Check WordPress error logs for role assignment failures
5. Test role assignment manually

### Issue 3: Wrong User Tagging
**Symptoms:** Webhook mentions wrong user or generic username
**Troubleshooting Steps:**
1. Verify Discord ID is properly stored in user meta
2. Check that user linking is working properly
3. Verify fallback to username when Discord ID missing
4. Test with multiple users to isolate issue

### Issue 4: Formatting Issues
**Symptoms:** Poor embed formatting, missing emojis, etc.
**Troubleshooting Steps:**
1. Check Discord message payload structure
2. Verify embed fields are properly formatted
3. Ensure emoji codes are valid Discord emoji
4. Test with Discord developer tools if available

---

## Quality Assurance Final Verification

### Pre-Launch Checklist
- [ ] All Discord announcement types tested and working
- [ ] Role assignment and removal working correctly
- [ ] Security measures validated
- [ ] Error handling verified
- [ ] Performance benchmarks met
- [ ] Configuration options documented
- [ ] User Discord linking working properly
- [ ] Message formatting looks professional
- [ ] Integration doesn't impact site performance

### Post-Launch Monitoring
- [ ] Monitor Discord announcement delivery rates
- [ ] Track role assignment success rates
- [ ] Collect user feedback on Discord integration
- [ ] Watch for error logs related to Discord communication
- [ ] Verify announcements are timely and accurate
- [ ] Monitor for any rate limiting issues
- [ ] Check that role management is working at scale