/* Visual Enhancements JavaScript for Gamerz Guild */

jQuery(document).ready(function($) {
    // Create confetti effect for achievements
    function createConfetti() {
        const confettiCount = 150;
        const container = document.body;
        
        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.className = 'gamerz-confetti-piece';
            
            // Random properties
            const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff', '#ffffff', '#ffcc00', '#ff6600', '#ff3366'];
            const shapes = ['circle', 'square', 'diamond'];
            
            const size = Math.random() * 10 + 5;
            const color = colors[Math.floor(Math.random() * colors.length)];
            const shape = shapes[Math.floor(Math.random() * shapes.length)];
            
            Object.assign(confetti.style, {
                position: 'fixed',
                width: `${size}px`,
                height: `${size}px`,
                backgroundColor: color,
                borderRadius: shape === 'circle' ? '50%' : shape === 'diamond' ? '0' : '0',
                top: '0',
                left: `${Math.random() * 100}vw`,
                zIndex: '99999',
                opacity: '0.8',
                transform: `rotate(${Math.random() * 360}deg)`,
                animation: `confetti-fall ${Math.random() * 3 + 2}s linear forwards`
            });
            
            // Create diamond shape with CSS
            if (shape === 'diamond') {
                confetti.style.transform = `rotate(45deg) rotate(${Math.random() * 360}deg)`;
            }
            
            document.body.appendChild(confetti);
            
            // Remove after animation
            setTimeout(() => {
                confetti.remove();
            }, 5000);
        }
    }
    
    // Add confetti animation to CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes confetti-fall {
            0% { transform: translateY(0) rotate(0deg) translateX(0); }
            100% { 
                transform: translateY(100vh) rotate(720deg) translateX(${(Math.random() - 0.5) * 200}px);
                opacity: 0;
            }
        }
        .gamerz-confetti-piece {
            pointer-events: none;
        }
    `;
    document.head.appendChild(style);

    // Handle XP gain notifications
    $(document).on('gamerz_xp_gained', function(e, xp_amount, new_total_xp) {
        // Animate XP bars
        $('.gamerz-xp-fill').each(function() {
            const currentWidth = parseFloat($(this).css('width'));
            const newWidth = Math.min(100, currentWidth + 5); // Example: increase by 5%
            $(this).animate({ width: newWidth + '%' }, 500);
        });
        
        // Show XP notification
        showXpNotification(xp_amount);
        
        // Trigger level up animation if applicable
        if (typeof new_total_xp !== 'undefined') {
            maybeTriggerLevelUp(new_total_xp);
        }
    });

    // Display XP gain notification
    function showXpNotification(xpAmount) {
        const notification = $(`<div class="gamerz-xp-notification">+${xpAmount} XP</div>`);
        notification.css({
            position: 'fixed',
            bottom: '80px',
            left: '50%',
            transform: 'translateX(-50%)',
            background: 'linear-gradient(135deg, #0073aa, #00a0d2)',
            color: 'white',
            padding: '10px 20px',
            borderRadius: '20px',
            zIndex: '10000',
            fontWeight: 'bold',
            boxShadow: '0 4px 15px rgba(0,0,0,0.3)',
            animation: 'xpNotification 2s ease-out forwards'
        });
        
        $('body').append(notification);
        
        setTimeout(() => {
            notification.fadeOut(300, () => {
                notification.remove();
            });
        }, 2000);
    }

    // Check if user leveled up and trigger animation
    function maybeTriggerLevelUp(newXp) {
        // This would typically check against rank thresholds
        // For demo, we'll just trigger sometimes
        if (Math.random() > 0.7) { // Random chance for demo purposes
            triggerLevelUpAnimation();
        }
    }

    // Trigger level up animation
    function triggerLevelUpAnimation() {
        // Add class to elements to trigger animation
        $('.gamerz-rank-chip, .gamerz-badge-capsule, .gamerz-progress-bar').addClass('gamerz-level-up');
        
        // Remove class after animation
        setTimeout(() => {
            $('.gamerz-rank-chip, .gamerz-badge-capsule, .gamerz-progress-bar').removeClass('gamerz-level-up');
        }, 500);
    }

    // Handle achievement unlock
    $(document).on('gamerz_achievement_unlocked', function(e, badgeName, badgeDetails) {
        // Create achievement notification
        const achievement = $(`
            <div class="gamerz-achievement-notification">
                <div class="gamerz-achievement-title">Achievement Unlocked!</div>
                <div class="gamerz-achievement-name">${badgeName}</div>
                ${badgeDetails ? `<div class="gamerz-achievement-desc">${badgeDetails}</div>` : ''}
            </div>
        `);
        
        achievement.css({
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: 'linear-gradient(135deg, #0073aa, #00a0d2)',
            color: 'white',
            padding: '15px 25px',
            borderRadius: '8px',
            zIndex: '10000',
            boxShadow: '0 4px 15px rgba(0,0,0,0.3)',
            fontWeight: 'bold',
            display: 'flex',
            flexDirection: 'column',
            alignItems: 'flex-start'
        });
        
        achievement.find('.gamerz-achievement-title').css({
            fontSize: '1.1em',
            marginBottom: '5px'
        });
        
        achievement.find('.gamerz-achievement-name').css({
            fontSize: '0.9em',
            fontWeight: 'normal'
        });
        
        achievement.find('.gamerz-achievement-desc').css({
            fontSize: '0.8em',
            opacity: '0.8',
            marginTop: '3px'
        });
        
        $('body').append(achievement);
        
        // Trigger confetti
        createConfetti();
        
        // Remove after 5 seconds
        setTimeout(() => {
            achievement.fadeOut(300, () => {
                achievement.remove();
            });
        }, 5000);
    });

    // Enhance buttons with click effects
    $(document).on('click', '.gamerz-btn', function(e) {
        const btn = $(this);
        const ripple = $('<span class="gamerz-ripple"></span>');
        
        // Position ripple at click location
        const rect = btn[0].getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        ripple.css({
            position: 'absolute',
            borderRadius: '50%',
            background: 'rgba(255, 255, 255, 0.3)',
            width: '0',
            height: '0',
            top: y + 'px',
            left: x + 'px',
            transform: 'translate(-50%, -50%)',
            animation: 'ripple 0.6s linear'
        });
        
        btn.prepend(ripple);
        
        // Remove ripple after animation
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });

    // Add ripple animation to CSS
    const rippleStyle = document.createElement('style');
    rippleStyle.textContent = `
        @keyframes ripple {
            to {
                width: 400px;
                height: 400px;
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(rippleStyle);

    // Interactive rank badges tooltips
    $(document).on('mouseenter', '.gamerz-rank-chip', function() {
        const rank = $(this).text();
        // In a real implementation, you'd show more details about the rank
    });

    // Add hover effects to gamified elements
    $(document).on('mouseenter', '.gamerz-gamified-element', function() {
        $(this).css({
            transform: 'translateY(-5px)',
            boxShadow: '0 10px 25px rgba(0, 115, 170, 0.3)'
        });
    });
    
    $(document).on('mouseleave', '.gamerz-gamified-element', function() {
        $(this).css({
            transform: 'translateY(0)',
            boxShadow: '0 4px 12px rgba(0,0,0,0.1)'
        });
    });

    // Animate progress bars on load
    function animateProgressBars() {
        $('.gamerz-progress-bar').each(function() {
            const fill = $(this).find('.gamerz-progress-fill');
            const targetWidth = fill.attr('data-width') || fill.css('width');
            
            // Reset width to 0 then animate to target
            fill.css('width', '0');
            fill.animate({ width: targetWidth }, 1000, 'swing');
        });
    }
    
    // Run animation when document is ready and on scroll
    animateProgressBars();
    
    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 100) {
            animateProgressBars();
        }
    });

    // Enhanced XP bar in header
    function updateHeaderXpBar(newPercentage) {
        $('#gamerz-xp-header-bar .gamerz-xp-fill').animate({ 
            width: newPercentage + '%' 
        }, 500);
    }

    // Example: Update XP bar if user gains XP (this would come from AJAX)
    // This is just an example - in reality this would be triggered by an event
    window.gamerzUpdateXpBar = function(percentage) {
        updateHeaderXpBar(percentage);
    };

    // Handle guild join/leave animations
    $(document).on('click', '.gamerz-guild-join-btn, .gamerz-guild-leave-btn', function(e) {
        const btn = $(this);
        btn.prop('disabled', true);
        
        setTimeout(() => {
            btn.prop('disabled', false);
        }, 1000);
    });

    // Badge collection animation
    function animateBadgeCollection() {
        $('.gamerz-badge-capsule').each(function(index) {
            const badge = $(this);
            
            setTimeout(() => {
                badge.addClass('gamerz-level-up');
                
                setTimeout(() => {
                    badge.removeClass('gamerz-level-up');
                }, 300);
            }, index * 100);
        });
    }
    
    // Run badge animation
    setTimeout(animateBadgeCollection, 1000);
});

// Global functions for other scripts to use
window.gamerzTriggerAchievement = function(title, description) {
    jQuery(document).trigger('gamerz_achievement_unlocked', [title, description]);
};

window.gamerzUpdateUserXp = function(xpAmount, newTotalXp) {
    jQuery(document).trigger('gamerz_xp_gained', [xpAmount, newTotalXp]);
};