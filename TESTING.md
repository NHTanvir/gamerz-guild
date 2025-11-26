# Gamerz Guild Plugin - Testing Guide

## Overview
This document outlines comprehensive testing procedures for the Gamerz Guild gamification system. All features must be thoroughly tested before the system can be considered production-ready.

## Prerequisites
- WordPress 5.0+
- myCRED plugin installed and active
- BuddyPress installed and active
- bbPress installed and active (for forum integration)
- The Events Calendar (for event integration) - optional
- WooCommerce (for redemption system) - optional

## Test Environment Setup

### 1. Install Required Plugins
```bash
# Ensure these plugins are active
- myCRED (version 2.9.0+)
- BuddyPress (version 10.0+)
- bbPress (version 2.6+)
- The Events Calendar (version 6.0+) - optional
- WooCommerce (version 7.0+) - optional
```

### 2. Configure myCRED
- Set up a point type called "Gamerz XP" or similar
- Ensure myCRED hooks are enabled for the actions you want to test

### 3. Create Test Users
Create several test users with different permission levels:
- Admin user
- Regular member (new user)
- High-ranking member (simulated high XP)

## Feature Tests

### 1. XP Earning System
**Test daily login XP:**
1. Log in as test user
2. Verify 5 XP is awarded for first login of the day
3. Check myCRED log for daily login entry
4. Verify XP is not awarded on second visit to same day

**Test forum XP:**
1. Navigate to bbPress forums
2. Create a new topic
3. Verify 8 XP is awarded to topic author
4. Reply to an existing topic
5. Verify 5 XP is awarded for reply
6. Check that XP appears in user profile

**Test event XP:**
1. If The Events Calendar is active, attend an event
2. Verify 15 XP is awarded for event participation
3. Check myCRED log for event participation entry

**Test guild creation/join XP:**
1. Create a new guild
2. Verify 50 XP is awarded for guild creation
3. Join an existing guild
4. Verify 10 XP is awarded for guild joining

### 2. Rank Progression System
**Test rank advancement:**
1. Manually adjust user XP to cross rank thresholds
2. Verify rank changes automatically
3. Check that user receives correct rank name in profile
4. Verify rank privileges are enabled (e.g., ability to create topics at rank 3)

**Test rank visualization:**
1. View user profile in BuddyPress
2. Verify rank is displayed prominently
3. Check that forum posts show user rank
4. Verify progress bar displays correctly on profile

### 3. Badge System
**Test automatic badge awarding:**
1. Perform first forum post
2. Verify "Forum Newbie" badge is awarded
3. Perform first guild creation
4. Verify relevant guild badge is awarded
5. Check that badges appear on user profile

**Test manual badge awarding:**
1. Navigate to user profile as admin
2. Manually award a badge (like "Good Samaritan")
3. Verify badge appears on user profile
4. Check that user receives notification

### 4. Guild System
**Test guild creation:**
1. Navigate to guild creation page (or admin panel)
2. Create a new guild with valid details
3. Verify guild is created successfully
4. Check that creator is assigned as guild leader
5. Verify 50 XP is awarded to creator
6. Confirm guild appears in guild listings

**Test guild joining:**
1. Browse available guilds as regular user
2. Find "Join Guild" button for an existing guild
3. Click the join button and confirm action
4. Verify user is added to guild members
5. Confirm 10 XP is awarded for joining
6. Check that guild membership appears on user profile

**Test guild leaving:**
1. Visit guild page as a member (not leader)
2. Find "Leave Guild" button
3. Click and confirm leaving guild
4. Verify user is removed from guild
5. Confirm guild no longer appears on user profile

**Test guild member management:**
1. As guild leader, access member management
2. Test promoting members to officer role
3. Test demoting officers back to member
4. Test kicking members from guild
5. Verify role changes are reflected in guild

**Test guild activity feed:**
1. Perform guild-related actions (join, leave, etc.)
2. Verify activities appear in guild activity feed
3. Check that timestamps and descriptions are correct

### 5. Forum Integration
**Test rank display in forums:**
1. Navigate to bbPress forum
2. Verify user ranks appear under usernames
3. Check that rank badges show in post author info
4. Confirm XP progress info appears in user profile view

**Test forum XP awards:**
1. Create new topic in forum
2. Verify XP is awarded to topic creator
3. Reply to existing topic
4. Confirm XP is awarded for reply
5. Check myCRED logs for accuracy

### 6. Events Integration
**Test guild events:**
1. Create a guild event using the system
2. Verify event is created in The Events Calendar
3. Register for the guild event
4. Confirm XP is awarded upon attendance
5. Check that event appears linked to guild

### 7. Redemption System
**Test redemption shop:**
1. Navigate to XP redemption area
2. Verify available items are listed with XP costs
3. Check that user XP balance is displayed
4. Confirm user can only access items with sufficient XP

**Test item redemption:**
1. Select an available redemption item
2. Confirm redemption process completes
3. Verify XP is deducted from user balance
4. Check that reward is properly granted (coupon, cosmetic, etc.)

### 8. Leaderboards
**Test global leaderboard:**
1. Access global leaderboard page
2. Verify users are ranked by XP correctly
3. Check that top users display properly
4. Confirm all fields (rank, name, XP, rank name) are accurate

**Test guild leaderboard:**
1. Access a specific guild's leaderboard
2. Verify only guild members are shown
3. Check that ranking is correct within guild
4. Confirm the display matches global leaderboard styling

### 9. Weekly Challenges
**Test challenge visibility:**
1. Access weekly challenges page/shortcode
2. Verify current challenges are displayed
3. Check that challenge descriptions and rewards are shown
4. Confirm completion status updates correctly

**Test challenge completion:**
1. Complete a challenge through the required action
2. Verify challenge marked as completed
3. Confirm XP reward is awarded
4. Check that badge is awarded if applicable

### 10. Discord Integration
**Test role assignment:**
1. Have user rank up in the system
2. Verify corresponding Discord role is assigned
3. Check that old role is removed if applicable
4. Confirm Discord name color updates with rank

**Test Discord announcements:**
1. Perform a rank-up action
2. Check that announcement appears in Discord channel
3. Verify badge awards are announced
4. Confirm guild creation/activities are announced

### 11. Visual Enhancements
**Test rank visualization:**
1. View user profiles across the site
2. Verify ranks are displayed consistently
3. Check that progress bars function properly
4. Confirm avatar rank indicators appear

**Test XP bar visibility:**
1. Navigate site as logged-in user
2. Verify XP progress bar appears
3. Check that it updates in real-time with XP gains
4. Confirm it's visible across different pages

## Common Issues to Test

### 1. Missing Join/Leave Buttons
**Issue**: Guild join/leave buttons not visible
**Test**: 
- Verify user is logged in
- Check if user is already in a guild (join button should be disabled)
- Confirm guild has space for new members
- Test with different user roles and statuses

### 2. XP Not Awarded
**Issue**: XP not being awarded for actions
**Test**:
- Verify myCRED is properly configured
- Check that required plugins are active
- Confirm user is logged in
- Review myCRED logs for errors

### 3. Rank Progression Not Working
**Issue**: Ranks don't update automatically
**Test**:
- Manually adjust XP to cross thresholds
- Check if rank updates immediately
- Verify rank privileges are applied
- Test with different user accounts

### 4. Badge Awards Not Functioning
**Issue**: Badges not being awarded automatically
**Test**:
- Perform badge-earning actions
- Check if badges appear in profile
- Verify myCRED logs show badge entries
- Test manual badge awarding

### 5. Discord Integration Issues
**Issue**: Discord roles/announcements not working
**Test**:
- Verify Discord webhook is properly configured
- Check bot token permissions
- Confirm user Discord IDs are stored
- Test announcements with debugging enabled

## Troubleshooting Common Problems

### Guild System Not Working
**Check these points:**
- Ensure BuddyPress is active and functional
- Verify custom post type "guild" is registered
- Check that user permissions allow guild creation
- Confirm AJAX requests are working (check browser console)
- Verify guild-related shortcodes are properly implemented

### XP Not Being Awarded
**Check these points:**
- myCRED plugin is active and configured
- Point type matches system expectations
- User is logged in when performing actions
- myCRED hooks are enabled for the actions
- No PHP errors in logs during XP awarding

### Badges Not Displaying
**Check these points:**
- myCRED badges addon is active
- User has earned and saved badges
- Template files are properly calling badge functions
- User meta for badges is correctly stored

## Testing Checklist

### Basic Functionality
- [ ] XP earning for daily login works
- [ ] Forum XP awards function correctly
- [ ] Guild creation awards 50 XP
- [ ] Guild joining awards 10 XP
- [ ] Rank progression works based on XP
- [ ] Badges award automatically when criteria met

### Guild Features
- [ ] Guild creation interface functional
- [ ] Join guild button visible and works
- [ ] Leave guild button visible and works
- [ ] Guild member management works
- [ ] Guild activity feed displays properly
- [ ] Guild forums integrate with main forum system

### User Interface
- [ ] Rank displays on user profiles
- [ ] XP bars show correctly
- [ ] Badges appear on profiles
- [ ] Leaderboards display properly
- [ ] Weekly challenges show correctly

### Integration Features
- [ ] Discord integration works (roles and announcements)
- [ ] WooCommerce redemption functional
- [ ] Event integration works if The Events Calendar active
- [ ] Forum integration displays ranks/badges

### Performance
- [ ] System handles multiple users without performance issues
- [ ] Database queries are optimized
- [ ] AJAX requests respond quickly
- [ ] No memory leaks or excessive resource usage

## Known Issues to Address

1. **Missing guild join/leave buttons**: Verify AJAX handlers and front-end display logic
2. **XP not awarded from front-end**: Check myCRED integration hooks
3. **Guild data not saving properly**: Verify custom post type and meta handling
4. **No Discord integration working**: Check webhook configuration and bot permissions
5. **Badge display issues**: Confirm badge storage and retrieval from user meta
6. **Rank progression not triggering**: Verify myCRED hook for rank checks

## Post-Testing Verification

After completing all tests:

1. Verify all features work together cohesively
2. Test with multiple concurrent users
3. Check system performance under load
4. Verify data integrity and proper cleanup
5. Confirm no security vulnerabilities exist
6. Document any remaining issues or improvements needed

## Reporting Issues

When logging issues found during testing:
- Include steps to reproduce
- Provide expected vs actual behavior
- Note plugin versions and WordPress version
- Include relevant error messages or console output
- Screenshot any visual issues