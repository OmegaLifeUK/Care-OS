$(document).ready(function () {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrfToken } });

    // View toggle
    $('#btn-view-form').on('click', function () {
        $(this).addClass('active');
        $('#btn-view-history').removeClass('active');
        $('#form-section').show();
        $('#history-section').hide();
    });

    $('#btn-view-history').on('click', function () {
        $(this).addClass('active');
        $('#btn-view-form').removeClass('active');
        $('#form-section').hide();
        $('#history-section').show();
    });

    $('#btn-first-feedback').on('click', function () {
        $('#btn-view-form').trigger('click');
    });

    // Star rating widget
    var currentRating = 5;

    function updateStars(val) {
        currentRating = val;
        $('#rating-input').val(val);
        $('#rating-label').text(val + '/5');
        $('#star-rating .fa').each(function () {
            var starVal = parseInt($(this).data('val'));
            if (starVal <= val) {
                $(this).removeClass('fa-star-o').addClass('fa-star active');
            } else {
                $(this).removeClass('fa-star active').addClass('fa-star-o');
            }
        });
    }

    $('#star-rating .fa').on('click', function () {
        updateStars(parseInt($(this).data('val')));
    });

    $('#star-rating .fa').on('mouseenter', function () {
        var hoverVal = parseInt($(this).data('val'));
        $('#star-rating .fa').each(function () {
            var starVal = parseInt($(this).data('val'));
            if (starVal <= hoverVal) {
                $(this).removeClass('fa-star-o').addClass('fa-star');
            } else {
                $(this).removeClass('fa-star').addClass('fa-star-o');
            }
        });
    });

    $('#star-rating').on('mouseleave', function () {
        updateStars(currentRating);
    });

    // Mutually exclusive: anonymous vs callback
    $('input[name="is_anonymous"]').on('change', function () {
        if ($(this).is(':checked')) {
            $('input[name="wants_callback"]').prop('checked', false);
        }
    });
    $('input[name="wants_callback"]').on('change', function () {
        if ($(this).is(':checked')) {
            $('input[name="is_anonymous"]').prop('checked', false);
        }
    });

    // Cancel form
    $('#btn-cancel-form').on('click', function () {
        $('#feedback-form')[0].reset();
        updateStars(5);
    });

    // Submit feedback
    $('#feedback-form').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btn-submit-feedback');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

        var formData = {
            subject: $('input[name="subject"]').val(),
            comments: $('textarea[name="comments"]').val(),
            feedback_type: $('select[name="feedback_type"]').val(),
            category: $('select[name="category"]').val(),
            rating: parseInt($('#rating-input').val()),
            relationship: $('select[name="relationship"]').val(),
            is_anonymous: $('input[name="is_anonymous"]').is(':checked') ? 1 : 0,
            wants_callback: $('input[name="wants_callback"]').is(':checked') ? 1 : 0,
            contact_email: $('input[name="contact_email"]').val() || null,
            contact_phone: $('input[name="contact_phone"]').val() || null
        };

        $.ajax({
            url: window.location.origin + '/portal/feedback/submit',
            method: 'POST',
            data: formData,
            success: function (res) {
                if (res.status) {
                    $('.feedback-form-panel').hide();
                    $('#success-card').show();
                } else {
                    alert(res.message || 'Failed to submit feedback');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors || {};
                    var msg = '';
                    for (var field in errors) {
                        msg += errors[field].join('\n') + '\n';
                    }
                    alert(msg || 'Validation failed');
                } else if (xhr.status === 403) {
                    alert('Permission denied');
                } else {
                    alert('An error occurred. Please try again.');
                }
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Submit Feedback');
            }
        });
    });

    // After success, go to history
    $('#btn-after-success').on('click', function () {
        window.location.reload();
    });
});
