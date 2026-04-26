$(function() {
    // Keyboard navigation for week switching
    $(document).on('keydown', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;

        if (e.key === 'ArrowLeft') {
            var prevBtn = $('.week-nav .nav-arrows a').first();
            if (prevBtn.length) window.location.href = prevBtn.attr('href');
        } else if (e.key === 'ArrowRight') {
            var nextBtn = $('.week-nav .nav-arrows a').last();
            if (nextBtn.length) window.location.href = nextBtn.attr('href');
        }
    });
});
