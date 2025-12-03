# Scrub Gamerz Gamification System - Complete Testing Guide

## Overview
This document provides a comprehensive testing guide for the complete Scrub Gamerz Gamification System with myCred. All 8 major components and their sub-features are detailed with step-by-step instructions for thorough testing.

## Prerequisites
- WordPress site with Gamerz Guild plugin active
- myCRED plugin active and configured
- BuddyPress (optional but recommended for full experience)
- bbPress (for forum integration)
- The Events Calendar (for event integration)

## Setup for Testing
1. Ensure all required plugins are installed and activated
2. Access the site as an administrator to configure settings
3. Create test pages with the required shortcodes (instructions below)
4. Create test user accounts for different scenarios

## Required Test Pages Setup
Before testing, create these WordPress pages with the following shortcodes:

**Page 1: Guild Management** 
- Page Title: "Guild Management"
- Content: `[gamerz_guild_management]`
- URL: `/guild-management/`

**Page 2: Leaderboard** 
- Page Title: "Leaderboard"
- Content: `[gamerz_leaderboard]`
- URL: `/leaderboard/`

**Page 3: Weekly Challenges**
- Page Title: "Weekly Challenges"
- Content: `[gamerz_weekly_challenges]`
- URL: `/weekly-challenges/`

**Page 4: My Challenges**
- Page Title: "My Challenges" 
- Content: `[gamerz_my_challenges]`
- URL: `/my-challenges/`

**Page 5: XP Progress**
- Page Title: "My Progress"
- Content: `[gamerz_xp_progress]`
- URL: `/my-progress/`

After creating these pages, you can begin testing each feature using the specific page locations below.

---

## 1. XP Earning System Testing

### Test Daily Login XP (5 XP)
**Location:** Any page requiring login, then check profile
**Pages to Visit:**
- Login page: `/wp-login.php` (or your custom login)
- Profile page: `/wp-admin/profile.php` (or BuddyPress profile like `/members/username/`)
- XP Progress page: `/my-progress/` (the page you created with `[gamerz_xp_progress]` shortcode)

**Steps:**
1. **Navigate to Login:** Go to your site's login page (`/wp-login.php` or custom login URL)
2. **Log Out First:** If already logged in, log out completely
3. **Log Back In:** Enter credentials and log in to the site
4. **Check XP Balance:** Navigate to your progress page `/my-progress/` 
5. **Verify XP:** Look for +5 XP addition in your total XP and recent XP log
6. **Test Daily Cap:** Try logging in again the same day - should NOT receive additional XP

**Expected Result:** 5 XP awarded on first daily login, no additional XP for repeat logins same day.

### Test Forum XP (Topics & Replies)
**Location:** bbPress forums and your XP progress page
**Pages to Visit:**
- Forum section: `/forums/` (or your custom forum URL)
- Create topic: `/forums/` → Create Topic button
- Reply area: Inside any existing topic thread
- XP Progress page: `/my-progress/` (the page you created with `[gamerz_xp_progress]` shortcode)

**Steps:**
1. **Navigate to Forums:** Go to your forum section (`/forums/`)
2. **Create New Topic:** Click "Create Topic" button and submit a new topic - should receive +8 XP
3. **Visit XP Page:** Go to your XP progress page `/my-progress/` to verify XP received
4. **Reply to Topic:** Navigate to any existing topic and add a reply - should receive +5 XP
5. **Check Balance:** Return to `/my-progress/` to confirm both XP awards
6. **Verify Daily Caps:** Try posting multiple replies quickly - verify anti-spam measures work

**Expected Result:** 8 XP for new topic, 5 XP for reply, with anti-spam measures in place.

### Test Social Actions XP
**Location:** BuddyPress profiles, forums with like/reaction system
**Pages to Visit:**
- Your profile: `/members/yourusername/` (if using BuddyPress)
- Forum posts with reactions: `/forums/topic-topic-name/`
- Friends page: `/members/yourusername/friends/` (if using BuddyPress)
- XP Progress page: `/my-progress/`

**Steps:**
1. **Have Another User Like Your Post:** Ask another user to react/like to your forum post - you should get +1 XP on your `/my-progress/` page
2. **Add a Friend:** Navigate to another user's profile and send friend request - should get +2 XP when accepted
3. **Check Reaction Cap:** Have others react to your forum posts throughout the day - capped at ~10 XP/day from reactions
4. **Monitor XP:** Check your XP page `/my-progress/` to track total XP accumulation

**Expected Result:** 1 XP per reaction/like received, 2 XP for adding friend, reaction XP capped daily.

### Test Creative Actions XP
**Location:** Content submission areas and your XP progress page
**Pages to Visit:**
- Content submission page: `/wp-admin/post-new.php` (for posts) or your custom content page
- Your submitted content: The post/page URL after creation 
- XP Progress page: `/my-progress/`

**Steps:**
1. **Navigate to Content Creation:** Go to create a new post/page (`/wp-admin/post-new.php` or custom content area)
2. **Submit Content:** Create and publish a piece of content (post, image, video, etc.)
3. **Check XP Page:** Visit `/my-progress/` to verify you received +20 XP
4. **Create Strategy Guide:** If you have blog/guide functionality, create a detailed guide
5. **Verify Guide XP:** Check `/my-progress/` again to confirm +15 XP for strategy guides

**Expected Result:** 20 XP for content submission, 15 XP for guides, with verification for big actions if needed.

### Test Competitive Actions XP
**Location:** Event registration/completion areas and your XP progress page
**Pages to Visit:**
- Events page: `/events/` (if using The Events Calendar)
- Event registration: Individual event page
- XP Progress page: `/my-progress/`

**Steps:**
1. **Navigate to Events:** Go to your events section `/events/`
2. **Register and Participate:** Register for a community event/tournament and participate
3. **Check XP Page:** Visit `/my-progress/` to verify +15 XP for participation
4. **Tournament Victory:** If you win a tournament, check `/my-progress/` for +50 XP
5. **Clan Events:** For guild/group events, verify XP awards for participation and victories on `/my-progress/`

**Expected Result:** 15 XP for event participation, 50 XP for tournament wins.

### Test Leadership Actions XP
**Location:** Event creation, user management, and XP progress pages
**Pages to Visit:**
- Events creation: `/wp-admin/post-new.php?post_type=tribe_events` (if using The Events Calendar)
- Guild management: `/guild-management/` (your shortcode page)
- User management: `/wp-admin/users.php` (for recruiting)
- XP Progress page: `/my-progress/`

**Steps:**
1. **Navigate to Event Creation:** Go to create an event (`/wp-admin/post-new.php?post_type=tribe_events`)
2. **Organize an Event:** Create and publish an event - check `/my-progress/` for +50 XP
3. **Recruit Member:** Refer a new member and help them reach Rank 2 - earn +25 XP after verification
4. **Mentoring:** Manually get +30 XP for mentoring (admin-awarded) - check `/my-progress/`
5. **Verify Total:** Monitor your XP on `/my-progress/` to confirm all leadership rewards

**Expected Result:** 50 XP for event hosting, 25 XP for successful recruiting, 30 XP for mentoring.

---

## 2. Rank Progression Testing

### Test Rank Thresholds
**Location:** User profile pages and XP progress page
**Pages to Visit:**
- XP Progress page: `/my-progress/` (shows current rank)
- User profile: `/members/username/` (if using BuddyPress) or author page
- Leaderboard page: `/leaderboard/` (shows your rank position)

**Steps:**
1. **Check Starting Rank:** Visit `/my-progress/` to confirm you start as Scrubling (0 XP)
2. **Earn 50 XP:** Perform actions to reach 50+ XP, then check `/my-progress/` - should show "Scrub Recruit"
3. **Earn 100 XP:** Continue to 100+ XP, verify rank updates to "Scrub Scout" on `/my-progress/`
4. **Continue Through All Ranks:** Track progression to 4500 XP (Legendary Scrub) on `/my-progress/`
5. **Verify on Profile:** Check your user profile page to see rank displayed there too

**Expected Result:** Rank updates automatically when XP thresholds reached and displays on both `/my-progress/` and profile pages.

### Test Rank Display in Different Areas
**Location:** Multiple site areas accessible via your test pages
**Pages to Visit:**
- XP Progress page: `/my-progress/`
- Profile page: `/members/username/` or author page
- Forum sections: `/forums/` (check posts under username)
- Leaderboard page: `/leaderboard/`

**Steps:**
1. **Profile Page Check:** Visit your profile page - rank should be prominently displayed
2. **XP Progress Page:** Go to `/my-progress/` - current and next rank clearly shown
3. **Forum Sections:** Navigate to `/forums/` - your rank should appear under your username in posts
4. **Leaderboard Page:** Visit `/leaderboard/` - your current rank position displayed
5. **Check Discord:** If configured, verify rank role appears in Discord (see Discord section below)

**Expected Result:** Rank displayed consistently across `/my-progress/`, profile, forum, and leaderboard pages.

### Test Rank Privileges
**Location:** Various feature areas accessible via different pages
**Pages to Visit:**
- Profile editing: `/wp-admin/profile.php` or BuddyPress profile settings
- Forum creation: `/forums/create-topic/` (if available with rank privilege)
- Guild management: `/guild-management/` (if available with rank privilege)
- XP Progress page: `/my-progress/` (to track rank)

**Steps:**
1. **Rank 2 (Scrub Recruit) - Custom Avatar:** Go to profile settings (`/wp-admin/profile.php`) - verify ability to upload custom avatar unlocks
2. **Rank 3 (Scrub Scout) - Create Topics:** Try creating forum topics freely at `/forums/create-topic/` - should be allowed
3. **Rank 5 (Scrub Strategist) - Event Hosting:** Check guild page `/guild-management/` - hosting privileges should be available
4. **Rank 7 (Scrub Champion) - Profile Features:** Profile customization options should unlock
5. **Rank 10+ (Warlord+) - Custom Titles:** Advanced features should become available

**Expected Result:** Each rank unlocks specified privileges as defined in the system and visible on relevant pages.

### Test Discord Role Sync
**Location:** Discord server and WordPress admin settings
**Pages to Visit:**
- WordPress Admin: `/wp-admin/admin.php?page=gamerz-guild-settings`
- XP Progress page: `/my-progress/` (to track rank progression)

**Steps:**
1. **Configure Discord:** Ensure Discord settings are in WordPress Admin at `/wp-admin/admin.php?page=gamerz-guild-settings`
2. **Earn Rank Threshold:** Reach a rank that has Discord role mapping (e.g., Rank 7 - Scrub Champion)
3. **Check Discord:** Look in your Discord server - your role should update automatically to mapped role
4. **Verify Permissions:** Confirm the role has appropriate color and permissions in Discord
5. **Test Multiple Ranks:** As you progress to higher ranks, verify Discord roles update accordingly

**Expected Result:** Discord roles update automatically when ranks change, with appropriate colors and permissions.

---

## 3. Achievements & Badges Testing

### Test Auto-Award Badges
**Location:** Various activity areas and profile pages
**Pages to Visit:**
- Forum section: `/forums/` (for forum activities)
- Profile page: `/members/username/` or author page
- Content creation: `/wp-admin/post-new.php` (for content submission)
- Events: `/events/` (if using events system)

**Steps:**
1. **Forum Newbie Badge:** Make your first forum post at `/forums/` - return to profile page to check for "Forum Newbie" badge
2. **Social Butterfly Badge:** Add 5 friends or get 20 likes on your content - check profile page for "Social Butterfly" badge
3. **Daily Grinder Badge:** Log in 30 consecutive days - verify badge appears on profile page
4. **Content Creator Badge:** Upload your first piece of content at `/wp-admin/post-new.php` - check profile for badge
5. **Event Enthusiast Badge:** Participate in 5 events at `/events/` - verify badge on profile page

**Expected Result:** Badges automatically awarded when criteria met and visible on user profile page.

### Test Manual Award Badges
**Location:** Admin dashboard and user profile pages
**Pages to Visit:**
- WordPress Admin: `/wp-admin/`
- Users section: `/wp-admin/users.php`
- Edit user: `/wp-admin/user-edit.php?user_id=XX`
- User profile: `/members/username/` (to verify badge)

**Steps:**
1. **Log in as Admin:** Access WordPress Admin at `/wp-admin/`
2. **Navigate to Users:** Go to Users section `/wp-admin/users.php`
3. **Select User:** Click on a user to edit `/wp-admin/user-edit.php?user_id=XX`
4. **Award Tournament Champion:** Manually award "Tournament Champion" badge to event winner
5. **Award Party Starter:** Award "Party Starter" to event host
6. **Award Mentor:** Award "Mentor" to helpful users
7. **Verify on Profile:** Check recipient's profile page `/members/username/` to confirm badge appears

**Expected Result:** Admins can manually award badges through admin interface, visible on user profile page.

### Test Badge Display
**Location:** User profile pages
**Pages to Visit:**
- Profile pages: `/members/username/` or author pages
- User with multiple badges: Ask admin to check a user with multiple badges

**Steps:**
1. **Visit Profile:** Go to a user profile page (either your own or another with badges)
2. **Find Badges Section:** Look for "Badges" section on the profile page
3. **Verify Display:** Check that badges display properly with icons and names
4. **Badge Details:** Hover over or check details for badge descriptions
5. **Multiple Badges:** Test with users having multiple badges to ensure all display properly

**Expected Result:** Badges visually displayed with icons, names, and descriptions on user profile pages.

---

## 4. XP Redemption System Testing

### Test Real-World Rewards (WooCommerce)
**Location:** Redemption shop (this would typically be integrated into WooCommerce)
**Pages to Visit:**
- Your shop/checkout: `/shop/` or `/checkout/` (if WooCommerce redemption is set up)
- Profile/my account: `/my-account/` (to check coupons)
- XP Progress page: `/my-progress/` (to check XP balance)

**Steps:**
1. **Check XP Balance:** Visit `/my-progress/` to confirm sufficient XP for redemption
2. **Find Redemption Area:** Navigate to where you can spend XP for coupons (admin sets up WooCommerce products for XP redemption)
3. **Redeem Reward:** Choose a reward (e.g., 1000 XP for $5 coupon) and complete redemption 
4. **Verify XP Deduction:** Check `/my-progress/` - XP should decrease by redemption amount
5. **Check Coupon:** Go to `/my-account/` → "Coupons" to verify coupon code was generated
6. **Test at Checkout:** Go to `/checkout/` and apply the generated coupon code

**Expected Result:** XP deducted from balance, coupon generated and available in account, coupon works at checkout.

### Test In-Platform Rewards
**Location:** Profile and site features (no specific page, features activate on spending XP)
**Pages to Visit:**
- Profile page: `/members/username/` or profile editing
- XP Progress page: `/my-progress/` (to track XP before/after)
- Site pages to see cosmetic changes (depends on specific features)

**Steps:**
1. **Check XP Balance:** Visit `/my-progress/` to ensure sufficient XP
2. **Purchase Username Glow (200 XP):** Complete transaction (method depends on implementation) - check site for glow effect on your username
3. **Purchase Avatar Frame (500 XP):** Complete redemption - check your avatar on profile for animated frame
4. **Purchase VIP Access (1000 XP):** If implemented, verify access to premium areas
5. **Check Feature Activation:** Visit profile page `/members/username/` to see new features active

**Expected Result:** Features unlocked and visible across the site after XP redemption.

### Test Redemption Safeguards
**Location:** Redemption interface (WooCommerce or custom redemption page)
**Pages to Visit:**
- XP Progress page: `/my-progress/` (to check balance and rank)
- Redemption area: Wherever XP spending is configured (likely WooCommerce products)
- Profile page: `/members/username/` (to verify rank)

**Steps:**
1. **Insufficient XP Test:** Try to redeem an item with insufficient XP - should show error message
2. **Rank Requirement Test:** Try to redeem rank-locked item without required rank - should be blocked
3. **Usage Limit Test:** Try to redeem limited item after max usage reached - should be prevented
4. **Verify Protections:** All safeguards should prevent invalid redemptions with clear error messages

**Expected Result:** Proper validation prevents invalid redemptions with appropriate error messages.

---

## 5. Leaderboards & Visibility Testing

### Test Global Leaderboard
**Location:** Leaderboard page you created with shortcode
**Pages to Visit:**
- Global Leaderboard: `/leaderboard/` (the page with `[gamerz_leaderboard]` shortcode)
- XP Progress page: `/my-progress/` (to verify your own XP)

**Steps:**
1. **Visit Leaderboard Page:** Go to your leaderboard page at `/leaderboard/`
2. **Verify Content:** Check that the page displays users ranked by total XP
3. **Check Top Players:** Verify top 10 players are displayed with names, XP, and ranks
4. **Test Sorting:** Ensure rankings are based on total XP in descending order
5. **Check Accuracy:** Compare with your XP at `/my-progress/` to verify display accuracy

**Expected Result:** Accurate global leaderboard showing top players with names, XP, and ranks.

### Test Guild Leaderboard
**Location:** Guild page with specific leaderboard shortcode
**Pages to Visit:**
- Guild Management: `/guild-management/` (to identify your guild ID)
- Guild Leaderboard: Create a specific page with `[gamerz_leaderboard type="guild" guild_id="XX"]` shortcode
- Alternative: Use URL parameter if supported: `/leaderboard/?type=guild&guild_id=XX`

**Steps:**
1. **Identify Your Guild:** Go to `/guild-management/` to find your guild ID
2. **Create Guild Board:** Create a page with `[gamerz_leaderboard type="guild" guild_id="YOUR_GUILD_ID"]`
3. **Visit Guild Board:** Navigate to your guild-specific leaderboard page
4. **Verify Membership:** Confirm only members of that specific guild appear on the list
5. **Check Sorting:** Ensure ranking is based on XP within that guild only

**Expected Result:** Accurate guild-specific leaderboard showing only members of that guild ranked by guild XP.

### Test Profile XP Display
**Location:** User profile pages
**Pages to Visit:**
- Your Profile: `/members/yourusername/` or author page
- Other Profiles: `/members/otherusername/` or other author pages
- XP Progress: `/my-progress/`

**Steps:**
1. **Visit Profile:** Go to a user profile page (either yours or another's)
2. **Find XP Section:** Look for the prominent XP total display
3. **Check Progress Bar:** Verify XP progress bar shows current progress to next rank
4. **Current Rank:** Confirm current rank is displayed
5. **Next Rank Info:** Verify next rank and XP needed are shown
6. **Compare Values:** Cross-check with `/my-progress/` for accuracy

**Expected Result:** Clear XP information on all user profiles including total XP, current rank, progress bar, and next rank info.

### Test Forum Rank Display
**Location:** bbPress forums
**Pages to Visit:**
- Forum index: `/forums/`
- Specific topic: `/forums/topic-topic-name/`
- Forum profile: `/forums/participants/` (if available)

**Steps:**
1. **Navigate to Forums:** Go to your forum section at `/forums/`
2. **Check Post Display:** Look at any forum topic `/forums/topic-topic-name/` to see rank under usernames
3. **Verify Formatting:** Confirm rank appears under usernames in a color-coded format
4. **Consistency Test:** Check multiple forum threads to ensure rank display is consistent
5. **Color Verification:** Verify that different ranks display with appropriate color coding

**Expected Result:** Consistent rank display under usernames in all forum posts with appropriate color coding.

---

## 6. Weekly Challenges Testing

### Test Challenge Display
**Location:** Weekly challenges page you created
**Pages to Visit:**
- Weekly Challenges: `/weekly-challenges/` (the page with `[gamerz_weekly_challenges]` shortcode)
- XP Progress: `/my-progress/` (to verify XP rewards)

**Steps:**
1. **Visit Challenges Page:** Go to your weekly challenges page at `/weekly-challenges/`
2. **Verify Display:** Check that current weekly challenges are displayed clearly
3. **Check Challenge Types:** Verify you see 3 different challenge types (social, creative, competitive)
4. **Reward Verification:** Confirm reward amounts are displayed (e.g., +50 XP, +100 XP)
5. **Objective Clarity:** Check that challenge descriptions and objectives are clear and actionable

**Expected Result:** Clear display of current weekly challenges with rewards and descriptions on `/weekly-challenges/`.

### Test Challenge Completion
**Location:** Weekly challenges page
**Pages to Visit:**
- Weekly Challenges: `/weekly-challenges/`
- XP Progress: `/my-progress/` (to verify XP reward)
- Profile: `/members/username/` (to check for challenge-related badges)

**Steps:**
1. **Go to Challenges:** Navigate to `/weekly-challenges/`
2. **Select Challenge:** Choose a doable challenge like "Squad Up with a Newbie"
3. **Complete Task:** Perform the required action (e.g., team up with a newbie)
4. **Click Complete:** On `/weekly-challenges/`, click "Mark Complete" button for that challenge
5. **Verify Completion:** Check that challenge is marked as completed on the page
6. **Check XP Reward:** Visit `/my-progress/` to confirm XP reward was added to your balance

**Expected Result:** Challenge completion recorded on `/weekly-challenges/`, XP awarded and visible on `/my-progress/`.

### Test Proof Submission Challenges
**Location:** Weekly challenges page for challenges requiring proof
**Pages to Visit:**
- Weekly Challenges: `/weekly-challenges/`
- My Challenges: `/my-challenges/` (to see submission status)

**Steps:**
1. **Find Proof Challenge:** On `/weekly-challenges/`, locate a challenge requiring proof (e.g., "Clip Contest")
2. **Click Submit Proof:** Click the "Submit Proof" button associated with that challenge
3. **Enter Details:** Fill in proof details in the modal that appears
4. **Submit for Review:** Complete the submission process
5. **Check Confirmation:** Verify confirmation message appears
6. **Track Status:** Visit `/my-challenges/` to check proof submission status

**Expected Result:** Proof submission accepted via modal on `/weekly-challenges/`, status trackable on `/my-challenges/`.

### Test Challenge History
**Location:** My challenges page you created
**Pages to Visit:**
- My Challenges: `/my-challenges/` (the page with `[gamerz_my_challenges]` shortcode)
- Weekly Challenges: `/weekly-challenges/` (to compare with current challenges)

**Steps:**
1. **Visit History Page:** Go to your challenge history at `/my-challenges/`
2. **Check Completed List:** Verify all previously completed challenges are listed
3. **Date Verification:** Confirm completion dates are displayed accurately
4. **XP Tracking:** Check that XP earned from past challenges is visible
5. **Compare with Current:** Go back to `/weekly-challenges/` and compare with current challenges

**Expected Result:** Complete history of past challenges with dates and XP earned visible on `/my-challenges/`.

---

## 7. Discord Integration Testing

### Test Rank Up Announcements
**Location:** Discord server and WordPress admin settings
**Pages to Visit:**
- WordPress Admin: `/wp-admin/admin.php?page=gamerz-guild-settings`
- XP Progress: `/my-progress/` (to track rank progression)
- Profile: `/members/username/` (to see rank display)

**Steps:**
1. **Configure Discord:** Ensure webhook is configured in WordPress Admin at `/wp-admin/admin.php?page=gamerz-guild-settings`
2. **Earn Rank Up:** Gain enough XP to reach a new rank (check `/my-progress/` to track)
3. **Check Discord:** Look in your Discord server (typically in #rank-up-log or configured announcement channel)
4. **Verify Format:** Confirm message format: "@user has ascended to [Rank Name]!" with proper formatting
5. **Timing Check:** Ensure announcement appears shortly after rank achievement

**Expected Result:** Automatic rank up announcement with user mention and rank name in Discord.

### Test Badge Announcements
**Location:** Discord server and user profile
**Pages to Visit:**
- Profile: `/members/username/` (to earn and see badges) 
- XP Progress: `/my-progress/` (to track XP from badge earning)

**Steps:**
1. **Earn a Badge:** Perform an action that earns a badge (e.g., first forum post → Forum Newbie)
2. **Check Profile:** Verify badge appears on your profile at `/members/username/`
3. **Monitor Discord:** Watch your Discord server for badge announcement
4. **Verify Format:** Check message format includes user mention, badge name, and proper embed styling
5. **Multiple Badges:** Test with different badge types to ensure all announce properly

**Expected Result:** Automatic badge achievement announcement with user mention and badge name in Discord.

### Test Guild Event Announcements
**Location:** Discord server and guild management
**Pages to Visit:**
- Guild Management: `/guild-management/` (to create/join guilds)
- XP Progress: `/my-progress/` (to track guild XP)

**Steps:**
1. **Create Guild:** Use `/guild-management/` to create a new guild
2. **Check Discord:** Look in Discord for "New Guild Formed!" announcement with proper formatting
3. **Join Guild:** Have other users join the guild via `/guild-management/`
4. **Monitor Announcements:** Verify "New Guild Member!" announcements appear for each join
5. **Guild Activities:** If you promote/demote members, check for related announcements

**Expected Result:** Guild creation and member join events automatically announced in Discord.

### Test Role Assignments
**Location:** Discord server and WordPress admin
**Pages to Visit:**
- WordPress Admin: `/wp-admin/admin.php?page=gamerz-guild-settings` (for role mapping)
- XP Progress: `/my-progress/` (to track rank progression)
- Profile: `/members/username/` (to see current rank)

**Steps:**
1. **Configure Roles:** Set up Discord role mappings in WordPress Admin at `/wp-admin/admin.php?page=gamerz-guild-settings`
2. **Reach Threshold:** Earn enough XP to reach a rank that has a Discord role mapped (e.g., Scrub Champion)
3. **Check Discord:** Verify that corresponding Discord role is automatically assigned to your account
4. **Verify Colors:** Confirm the role has appropriate color matching your rank level
5. **Role Hierarchy:** Check that only current rank role is active (old roles removed)

**Expected Result:** Discord roles automatically assigned based on site rank with appropriate colors and permissions.

---

## 8. Visual & UX Enhancements Testing

### Test XP Progress Bar
**Location:** User profiles, XP progress page, and forum areas
**Pages to Visit:**
- XP Progress page: `/my-progress/` (main progress bar display)
- Profile page: `/members/username/` (if XP bar is displayed there)
- Forum pages: `/forums/topic-name/` (if XP/rank shown in forum context)

**Steps:**
1. **Visit XP Progress Page:** Go to `/my-progress/` to see main XP progress bar
2. **Check Animation:** Perform an action that gains XP and look for progress bar animation
3. **Verify Smoothness:** Ensure progress bar fills smoothly without jumps or glitches
4. **Visual Effects:** Check for glow effects, pulsations, or other visual enhancements
5. **Responsiveness:** Test on different screen sizes to ensure visual quality is maintained

**Expected Result:** Smooth, animated progress visualizations on `/my-progress/` with glow and effects.

### Test Rank Avatar Indicators
**Location:** All areas displaying avatars
**Pages to Visit:**
- Profile pages: `/members/username/`
- Forum pages: `/forums/` and `/forums/topic-name/`
- Comments sections: Any page with user comments
- User lists: `/members/` (if using BuddyPress)

**Steps:**
1. **Navigate to Avatar Pages:** Visit pages showing user avatars (profile, forum, comments)
2. **Check Avatar Borders:** Verify avatar borders reflect rank level (thicker/different colors for higher ranks)
3. **Rank Badges:** Look for rank badge overlays on larger avatars (usually top-right corner)
4. **Consistent Colors:** Confirm rank colors are applied consistently across all avatar displays
5. **Size Variations:** Test with different avatar sizes to ensure indicators work properly at all sizes

**Expected Result:** Visual rank indicators (border styling and badge overlays) on all user avatars across all pages.

### Test Achievement Animations
**Location:** Any page where achievements are earned (XP Progress, Profile)
**Pages to Visit:**
- XP Progress: `/my-progress/` (to trigger/see achievements)
- Profile page: `/members/username/` (to earn badges/see effects)
- Forum pages: `/forums/` (for forum-related achievements)

**Steps:**
1. **Earn Achievement:** Perform an action that awards a badge or achievement
2. **Look for Effects:** Watch for confetti effects, animations, or celebration popups
3. **Notification System:** Check for achievement notification popups in top-right/bottom corner
4. **Visual Feedback:** Verify there are visual celebration effects when achievements unlock
5. **Browser Compatibility:** Test on different browsers to ensure animations work properly

**Expected Result:** Visual celebration effects (confetti, popups, or notifications) when achievements are unlocked.

### Test HUD-Style Elements
**Location:** XP progress and rank display areas
**Pages to Visit:**
- XP Progress: `/my-progress/`
- Profile pages: `/members/username/`
- Forum pages: `/forums/` (for rank displays under names)
- Leaderboard: `/leaderboard/` (if UI enhancements apply)

**Steps:**
1. **View XP Elements:** Look at XP progress bars, rank displays, and other gamification UI elements
2. **Check Styling:** Verify neon/cyberpunk styling is applied (glow effects, specific color schemes)
3. **Glow Effects:** Confirm glow and HUD-style effects are visible and not too flashy
4. **Mobile Testing:** Check that visual elements are responsive and look good on mobile devices
5. **Performance:** Ensure visual effects don't negatively impact page loading speed

**Expected Result:** Game-like HUD styling throughout the experience with appropriate glow effects and cyberpunk aesthetics.

---

## Complete Testing Checklist

### Essential Features
- [ ] Daily login XP (5 XP) - **Test:** Login to site, **Verify:** `/my-progress/` shows +5 XP
- [ ] Forum topic XP (8 XP) - **Test:** Create topic at `/forums/`, **Verify:** `/my-progress/` shows +8 XP  
- [ ] Forum reply XP (5 XP) - **Test:** Reply in topic at `/forums/topic-name/`, **Verify:** `/my-progress/` shows +5 XP
- [ ] Social actions (1-2 XP) - **Test:** Like/post/reaction at `/forums/`, **Verify:** `/my-progress/` shows +1-2 XP
- [ ] Content submission (20 XP) - **Test:** Create post at `/wp-admin/post-new.php`, **Verify:** `/my-progress/` shows +20 XP
- [ ] Event participation (15 XP) - **Test:** Register at `/events/event-name/`, **Verify:** `/my-progress/` shows +15 XP
- [ ] Tournament victory (50 XP) - **Test:** Win event, **Verify:** `/my-progress/` shows +50 XP
- [ ] Guild creation (50 XP) - **Test:** Create at `/guild-management/`, **Verify:** `/my-progress/` shows +50 XP
- [ ] Guild joining (10 XP) - **Test:** Join at `/guild-management/`, **Verify:** `/my-progress/` shows +10 XP

### Rank System
- [ ] Rank updates when XP thresholds reached - **Test:** Earn XP, **Verify:** `/my-progress/` and profile page show updated rank
- [ ] Rank displayed on user profiles - **Test:** Visit profile, **Verify:** `/members/username/` shows rank prominently
- [ ] Rank shown in forums under usernames - **Test:** Make forum post, **Verify:** `/forums/topic-name/` shows rank under username
- [ ] Discord role sync working - **Test:** Reach rank threshold, **Verify:** Discord server shows updated role
- [ ] Rank privileges unlocked - **Test:** Reach each rank, **Verify:** Profile/guild/forum features unlock

### Badges System  
- [ ] Auto-badges earned (Forum Newbie, Content Creator, etc.) - **Test:** Perform action, **Verify:** `/members/username/` shows new badge
- [ ] Manual badges can be awarded - **Test:** Admin interface `/wp-admin/users.php`, **Verify:** User profile shows badge
- [ ] Badges display on profiles - **Test:** Any user profile, **Verify:** `/members/username/` badges section visible
- [ ] Badge descriptions available - **Test:** Hover on badge, **Verify:** Description tooltip appears on profile

### Redemption System
- [ ] XP redemption accessible - **Test:** Navigate to WooCommerce shop, **Verify:** XP-redeemable items available
- [ ] Real-world rewards (WooCommerce coupons) - **Test:** Redeem 1000 XP for coupon, **Verify:** Code appears in `/my-account/`
- [ ] In-platform rewards available - **Test:** Spend XP on cosmetics, **Verify:** Features activate on your profile/forum
- [ ] Proper validation works - **Test:** Insufficient XP/rank, **Verify:** Clear error messages shown

### Leaderboards
- [ ] Global leaderboard functional - **Test:** Visit, **Verify:** `/leaderboard/` shows top players ranked by XP
- [ ] Guild-specific leaderboard functional - **Test:** Visit guild page, **Verify:** Guild members ranked by guild XP
- [ ] Profile XP display working - **Test:** Visit any profile, **Verify:** `/members/username/` shows XP and rank clearly
- [ ] Forum rank display working - **Test:** Visit forum, **Verify:** `/forums/topic-name/` shows ranks under usernames

### Weekly Challenges
- [ ] Challenges display properly - **Test:** Visit, **Verify:** `/weekly-challenges/` shows current challenges with rewards
- [ ] Challenge completion working - **Test:** Complete challenge, **Verify:** `/weekly-challenges/` shows completion, XP on `/my-progress/`
- [ ] Proof submission available - **Test:** Submit proof, **Verify:** Modal appears, confirmation received
- [ ] History tracking - **Test:** Complete challenges, **Verify:** `/my-challenges/` shows complete history

### Discord Integration
- [ ] Rank up announcements in Discord - **Test:** Reach new rank, **Verify:** Discord server receives announcement
- [ ] Badge earned announcements in Discord - **Test:** Earn badge, **Verify:** Discord server receives notification
- [ ] Role assignments based on rank - **Test:** Reach mapped rank, **Verify:** Discord role assigned automatically
- [ ] Guild event announcements - **Test:** Create/join guild, **Verify:** Discord server receives event announcement

### Visual Enhancements
- [ ] XP progress bars with animations - **Test:** Gain XP, **Verify:** `/my-progress/` shows animated progress bar
- [ ] Rank indicators on avatars - **Test:** Visit pages with avatars, **Verify:** `/members/username/`, `/forums/` show rank styling
- [ ] Achievement animations - **Test:** Unlock achievement, **Verify:** Visual celebration effects appear
- [ ] HUD-style elements - **Test:** Visit XP/rank pages, **Verify:** `/my-progress/`, `/leaderboard/` show game-style styling

---

## Troubleshooting Common Issues

### XP Not Awarding
**Symptoms:** User performs action but XP doesn't increase
**Solutions:**
- Verify myCRED plugin is active and configured
- Check that hooks are properly connected in the plugin
- Ensure user is logged in when performing actions
- Verify daily caps aren't preventing awards

### Rank Not Updating
**Symptoms:** XP increases but rank doesn't change
**Solutions:**
- Check that XP thresholds match the defined rank system
- Verify the rank update hook is connected to myCRED balance updates
- Ensure the user has enough XP to reach the next rank threshold

### Badges Not Awarding
**Symptoms:** Badge criteria met but badge not received
**Solutions:**
- Verify badge criteria match the actual actions
- Check that auto-badge hooks are properly connected
- Ensure user permissions allow badge acquisition

### Discord Integration Not Working
**Symptoms:** No announcements or role changes in Discord
**Solutions:**
- Verify webhook URL and bot token in settings
- Confirm role mappings are properly configured
- Check Discord permissions for the bot
- Verify action hooks are connected to Discord announcements

---

## Performance Testing

### Load Testing
1. Test with 100+ active users earning XP simultaneously
2. Verify database performance doesn't degrade
3. Check that leaderboards load quickly with 1000+ users
4. Test guild management with 50+ members per guild

### Stress Testing
1. Simulate maximum XP earning rates
2. Test rapid challenge completion
3. Verify concurrent badge earning doesn't cause issues
4. Check redemption system under load

---

## Security Testing

### Authorization Testing
- Verify only logged-in users can earn XP
- Confirm only appropriate users can award manual badges
- Check that rank privileges are properly enforced
- Validate that XP redemption requires proper permissions

### Input Validation
- Test for XSS in user-generated content that triggers XP
- Verify sanitization of user inputs in forms
- Check that AJAX calls require proper nonces
- Confirm database inputs are properly escaped

### Data Integrity
- Verify user XP cannot be modified by other users
- Confirm badges can only be awarded to correct users
- Check that redemption system prevents duplicate rewards
- Verify rank progression cannot be bypassed

---

## Quality Assurance Final Checklist

### Before Production Deployment
- [ ] All XP earning methods tested and working
- [ ] Rank progression tested from 0 to max rank
- [ ] All badges tested (auto and manual)
- [ ] Redemption system tested with real rewards  
- [ ] All leaderboards display correctly
- [ ] Weekly challenges cycle and reset properly
- [ ] Discord integration fully functional
- [ ] Visual elements work across all browsers
- [ ] Performance benchmarks met
- [ ] Security vulnerabilities addressed
- [ ] User experience tested end-to-end

### Post-Deployment Monitoring
- [ ] Monitor XP logs for unusual patterns
- [ ] Track badge awarding rates
- [ ] Monitor leaderboard accuracy
- [ ] Check Discord integration logs
- [ ] Gather user feedback on gamification features
- [ ] Track redemption rates and adjust as needed
- [ ] Monitor system performance under real usage

This comprehensive testing guide ensures all aspects of the Scrub Gamerz Gamification System are thoroughly verified before and after deployment.