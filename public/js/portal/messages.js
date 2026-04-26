$(function () {
    // Toggle compose panel
    $('#btn-toggle-compose, #btn-first-message').on('click', function () {
        $('#compose-panel').slideDown(200);
        $('#btn-toggle-compose').hide();
        $('#compose-form [name="subject"]').focus();
    });

    $('#btn-cancel-compose').on('click', function () {
        $('#compose-panel').slideUp(200);
        $('#btn-toggle-compose').show();
        $('#compose-form')[0].reset();
        $('#replied_to_id').val('');
    });

    // Expand/collapse message detail
    $('.message-item').on('click', function () {
        var $item = $(this);
        var id = $item.data('id');
        var $detail = $('#detail-' + id);
        var wasVisible = $detail.is(':visible');

        // Collapse all others
        $('.msg-detail').slideUp(150);

        if (!wasVisible) {
            $detail.slideDown(200);

            // Mark as read if unread staff message
            if ($item.data('sender-type') === 'staff' && $item.data('is-read') === 0) {
                $.post('/portal/messages/read/' + id, function (res) {
                    if (res.status) {
                        $item.removeClass('unread-staff');
                        $item.find('.badge-new').remove();
                        $item.data('is-read', 1);
                    }
                });
            }
        }
    });

    // Reply button
    $(document).on('click', '.btn-reply', function (e) {
        e.stopPropagation();
        var msgId = $(this).data('msg-id');
        var subject = $(this).data('subject');

        $('#compose-panel').slideDown(200);
        $('#btn-toggle-compose').hide();

        var subjectVal = subject.indexOf('Re: ') === 0 ? subject : 'Re: ' + subject;
        $('#compose-form [name="subject"]').val(subjectVal);
        $('#replied_to_id').val(msgId);
        $('#compose-form [name="message_content"]').focus();

        $('html, body').animate({ scrollTop: $('#compose-panel').offset().top - 20 }, 300);
    });

    // Send message
    $('#compose-form').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btn-send');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

        var formData = {
            subject: $('#compose-form [name="subject"]').val(),
            message_content: $('#compose-form [name="message_content"]').val(),
            category: $('#compose-form [name="category"]').val(),
            priority: $('#compose-form [name="priority"]').val(),
            replied_to_id: $('#replied_to_id').val() || null
        };

        $.ajax({
            url: '/portal/messages/send',
            type: 'POST',
            data: formData,
            success: function (res) {
                if (res.status) {
                    window.location.reload();
                } else {
                    alert('Failed to send message: ' + (res.message || 'Unknown error'));
                    $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Message');
                }
            },
            error: function (xhr) {
                var msg = 'Failed to send message.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    msg = Object.values(errors).map(function (e) { return e[0]; }).join('\n');
                } else if (xhr.status === 403) {
                    msg = 'Permission denied.';
                }
                alert(msg);
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Message');
            }
        });
    });
});
