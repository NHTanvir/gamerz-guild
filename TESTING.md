# Gamerz Guild Plugin - Complete Testing Guide

## Overview
This guide provides detailed, line-by-line testing instructions for the complete Gamerz Guild gamification system with all requested features implemented.

## Prerequisites
- WordPress installation with plugin active
- Required plugins: myCred, BuddyPress, bbPress, The Events Calendar
- Optional plugins: Youzify, WooCommerce (for redemption), Uncanny Automator (for Discord)
- Test user accounts created

## Comprehensive Feature Testing Guide

### 1. Guild Management System Testing

#### Test 1.1: Create Guild Functionality
1. Navigate to: Guild Creation Page (URL depends on your setup)
2. Click "Create New Guild" button
3. Fill in required fields:
   - Guild Name: Enter "Test Guild"
   - Description: Enter "This is a test guild"
   - Tagline: Enter "Testing purposes"
   - Max Members: Enter "20" (default)
4. Click "Create Guild" button
5. Expected Result: Guild appears in your guild list with status "Active"
6. Admin Check: Visit WP Admin → Guilds → Verify guild exists with proper details
7. XP Check: Verify creator received 50 XP (Guild Creation reward)

#### Test 1.2: Join Guild Functionality
1. Log in as different user (not guild creator)
2. Navigate to: Browse Guilds page
3. Find "Test Guild" in list
4. Click "Join Guild" button
5. Expected Result: User is added to guild member list
6. Admin Check: Visit WP Admin → Guilds → Click guild → Check member list
7. XP Check: Verify joining user received 10 XP (Guild Join reward)

#### Test 1.3: Leave Guild Functionality
1. Log in as guild member (not leader)
2. Navigate to: Guild Dashboard
3. Click "Leave Guild" button
4. Confirm in popup if shown
5. Expected Result: User is removed from guild member list
6. Admin Check: Visit WP Admin → Guilds → Verify user removed
7. Check: User should no longer see guild in their guild list

#### Test 1.4: Guild Member Management
1. Log in as Guild Leader
2. Navigate to: Guild Management page
3. Test Promote: Click "Promote" next to a member → Confirm role changes to "Officer"
4. Test Demote: Click "Demote" → Confirm role changes back to "Member"  
5. Test Kick: Click "Kick" → Confirm member is removed
6. Expected Results: All role changes reflected immediately in member list

### 2. Guild Forums Integration Testing

#### Test 2.1: Forum Access for Guild Members
1. As guild member, navigate to guild forum (if separate)
2. Verify you can create new topics and replies
3. As non-guild member, try accessing guild-specific areas
4. Expected Result: Non-members restricted from guild-specific forum areas

#### Test 2.2: Rank Display in Forums
1. Navigate to any forum topic
2. Check user avatars/usernames for:
   - Rank badge displayed under username
   - XP amount shown (e.g., "+1250 XP")
   - Rank level indicator (e.g., "Scrub Strategist")
3. Expected Result: Ranks displayed consistently across all forum posts

#### Test 2.3: XP Rewards from Forums
1. Create a new forum topic
2. Check myCred log: Should show +8 XP for new topic
3. Create a forum reply
4. Check myCred log: Should show +5 XP for reply
5. Expected Result: All forum activities properly award XP

### 3. Guild Events Integration Testing

#### Test 3.1: Guild-Specific Events
1. Create a new event using The Events Calendar
2. In event creation, link to your test guild
3. Save the event
4. Expected Result: Event appears in guild event list
5. Admin Check: WP Admin → Events → Verify event has guild association via custom field

#### Test 3.2: XP Rewards from Events  
1. Attend a guild event
2. After event completion, check XP log
3. Expected Result: Should receive +15 XP for event participation
4. For event victory, check if +50 XP was awarded

### 4. Guild Activity Feed Testing

#### Test 4.1: Activity Logging
1. Perform guild actions: join, leave, get promoted
2. Navigate to: Guild Activity Feed page
3. Expected Results: Each action should appear as a chronological entry:
   - "User X joined the guild" 
   - "User Y was promoted to Officer"
   - "User Z left the guild"

#### Test 4.2: Real-time Updates
1. Have another user perform guild action while viewing feed
2. Refresh activity feed
3. Expected Result: New activities appear in chronological order

### 5. XP Earning System Testing

#### Test 5.1: Daily Actions
1. Log in on first day → Check for +5 XP (daily login)
2. Return tomorrow → Check for another +5 XP
3. Expected Result: Daily login bonus awarded once per 24-hour period

#### Test 5.2: Social Actions
1. Like another user's post → Should earn +1 XP
2. Make a friend connection → Should earn +2 XP  
3. Add comment → Should earn +1 XP
4. Expected Result: Social actions award configured XP amounts

#### Test 5.3: Creative Actions
1. Submit content (blog post, media, etc.) → Should earn +20 XP
2. Write a forum guide (>300 words) → Should earn +15 XP
3. Expected Result: Creative contributions award higher XP values

#### Test 5.4: Anti-Abuse Mechanisms
1. Make 10 forum replies rapidly → First 5 should get full XP, rest should get 0
2. Like 50 posts in a day → After first 10, no more XP earned
3. Expected Result: Daily caps and per-action limits prevent abuse

### 6. Rank Progression System Testing

#### Test 6.1: Rank Advancement
1. Start as Rank 1 (Scrubling) with 0 XP
2. Earn 50 XP through activities
3. Expected Result: Should automatically advance to Rank 2 (Scrub Recruit)
4. Check profile → Rank should show as "Scrub Recruit"

#### Test 6.2: Rank Privileges
1. Reach Rank 2 → Verify custom avatar upload is unlocked
2. Reach Rank 3 → Verify topic creation is unlocked  
3. Reach Rank 5 → Verify event creation ability activates
4. Expected Result: Each rank unlocks appropriate privileges

#### Test 6.3: Progress Tracking
1. Visit profile page
2. Check XP progress bar → Shows percentage to next rank
3. View detailed XP → Shows current total and needed for next rank
4. Expected Result: Progress visually represented accurately

### 7. Badge Achievement System Testing

#### Test 7.1: Automatic Badges
1. Make first forum post → "Forum Newbie" badge should auto-award
2. Add 5 friends → "Social Butterfly" badge should auto-award
3. Login for 30 consecutive days → "Daily Grinder" badge should auto-award
4. Expected Result: Auto-badges trigger based on specified criteria

#### Test 7.2: Manual Badges
1. As admin, go to user profile
2. Award "Helpful Scrub" manual badge
3. Expected Result: User receives badge and XP notification

#### Test 7.3: Badge Display
1. Visit user profile with earned badges
2. Check badge gallery → All earned badges should display
3. Hover over badge → Description tooltip should appear
4. Expected Result: Badges properly displayed and accessible

### 8. XP Redemption System Testing

#### Test 8.1: Redemption Store Access
1. Navigate to: XP Redemption Store
2. Check available items → Should see items matching user's rank and XP
3. Expected Result: Items filtered by user's privileges and affordability

#### Test 8.2: Merchandise Redemption
1. Select "$5 Merch Discount" (costs 1000 XP)
2. Click "Redeem" button
3. Expected Result: 1000 XP deducted, WooCommerce coupon generated

#### Test 8.3: Digital Rewards Redemption
1. Select "Custom Avatar Frame" (costs 500 XP)  
2. Click "Redeem" button
3. Expected Result: 500 XP deducted, cosmetic effect applied to profile

#### Test 8.4: Access Rewards Redemption
1. Select "VIP Voice Access" (costs 1000 XP)
2. Click "Redeem" button  
3. Expected Result: 1000 XP deducted, Discord role access granted

### 9. Leaderboard and Visibility Testing

#### Test 9.1: Global Leaderboard
1. Use shortcode [gamerz_leaderboard] or visit leaderboard page
2. Check ranking order → Top XP earners at top
3. Expected Result: Users ranked by total XP descending

#### Test 9.2: Guild-Specific Leaderboard
1. Use shortcode [gamerz_leaderboard type="guild" guild_id="X"] 
2. Check ranking order → Only guild members shown ranked by XP
3. Expected Result: Guild leaderboard shows only member rankings

#### Test 9.3: User Position Tracking
1. Visit leaderboard → Find your position
2. Earn XP → Reload leaderboard
3. Expected Result: Position should improve with increased XP

### 10. Weekly Challenges System Testing

#### Test 10.1: Challenge Visibility
1. Navigate to: Weekly Challenges page
2. Check current challenges → Should show 3 rotating challenges
3. Expected Result: New challenges appear each Monday

#### Test 10.2: Challenge Completion
1. Complete "Squad Up with Newbie" challenge
2. Click "Mark Complete" button
3. Expected Result: +50 XP awarded, challenge marked as completed

#### Test 10.3: Challenge Proof Submission
1. For challenges requiring proof, click "Submit Proof"
2. Enter proof details in modal
3. Submit for admin review
4. Expected Result: Proof submitted for manual verification

### 11. Discord Integration Testing

#### Test 11.1: Rank-Up Announcements
1. Level up to a new rank
2. Check Discord server → Should see rank-up announcement
3. Expected Result: Automated message posted to configured channel

#### Test 11.2: Role Assignment  
1. Reach rank threshold (e.g., 300 XP = Rank 5 = Scrub Strategist)
2. Check Discord → Should have appropriate role assigned
3. Expected Result: Discord role matches website rank

#### Test 11.3: Achievement Notifications
1. Earn a notable badge
2. Check Discord → Should see achievement unlock notification
3. Expected Result: Badge earned announced in Discord

### 12. Visual Enhancements Testing

#### Test 12.1: XP Progress Bar
1. Navigate site → Check bottom-left corner
2. Expected Result: Animated XP progress bar visible with current XP

#### Test 12.2: Badge Animations
1. Earn a badge → Check for confetti animation
2. Expected Result: Confetti effect and achievement notification

#### Test 12.3: Rank Indicators
1. View any user profile/forum post
2. Expected Result: Rank name and level clearly displayed

#### Test 12.4: HUD Elements
1. Navigate site as logged-in user
2. Expected Result: Game-like HUD elements visible throughout interface

## Troubleshooting Common Issues

If testing reveals issues:
1. Check WP Admin → Tools → Error Log for PHP errors
2. Verify all required plugins are active
3. Confirm myCred point types are properly set up
4. Check Discord webhook URL validity
5. Ensure Cron jobs are running (for weekly challenges reset)

## Performance Testing

1. Load time verification: All pages should load in <3 seconds
2. Concurrency test: 10 users performing actions simultaneously
3. Database efficiency: Leaderboard queries should execute in <500ms
4. Memory usage: Plugin should not exceed 16MB additional memory

## Final Verification Checklist

Complete each test item and check off:

- [ ] Guild creation, joining, leaving functionality works
- [ ] Guild member management works (promote/demote/kick)
- [ ] Guild forums integrate properly with bbPress
- [ ] Guild events link to The Events Calendar
- [ ] Guild activity feed logs all actions properly
- [ ] XP system awards points for all actions
- [ ] Rank progression advances correctly
- [ ] Badges award automatically and manually
- [ ] Redemption system processes XP exchanges
- [ ] Leaderboards update in real-time
- [ ] Weekly challenges rotate properly
- [ ] Discord integration posts announcements
- [ ] Visual elements display properly
- [ ] All anti-abuse measures function correctly
- [ ] Mobile responsiveness verified
- [ ] Admin controls work properly

## Admin Testing

As administrator, verify:
- [ ] Dashboard shows accurate statistics
- [ ] Guild management interface functional
- [ ] User XP can be manually adjusted
- [ ] Badges can be manually awarded
- [ ] Settings page saves configurations
- [ ] Challenge creation works properly