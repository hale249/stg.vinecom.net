'use strict';

// Investment Report Animations
(function($) {
    // Initialize when document is ready
    $(document).ready(function() {
        // Initialize animations
        initAnimations();
        
        // Initialize chart hover effects
        initChartEffects();
        
        // Initialize data refresh animations
        initDataRefreshAnimations();
        
        // Initialize scroll reveal
        initScrollReveal();
        
        // Initialize loading indicators
        initLoadingIndicators();
    });
    
    // Initialize animations for elements
    function initAnimations() {
        // Add entrance animations to cards
        $('.card').each(function(index) {
            $(this).addClass('animate-fade-in');
            $(this).css('animation-delay', (index * 0.1) + 's');
        });
        
        // Add pulse animation to important metrics
        $('.deposit-amount, .total_invest, h3:contains("Tổng đầu tư")').addClass('animate-pulse');
    }
    
    // Add hover effects to charts
    function initChartEffects() {
        $('.chart-area').hover(
            function() {
                $(this).find('canvas').css('transform', 'scale(1.02)');
            },
            function() {
                $(this).find('canvas').css('transform', 'scale(1)');
            }
        );
    }
    
    // Add animations when data is refreshed
    function initDataRefreshAnimations() {
        // Override AJAX success to add animations
        const originalAjaxSuccess = $.ajax;
        $.ajax = function() {
            const originalSuccess = arguments[0].success;
            if (typeof originalSuccess === 'function') {
                arguments[0].success = function() {
                    // Add highlight animation to updated elements
                    $('.total_invest, .runningInvests, .completedInvests, .interests').addClass('data-update');
                    
                    // Remove animation class after animation completes
                    setTimeout(function() {
                        $('.data-update').removeClass('data-update');
                    }, 1000);
                    
                    // Call original success handler
                    return originalSuccess.apply(this, arguments);
                };
            }
            return originalAjaxSuccess.apply(this, arguments);
        };
        
        // Add loading spinner during AJAX requests
        $(document).ajaxStart(function() {
            showLoadingSpinner();
        }).ajaxStop(function() {
            hideLoadingSpinner();
        });
    }
    
    // Show loading spinner
    function showLoadingSpinner() {
        if ($('#global-loading-spinner').length === 0) {
            $('body').append('<div id="global-loading-spinner" class="loading-spinner-container"><div class="loading-spinner"></div></div>');
        }
        setTimeout(function() {
            $('#global-loading-spinner').fadeIn(200);
        }, 300); // Only show for requests that take more than 300ms
    }
    
    // Hide loading spinner
    function hideLoadingSpinner() {
        $('#global-loading-spinner').fadeOut(200);
    }
    
    // Initialize scroll reveal animations
    function initScrollReveal() {
        // Animate elements when they come into view
        const animateOnScroll = function() {
            $('.animate-on-scroll:not(.animated)').each(function() {
                const elementTop = $(this).offset().top;
                const elementBottom = elementTop + $(this).outerHeight();
                const viewportTop = $(window).scrollTop();
                const viewportBottom = viewportTop + $(window).height();
                
                if (elementBottom > viewportTop && elementTop < viewportBottom) {
                    $(this).addClass('animated');
                    
                    // Add different animation classes based on data attribute
                    const animationType = $(this).data('animation') || 'fade-in';
                    $(this).addClass('animate-' + animationType);
                }
            });
        };
        
        // Run on scroll
        $(window).on('scroll', animateOnScroll);
        
        // Run once on load
        animateOnScroll();
    }
    
    // Enhanced chart animations
    function enhanceChartAnimations() {
        // Override Chart.js animations
        if (typeof Chart !== 'undefined') {
            const originalAnimation = Chart.defaults.global.animation;
            Chart.defaults.global.animation = {
                ...originalAnimation,
                duration: 1500,
                easing: 'easeOutQuart'
            };
            
            // Add hover interactions
            const originalUpdateHoverStyle = Chart.helpers.updateHoverStyle;
            Chart.helpers.updateHoverStyle = function(elements) {
                elements.forEach(element => {
                    if (element._model && element._model.backgroundColor) {
                        element._model._originalBackgroundColor = element._model.backgroundColor;
                        element._model.backgroundColor = Chart.helpers.color(element._model.backgroundColor).alpha(0.8).rgbString();
                    }
                });
                return originalUpdateHoverStyle.apply(this, arguments);
            };
        }
    }
    
    // Add CSS for loading spinner
    const spinnerCSS = `
        .loading-spinner-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            padding: 20px;
            display: none;
        }
    `;
    
    $('head').append('<style>' + spinnerCSS + '</style>');
    
    // Add fullscreen toggle functionality with animation
    $('.fullscreen-open').on('click', function() {
        const card = $(this).closest('.card');
        card.addClass('fullscreen-mode');
        card.animate({
            position: 'fixed',
            top: 0,
            left: 0,
            width: '100%',
            height: '100%',
            zIndex: 9999
        }, 300);
    });
    
    $('.fullscreen-close').on('click', function() {
        const card = $(this).closest('.card');
        card.removeClass('fullscreen-mode');
        card.animate({
            position: 'relative',
            top: 'auto',
            left: 'auto',
            width: '100%',
            height: 'auto',
            zIndex: 1
        }, 300);
    });
    
    // Add smooth transitions when changing time periods
    $('[name=invest_time], [name=project_statistics_time], [name=invest_interest_time], [name=project_statistics_invests]').on('change', function() {
        const chartContainer = $(this).closest('.card').find('.chart-area, .my_invest_canvas');
        chartContainer.fadeOut(200, function() {
            chartContainer.fadeIn(400);
        });
    });

})(jQuery); 