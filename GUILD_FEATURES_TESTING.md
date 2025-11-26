# Guild Features Testing Guide

## Overview
This document provides comprehensive testing procedures for all guild-related features in the Gamerz Guild plugin. Each feature is thoroughly tested to ensure proper functionality.

## Guild Feature Requirements
The guild system includes these core features:
- Create/join/leave guilds
- Guild forums (integration with existing forum system)
- Guild member management
- Guild activity feed
- Guild events (integration with existing event system)

## Prerequisites
- WordPress with Gamerz Guild plugin active
- BuddyPress plugin installed and active
- bbPress plugin installed and active (for guild forums)
- The Events Calendar plugin installed and active (for guild events) - optional
- myCRED plugin installed and active (for XP system)

## Setup for Testing
Before beginning tests, ensure the following:
1. **Administrator**: Log in to WordPress admin dashboard at `yoursite.com/wp-admin`
2. **Verify Plugin Activation**: Go to `Plugins` menu in WordPress admin, ensure "Gamerz Guild" is activated
3. **Verify Prerequisites**: Check that BuddyPress, bbPress, and myCRED plugins are also activated
4. **Create Test Page**:
   - Go to `Pages` → `Add New` in WordPress admin
   - Add the shortcode `[gamerz_guild_management]` to the page content
   - Publish the page and note the URL
   - Example: `yoursite.com/guild-management/`
5. **User Access**: End users will access guild features by visiting the page where you placed the shortcode

---

## User Navigation Guide

### For End Users (Frontend Access):
- **To Access Guild Features**: Visit the page URL where you added `[gamerz_guild_management]` shortcode
- **Example**: Go to `yoursite.com/guild-management/` (or whatever page you created)
- **Required**: Must be logged in as a WordPress user
- **Not Available**: Guild features are NOT accessible from WordPress admin for regular users

### For Administrators (Admin Access):
- **Plugin Settings**: Go to `Gamerz Guild` → `Settings` in WordPress admin menu
- **Guild Management**: Go to `Gamerz Guild` → `Guilds` to view all created guilds
- **Challenges Management**: Go to `Gamerz Guild` → `Challenges` to manage weekly challenges
- **Dashboard**: Go to `Gamerz Guild` → `Dashboard` for overall system statistics

### Who Can Be in a Guild:
- **Any registered WordPress user** who is logged in can join or create a guild
- **Requirement**: User cannot be in multiple guilds simultaneously (one guild per user)
- **Restriction**: Users must not already be in another guild to join a new one

---

## Guild Creation Testing

### Test 1: Guild Creation Functionality
**Objective:** Verify guild creation works properly

**Prerequisites:**
- User must be logged in to the website
- User must NOT already be in a guild (check by visiting the guild management page first)
- A frontend page must exist containing the `[gamerz_guild_management]` shortcode
- **User Location**: Frontend page with guild shortcode (NOT WordPress admin)

**Steps:**
1. **Navigate to Guild Page**: Go to your frontend URL where guild shortcode is placed (e.g., `yoursite.com/guild-management/`)
2. **Verify Login**: Confirm you're logged in (should see your username at top of page)
3. **Verify Not in Guild**: Check that you're not already in a guild (you should see the "Create New Guild" section)
4. **Locate Form**: Find the "Create New Guild" section with form fields
5. **Fill Details**:
   - Guild Name: Enter a unique name (required, 1-100 characters)
   - Description: Enter description (optional)
   - Tagline: Enter tagline (optional)
   - Max Members: Enter number between 5-100 (default is 20)
6. **Submit**: Click the "Create Guild" button (green with "+" icon)
7. **Confirm Success**: Look for success message and page refresh showing your new guild

**Expected Results:**
- Guild post is created with post type 'guild' in WordPress admin
- You are assigned as guild leader
- You receive 50 XP (check by viewing your profile or myCRED logs)
- Guild appears in guild listings on the management page
- Guild activity feed shows creation event
- Page automatically refreshes to show your new guild details
- You can access guild management features as leader

**Backend Verification:**
- Check in WordPress admin under `Gamerz Guild` → `Guilds` that your guild exists
- Check wp_postmeta table for meta fields: `_guild_tagline`, `_guild_description`, `_guild_max_members`, `_guild_creator_id`, `_guild_status`
- Check wp_usermeta table for your user having `_guild_role_XXX` set to "leader"
- Check myCRED logs show 50 XP awarded for "Created a guild"

---

### Test 2: Guild Creation Validation
**Objective:** Verify form validation works correctly

**Prerequisites:**
- User must be logged in to the website
- A frontend page must exist containing the `[gamerz_guild_management]` shortcode
- User must NOT already be in a guild

**Steps:**
1. **Navigate to Guild Page**: Go to your frontend URL with guild shortcode (e.g., `yoursite.com/guild-management/`)
2. **Test Invalid Data Scenarios**:
   - Scenario A: Leave "Guild Name" field empty, click "Create Guild" → Should show error "Guild name is required"
   - Scenario B: Enter Max Members as "3" (less than minimum 5) → Should show validation error
   - Scenario C: Enter Max Members as "150" (more than maximum 100) → Should show validation error
3. **Test Security** (Admin only): In browser console, try calling AJAX directly without login/nonces → Should fail
4. **Test Guild Restriction**: If you've already created a guild, try creating another → Should show "You are already in a guild"

**Expected Results:**
- Clear error messages for each invalid data scenario
- Form rejects invalid data submissions
- Security measures prevent unauthorized creation
- "You are already in a guild" error when attempting to create multiple guilds

---

## Guild Join/Leave Testing

### Test 3: Guild Join Functionality
**Objective:** Verify guild joining works

**Prerequisites:**
- User must be logged in to the website
- User must NOT already be in a guild (check by visiting guild page first - should see "Create New Guild" option)
- At least one guild must exist on the site (created by another user or admin)
- A frontend page must exist containing the `[gamerz_guild_management]` shortcode
- **User Location**: Frontend page with guild shortcode (NOT WordPress admin)

**Steps:**
1. **Navigate to Guild Page**: Go to your frontend URL with guild shortcode (e.g., `yoursite.com/guild-management/`)
2. **Verify Current Status**: Confirm you're not in a guild (should see "Create New Guild" and "Available Guilds" sections)
3. **Locate Available Guilds**: Find the "Available Guilds" section with the guild listing table
4. **Check Guild Availability**: Find a guild where "Members" column shows "X/Y" where X < Y (available slots)
5. **Join Guild**: Click the "Join" button in the "Action" column for your chosen guild
6. **Confirm Success**: Look for confirmation message like "Successfully joined the guild"
7. **Verify Change**: Page should refresh to show your new guild details instead of the join options

**Expected Results:**
- You are added to the selected guild member list
- You receive 10 XP for joining (check your XP on the updated page or myCRED logs)
- Guild activity feed records the new member event
- You can access guild-specific features
- The join button changes to "Already Member" for your user
- Page automatically refreshes to show your current guild information

**Backend Verification:**
- Check wp_postmeta table: your user ID is in the guild's `_guild_members` array
- Check wp_usermeta table: you have `_guild_role_XXX` set to "member" for that guild
- Check myCRED logs: 10 XP awarded for "Joined a guild"
- Check guild's `_guild_activities` meta shows new member event

---

### Test 4: Guild Join Validation
**Objective:** Verify join restrictions work

**Prerequisites:**
- User must be logged in to the website
- A frontend page must exist containing the `[gamerz_guild_management]` shortcode

**Steps:**
1. **Test Duplicate Join**:
   - **Location**: Frontend guild page
   - **Action**: If already in a guild, try to join another guild
   - **Expected**: Should show "You are already in a guild" error

2. **Test Full Guild**:
   - **Location**: Frontend guild page
   - **Action**: Find a guild where Members shows "Y/Y" (like "15/15") and click Join
   - **Expected**: Should show "Guild is at maximum capacity" or "Guild Full" indicator

3. **Test Security** (Admin only):
   - **Location**: Browser console
   - **Action**: Try to call wp_ajax_guild_join directly without proper nonce/login
   - **Expected**: Should return "Security check failed" or "You must be logged in"

4. **Test Not Logged In** (Simulated):
   - **Location**: Browser with logged-out state
   - **Action**: Try to join guild (would require direct AJAX call in reality)
   - **Expected**: Should fail with login requirement

**Expected Results:**
- Clear error messages for each restriction scenario
- Duplicate memberships are prevented
- Full guilds show "Guild Full" status instead of join button
- Security measures block unauthorized access

---

### Test 5: Guild Leave Functionality
**Objective:** Verify guild leaving works

**Prerequisites:**
- User must be logged in to the website
- User must currently be in a guild (not the guild leader if other members exist)
- A frontend page must exist containing the `[gamerz_guild_management]` shortcode
- **User Location**: Frontend page with guild shortcode (NOT WordPress admin)

**Steps:**
1. **Navigate to Guild Page**: Go to your frontend URL with guild shortcode (e.g., `yoursite.com/guild-management/`)
2. **Verify Current Guild**: Confirm you're in a guild (should see "Current Guild" section with guild details)
3. **Check Role**: Verify you're NOT the guild leader (or are the leader but no other members exist)
4. **Locate Leave Button**: Find the red "Leave Guild" button in the guild management section
5. **Click and Confirm**: Click "Leave Guild", then confirm the browser popup
6. **Verify Success**: Look for success message and page refresh

**Expected Results:**
- You are removed from the guild member list
- Your guild role is cleared from user meta
- Guild activity feed shows member left event
- Page refreshes to show "Create New Guild" and "Available Guilds" options again
- You can create or join another guild
- You lose access to guild-specific features

**Backend Verification:**
- Check wp_postmeta: your ID is removed from guild's `_guild_members` array
- Check wp_usermeta: `_guild_role_XXX` entry is deleted for that guild
- Check guild's `_guild_activities` shows member departure event

---

### Test 6: Guild Leader Leave Validation
**Objective:** Verify guild leader cannot leave when other members exist

**Prerequisites:**
- User must be logged in to the website
- User must be the leader of a guild
- Guild must have other members (more than just the leader)
- A frontend page must exist containing the `[gamerz_guild_management]` shortcode
- **User Location**: Frontend page with guild shortcode (NOT WordPress admin)

**Steps:**
1. **Navigate to Guild Page**: Go to your frontend URL with guild shortcode (e.g., `yoursite.com/guild-management/`)
2. **Verify Leadership**: Confirm you're the guild leader (check your role) and other members exist
3. **Look for Leave Option**: Check if "Leave Guild" button is available or if restriction message shows
4. **Attempt to Leave**: Try clicking the leave button if it's visible
5. **Check Validation**: Look for the restriction message

**Expected Results:**
- You cannot leave the guild while other members exist
- Clear message appears: "Transfer leadership before leaving" or similar text
- Leadership must be transferred to another member before leaving
- The leader must promote someone else to leader first, or wait for other members to leave

**Backend Verification:**
- Your user role remains "leader" in `_guild_role_XXX` meta
- Guild members list still includes your ID
- No departure activity is logged
- System enforces the business rule that leaders can't abandon guilds with members

---

## Guild Member Management Testing

### Test 7: Member Role Management
**Objective:** Verify guild leader can manage member roles

**Prerequisites:**
- User must be logged in to the website as guild leader
- Guild must have other members (not just the leader)
- A frontend page must exist containing the `[gamerz_guild_management]` shortcode
- **User Location**: Frontend page with guild shortcode (NOT WordPress admin)
- **Role Requirement**: You must be the guild leader (check by visiting the guild page)

**Steps:**
1. **Navigate to Guild Page**: Go to your frontend URL with guild shortcode (e.g., `yoursite.com/guild-management/`)
2. **Verify Leadership**: Confirm you're the guild leader (should see "Guild Administration" section with management buttons)
3. **Access Management**: Click the "Manage Members" button (appears in guild admin section for leaders)
4. **Modal Opens**: Verify the member management modal appears showing all guild members
5. **Select Member**: Find a member who is currently a "member" (not leader or officer)
6. **Promote Member**: Click the "Promote" button next to that member
7. **Verify Promotion**: Modal should refresh showing member now has "officer" role
8. **Test Demotion**: Click the "Demote" button next to the same member (now officer)
9. **Verify Demotion**: Modal should refresh showing member back to "member" role

**Expected Results:**
- Member roles update appropriately (member → officer, officer → member)
- Other members see role changes after page refresh
- Guild activity feed logs promotion/demotion events
- Changes persist across page refreshes
- Members' permissions within guild update with their roles

**Backend Verification:**
- Check wp_usermeta table: `_guild_role_XXX` updates correctly when promoting/demoting
- Check guild's `_guild_activities` meta shows promotion/demotion events
- Verify role changes persist in database after page refresh

---

### Test 8: Member Removal (Kick Feature)
**Objective:** Verify guild leaders can remove members from guild

**Prerequisites:**
- User must be logged in to the website as guild leader
- Guild must have other members who are NOT leaders
- A frontend page must exist containing the `[gamerz_guild_management]` shortcode
- **User Location**: Frontend page with guild shortcode (NOT WordPress admin)

**Steps:**
1. **Navigate to Guild Page**: Go to your frontend URL with guild shortcode (e.g., `yoursite.com/guild-management/`)
2. **Verify Leadership**: Confirm you're the guild leader
3. **Open Member Management**: Click the "Manage Members" button
4. **Modal Opens**: Verify the member management modal appears
5. **Select Member to Remove**: Find a member who is NOT the leader (should have "Kick" button available)
6. **Click Kick**: Click the "Kick" button next to the selected member
7. **Confirm Action**: Confirm the browser popup asking "Are you sure you want to kick this member?"
8. **Verify Removal**: Modal should refresh without the kicked member
9. **Check Guild Page**: Close modal and verify guild member count decreased

**Expected Results:**
- Selected member is removed from guild
- Removed member loses access to guild features
- Guild activity feed records the removal event
- Removed member's guild role is cleared from their user meta
- Guild member count updates correctly
- Kicked member can no longer access guild-specific features

**Backend Verification:**
- Check wp_postmeta: kicked member ID removed from guild's `_guild_members` array
- Check wp_usermeta: `_guild_role_XXX` entry cleared for kicked member
- Check guild's `_guild_activities` meta shows member removal event

---

### Test 9: Permission Validation (Non-Leader Security)
**Objective:** Verify regular guild members cannot manage members

**Prerequisites:**
- User must be logged in to the website as a regular guild member (NOT leader or officer)
- Guild must have other members
- A frontend page must exist containing the `[gamerz_guild_management]` shortcode
- **User Location**: Frontend page with guild shortcode (NOT WordPress admin)

**Steps:**
1. **Navigate as Regular Member**: Log in as a regular guild member (not leader/officer), go to guild page
2. **Check for Management Buttons**: Look for "Guild Administration" section or "Manage Members" button
3. **Verify No Access**: Confirm you don't see member management options
4. **Test Direct Access** (Admin only): Try accessing the functionality via browser console/developer tools
5. **Attempt AJAX Calls** (Admin only): Try calling guild management AJAX endpoints directly

**Expected Results:**
- "Guild Administration" section and "Manage Members" button are NOT visible to regular members
- Member management buttons (Promote, Demote, Kick) do NOT appear for regular members
- Security measures prevent unauthorized access to management features
- AJAX calls from non-leaders fail with permission errors
- Role-based access control functions properly

**Backend Verification:**
- Server-side checks verify user role before allowing management actions
- wp_ajax handlers check for leader role before executing management functions
- No unauthorized role changes occur in wp_usermeta table

---

## Guild Activity Feed Testing

### Test 10: Activity Feed Generation
**Objective:** Verify guild activity feed tracks all guild activities

**Prerequisites:**
- Guild must exist where you can perform actions
- System must be tracking guild activities (default behavior)
- **User Location**: Frontend page with guild shortcode (NOT WordPress admin)

**Steps:**
1. **Perform Guild Actions**: Do several guild operations on the frontend guild page:
   - Create a guild (if you're the leader)
   - Have another user join your guild
   - Promote a member to officer
   - Demote an officer back to member
   - Have a member leave the guild
2. **Check Backend Activity Tracking**: Access the database or WordPress admin to view activity
   - Go to WordPress admin → Gamerz Guild → Guilds
   - View the specific guild's activity (through custom fields or via database)
3. **Verify Activity Records**: Confirm each action created an activity log entry
4. **Check Activity Details**: Each activity should include proper user info and timestamps

**Expected Results:**
- Guild creation event is logged in guild's activity feed
- New member joining is logged with user details
- Member promotions are logged with user details
- Member demotions are logged with user details
- Member departures are logged with user details
- Each activity has proper timestamp and user information
- Maximum 50 activities maintained (older activities are purged to prevent database bloat)

**Backend Verification:**
- Check wp_postmeta table: guild's `_guild_activities` field contains activity records
- Verify each activity has proper structure: {type, user_id, content, timestamp}
- Confirm system only keeps last 50 activities (older ones are removed)

---

### Test 11: Guild Forum Connection
**Objective:** Verify guild integration with bbPress forums

**Prerequisites:**
- bbPress plugin must be active and configured
- Guild must exist and user must be in the guild
- User must be logged in to the website
- **User Location**: bbPress forum sections of the website (NOT WordPress admin)

**Steps:**
1. **Ensure Prerequisites**: Confirm bbPress is active in WordPress admin under Plugins
2. **Join or Create Guild**: Be in a guild by visiting your guild page and confirming membership
3. **Navigate to Forums**: Go to your website's forum section (e.g., `yoursite.com/forums/`)
4. **Make Forum Post**: Create a new topic or reply in any forum as a guild member
5. **Check Rank Display**: Look for your rank displayed under your username in the forum post
6. **Verify XP Award**: Check that you received XP for the forum activity (topic = 8 XP, reply = 5 XP)

**Expected Results:**
- Your guild rank displays under your username in forum posts (e.g., "Scrub Soldier")
- Rank appears with proper styling/colors based on your rank level
- You receive XP for forum activities (topics and replies) as per system settings
- Rank privileges apply to forum features when appropriate
- Guild-specific forum permissions work if implemented

**Backend Verification:**
- Check myCRED logs for XP awarded from forum activity
- Verify rank display functions are properly connected to bbPress templates
- Confirm XP hooks trigger correctly from forum posts by guild members

---

### Test 12: Forum Rank Integration
**Objective:** Verify guild ranks display properly in forums

**Prerequisites:**
- bbPress plugin active
- Multiple users with different guild ranks
- Users must be logged in to the website

**Steps:**
1. **Navigate to Forums**: Go to your website's forum section (e.g., `yoursite.com/forums/`)
2. **Check Different Users**: Look at forum posts made by users with different guild ranks
3. **Verify Rank Visibility**: Confirm each user's rank displays clearly under their name
4. **Test Different Ranks**: Verify ranks from "Scrubling" to "Legendary Scrub" display properly
5. **Check Rank Styling**: Confirm rank styling/privileges apply in forum context

**Expected Results:**
- User ranks clearly display under usernames in forum posts
- Different rank levels show appropriate visual styling
- Rank privileges (signatures, posting rights) apply to forum features
- XP progress shows in forum user profile sections
- Rank colors and styling apply consistently across all forum areas

---

## Guild Events Integration Testing

### Test 13: Guild Events Integration (The Events Calendar)
**Objective:** Verify guild integration with The Events Calendar plugin

**Prerequisites:**
- The Events Calendar plugin must be active
- User must be logged in to the website
- Guild must exist (for guild-specific events)
- **User Location**: Event sections of the website (NOT WordPress admin)

**Steps:**
1. **Verify Plugin**: Confirm The Events Calendar is active in WordPress admin
2. **Event Participation**: Register for events on your site as a guild member
3. **Check XP Award**: Verify you receive XP for event participation (15 XP) or victory (50 XP)
4. **Create Guild Events** (if functionality exists): Test creating events as a guild leader
5. **Check Guild Event Tracking**: Verify guild events are associated with your guild

**Expected Results:**
- Event participation awards 15 XP per event attended
- Event victories award 50 XP
- Events appear in user's event history
- Guild leader can create guild-specific events (if this functionality exists)
- Event participation tracks correctly with guild membership

**Backend Verification:**
- Check myCRED logs for XP awarded from events
- Verify event attendance tracked in user meta
- Confirm guild event integration works if implemented

---

### Test 14: Event XP Validation
**Objective:** Verify XP system works correctly with events

**Prerequisites:**
- The Events Calendar active
- myCRED plugin active
- User logged in

**Steps:**
1. **Attend an Event**: Register and attend an event on your site
2. **Check XP Award**: Verify correct XP amount is awarded (15 for participation)
3. **Track XP History**: Check myCRED logs for event-related XP entries
4. **Verify Limits**: Confirm daily XP caps don't affect event rewards if applicable

**Expected Results:**
- 15 XP awarded for event participation
- 50 XP awarded for event victories
- XP entries properly logged with event references
- No duplicate XP awards for same event

---

## Frontend Interface Testing

### Test 15: Guild Management Shortcode Display
**Objective:** Verify guild management interface displays correctly

**Prerequisites:**
- WordPress page with `[gamerz_guild_management]` shortcode exists
- User must be logged in to the website
- **User Location**: Frontend page containing guild management shortcode

**Steps:**
1. **Navigate to Guild Page**: Go to the URL where you placed the shortcode (e.g., `yoursite.com/guild-management/`)
2. **Check Page Load**: Verify page loads completely without errors
3. **Verify Interface**: Confirm guild management interface displays properly
4. **Test Different States**:
   - When not in guild: Should show "Create New Guild" and "Available Guilds"
   - When in guild: Should show current guild details and management options
5. **Check Responsiveness**: Test on mobile/desktop that interface displays correctly

**Expected Results:**
- Guild management container displays with proper styling (blue header, clean layout)
- Current guild information shows when in a guild
- Guild creation form shows when not in a guild
- Guild listing table shows available guilds with join buttons
- All UI elements display properly and are responsive
- JavaScript functionality works (buttons respond to clicks)

---

### Test 16: Shortcode Accessibility
**Objective:** Verify only logged-in users can access guild features

**Prerequisites:**
- Page with guild shortcode exists
- **User Location**: Frontend page with guild management shortcode

**Steps:**
1. **Test Logged Out**: Visit guild page while logged OUT of site → Should show login prompt only
2. **Test Logged In**: Visit guild page while logged IN → Should show full interface
3. **Test Different Roles**: Test with subscriber, contributor, admin accounts
4. **Verify Functionality**: Confirm all features work for logged-in users

**Expected Results:**
- Non-logged-in users see only login prompt
- Logged-in users see full guild management interface
- All user roles (except logged-out) can access basic guild features
- Interface adapts based on user's guild status (not in guild vs. in guild)

---

## Guild Data Integrity Testing

### Test 17: Guild Data Storage Verification
**Objective:** Verify all guild data is properly stored in database

**Prerequisites:**
- WordPress admin access for database verification
- User has created/joined a guild

**Steps:**
1. **Access WordPress Admin**: Log into WordPress admin at `yoursite.com/wp-admin/`
2. **Check Guild Post**: Go to `Gamerz Guild` → `Guilds` to see created guilds
3. **Database Verification** (Advanced users): Check wp_posts table for post_type = 'guild'
4. **Meta Fields Check**: Verify guild meta in wp_postmeta table:
   - `_guild_tagline`, `_guild_description`, `_guild_max_members`, `_guild_creator_id`, `_guild_status`
5. **User Meta Check**: In wp_usermeta table, verify:
   - `_guild_role_XXX` entries for guild members
   - `_guild_members` array in guild post meta

**Expected Results:**
- Guilds appear in `Gamerz Guild` → `Guilds` admin section
- Guild post type stores correctly as 'guild' in database
- All guild meta fields properly stored in post meta
- User guild roles correctly stored in user meta
- Member lists accurately maintained in guild meta

---

### Test 18: User Guild Meta Management
**Objective:** Verify user meta related to guilds works correctly

**Prerequisites:**
- Access to WordPress admin user management
- User has joined/left guilds

**Steps:**
1. **Join Guild**: Have user join a guild via frontend
2. **Check User Meta**: In WordPress admin, view user profile or database
3. **Verify Guild Assignment**: Confirm `_guild_role_XXX` meta exists where XXX is guild ID
4. **Leave Guild**: Have user leave guild
5. **Check Meta Cleanup**: Verify guild role meta is removed for that guild

**Expected Results:**
- `_guild_role_{guild_id}` meta created when joining a guild
- Meta removed when leaving guild
- Multiple guild roles handled separately (impossible per requirements, but if system changed)
- User meta persists across sessions correctly

---

## Guild Security Testing

### Test 19: Guild Access Security
**Objective:** Verify proper access controls prevent unauthorized guild actions

**Prerequisites:**
- WordPress admin access for verification
- Multiple user accounts
- **User Location**: Frontend guild management page

**Steps:**
1. **Verify Login Required**: Confirm guild features require login
2. **Test Role Restrictions**: Verify different user roles have appropriate access
3. **Check Guild Isolation**: Verify users can't access other guild data
4. **Test Nonce Security**: (Advanced) Verify AJAX calls require valid nonces
5. **Cross-User Access**: Verify one user can't modify another's guild data

**Expected Results:**
- Unauthorized access prevented for guild features
- Security checks (nonces, permissions) function properly
- No privilege escalation possible
- Users can only access their own guild-related data

---

### Test 20: AJAX Security Validation
**Objective:** Verify guild-related AJAX functions are secure against unauthorized access

**Prerequisites:**
- Technical knowledge for advanced testing
- Understanding of browser developer tools

**Steps:**
1. **Invalid Nonce Test**: Call guild AJAX endpoints with invalid/non-existent nonces
2. **Logged Out Test**: Attempt AJAX calls when logged out
3. **Parameter Tampering**: Try to call AJAX with incorrect/malicious parameters
4. **Cross-User Data Access**: Attempt to manipulate other users' guild data via AJAX

**Expected Results:**
- All AJAX operations fail without valid, current nonces
- All operations fail when not properly logged in
- Invalid parameters are rejected with appropriate errors
- Unauthorized data access attempts are blocked
- Appropriate error messages returned for all security violations

---

## Guild Performance Testing

### Test 21: Large Guild Performance
**Objective:** Verify system performance with large guilds (50+ members)

**Prerequisites:**
- Guild with 20+ members (or ability to create/test this)
- Multiple users available for testing
- **User Location**: Frontend guild management page

**Steps:**
1. **Create Large Guild**: Form a guild with 20+ members (or join existing large guild)
2. **Test Management**: Use guild management features with large membership
3. **Check Page Load**: Verify frontend page performance with large guild
4. **Test Member Operations**: Perform member promotion/demotion/kick operations
5. **Monitor Performance**: Note any slow loading or delays

**Expected Results:**
- Guild functionality remains responsive with 50+ members
- Database queries execute efficiently
- No significant performance degradation with larger guilds
- Management interface remains functional with large member lists

---

### Test 22: System Load Performance
**Objective:** Verify overall system performance with multiple guilds

**Prerequisites:**
- Multiple guilds created on system
- High-traffic simulation capability (optional)

**Steps:**
1. **Multiple Guilds**: Ensure 10+ guilds exist on system
2. **Concurrent Users**: Test with multiple users accessing guild features
3. **Guild Listing**: Check performance of guild listing/selection interface
4. **Admin Interface**: Verify admin guild management remains efficient

**Expected Results:**
- Guild listing remains efficient with many guilds
- Simultaneous guild operations perform well
- No excessive load times for guild management
- System remains responsive under load

---

## Common Issues to Test

### Issue 1: Missing Frontend Interface
**Problem:** Users can't find or access guild management
**Location:** Frontend
**Test Method:**
- Verify page with `[gamerz_guild_management]` shortcode is published and accessible
- Check if interface renders for logged-in users
- Confirm all buttons and forms display properly
- Test with different user roles

### Issue 2: XP Not Awarded for Guild Actions
**Problem:** Guild creation/joining doesn't award XP
**Location:** Backend/myCRED integration
**Test Method:**
- Verify myCRED plugin is active and configured
- Check that hooks are properly connected for guild actions
- Confirm 50 XP for creation, 10 XP for joining are awarded
- Review myCRED logs for guild-related transactions

### Issue 3: Guild Data Not Persisting
**Problem:** Guild information disappears or doesn't save properly
**Location:** Database/Storage
**Test Method:**
- Create guild and check if it persists after logging out/login
- Verify all guild meta fields save correctly
- Check that user guild assignments remain after session
- Test page refresh and navigation away and back

### Issue 4: Role Management Not Working
**Problem:** Guild leaders can't promote/demote members or kick functionality broken
**Location:** Frontend/Backend role management
**Test Method:**
- Log in as guild leader and test all management functions
- Verify promote/demote/kick buttons appear and function
- Confirm role changes persist and are visible to members
- Check that non-leaders can't access management features

---

## Test Results Summary

### Required Tests to Pass
- [ ] Test 1: Guild Creation Functionality
- [ ] Test 2: Guild Creation Validation
- [ ] Test 3: Guild Join Functionality
- [ ] Test 4: Guild Join Validation
- [ ] Test 5: Guild Leave Functionality
- [ ] Test 6: Guild Leader Leave Validation
- [ ] Test 7: Member Role Management
- [ ] Test 8: Member Removal (Kick Feature)
- [ ] Test 9: Permission Validation
- [ ] Test 15: Guild Management Shortcode Display

### Success Criteria
- All guild functionality works as expected on frontend
- XP system properly integrates with guild actions (50 XP for creation, 10 XP for joining)
- Security restrictions are properly enforced (nonces, user roles, permissions)
- Data is properly stored and retrieved from database
- User experience is smooth and intuitive
- `[gamerz_guild_management]` shortcode displays all guild features properly
- All AJAX operations are secure with proper nonces
- Frontend interface is accessible to logged-in users only

### Fail Criteria
- Guild creation, join or leave functions don't work properly
- Security vulnerabilities exist (unauthorized access, missing nonces)
- Data corruption occurs (guild info disappears, user roles not saved)
- Performance issues make system unusable (slow loading, timeouts)
- XP system does not award points for guild activities
- Frontend guild interface doesn't display properly for users
- Guild member management features don't work for leaders