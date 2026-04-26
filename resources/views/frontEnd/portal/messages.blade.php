@extends('frontEnd.portal.layouts.master')

@section('title', '— Messages')

@section('styles')
<style>
    .messages-header { margin-bottom: 25px; }
    .messages-header h2 { margin: 0 0 5px; font-weight: 600; color: #2c3e50; }
    .messages-header p { margin: 0; color: #777; font-size: 15px; }
    .stat-row { margin-bottom: 20px; }
    .msg-stat {
        background: #fff;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .msg-stat .stat-val { font-size: 28px; font-weight: 700; }
    .msg-stat .stat-lbl { font-size: 13px; color: #777; margin-top: 3px; }
    .msg-stat-total { border-top: 3px solid #3498db; }
    .msg-stat-total .stat-val { color: #3498db; }
    .msg-stat-unread { border-top: 3px solid #2ecc71; }
    .msg-stat-unread .stat-val { color: #2ecc71; }
    .msg-stat-sent { border-top: 3px solid #9b59b6; }
    .msg-stat-sent .stat-val { color: #9b59b6; }

    .btn-compose {
        margin-bottom: 20px;
    }
    .compose-panel {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        display: none;
    }
    .compose-panel .form-group label { font-weight: 600; color: #555; }
    .compose-panel .form-control { border-radius: 6px; }
    .compose-panel textarea.form-control { resize: vertical; }

    .message-list {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .message-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background 0.15s;
    }
    .message-item:hover { background: #fafafa; }
    .message-item:last-child { border-bottom: none; }
    .message-item.unread-staff { background: #f0faf4; }
    .message-item.unread-staff:hover { background: #e5f5ec; }

    .msg-row { display: flex; align-items: flex-start; gap: 12px; }
    .msg-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 16px; flex-shrink: 0;
    }
    .msg-avatar-staff { background: #3498db; }
    .msg-avatar-family { background: #9b59b6; }
    .msg-body { flex: 1; min-width: 0; }
    .msg-sender { font-weight: 600; color: #2c3e50; font-size: 14px; }
    .msg-sender .sender-label {
        font-weight: 400; font-size: 12px; color: #999;
        margin-left: 6px;
    }
    .msg-subject { font-size: 14px; color: #333; margin-top: 2px; }
    .msg-preview { font-size: 13px; color: #888; margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .msg-meta { display: flex; align-items: center; gap: 8px; margin-top: 5px; flex-wrap: wrap; }
    .msg-date { font-size: 12px; color: #aaa; }

    .badge-priority {
        font-size: 11px; padding: 2px 8px; border-radius: 10px; font-weight: 600;
    }
    .badge-high { background: #e74c3c; color: #fff; }
    .badge-normal { background: #3498db; color: #fff; }
    .badge-low { background: #95a5a6; color: #fff; }
    .badge-category {
        font-size: 11px; padding: 2px 8px; border-radius: 10px;
        background: #ecf0f1; color: #555; font-weight: 500;
    }
    .badge-new {
        font-size: 11px; padding: 2px 8px; border-radius: 10px;
        background: #2ecc71; color: #fff; font-weight: 600;
    }

    .msg-detail {
        display: none;
        padding: 15px 20px 15px 72px;
        border-bottom: 1px solid #f0f0f0;
        background: #fafcfe;
    }
    .msg-detail .detail-body {
        font-size: 14px; color: #444; line-height: 1.6;
        white-space: pre-wrap; word-wrap: break-word;
    }
    .msg-detail .btn-reply { margin-top: 12px; }

    .empty-state {
        text-align: center; padding: 60px 20px;
        background: #fff; border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .empty-state i { font-size: 48px; color: #bbb; margin-bottom: 15px; }
    .empty-state h4 { color: #888; font-weight: 400; }

    .access-denied-card {
        background: #fff; border-radius: 8px; padding: 40px;
        text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .access-denied-card i { font-size: 48px; color: #e74c3c; margin-bottom: 15px; }
    .access-denied-card h3 { color: #e74c3c; }
    .access-denied-card p { color: #888; }
</style>
@endsection

@section('content')
<div class="messages-header">
    <h2><i class="fa fa-envelope"></i> Messages</h2>
    <p>Communicate with your care team</p>
</div>

@if($access_denied)
<div class="access-denied-card">
    <i class="fa fa-lock"></i>
    <h3>Access Denied</h3>
    <p>You do not have permission to access the messaging feature. Please contact the care team if you believe this is an error.</p>
</div>
@else

<div class="row stat-row">
    <div class="col-md-4 col-sm-4">
        <div class="msg-stat msg-stat-total">
            <div class="stat-val">{{ $stats['total'] }}</div>
            <div class="stat-lbl">Total Messages</div>
        </div>
    </div>
    <div class="col-md-4 col-sm-4">
        <div class="msg-stat msg-stat-unread">
            <div class="stat-val">{{ $stats['unread'] }}</div>
            <div class="stat-lbl">Unread</div>
        </div>
    </div>
    <div class="col-md-4 col-sm-4">
        <div class="msg-stat msg-stat-sent">
            <div class="stat-val">{{ $stats['sent'] }}</div>
            <div class="stat-lbl">Sent</div>
        </div>
    </div>
</div>

<button class="btn btn-primary btn-compose" id="btn-toggle-compose">
    <i class="fa fa-plus"></i> New Message
</button>

<div class="compose-panel" id="compose-panel">
    <h4 style="margin-top:0; margin-bottom:15px; font-weight:600; color:#2c3e50">Compose Message</h4>
    <form id="compose-form">
        <div class="form-group">
            <label>To</label>
            <input type="text" class="form-control" value="Care Team" disabled>
        </div>
        <div class="form-group">
            <label>Subject <span style="color:#e74c3c">*</span></label>
            <input type="text" class="form-control" name="subject" maxlength="255" required>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" name="category">
                        <option value="general">General</option>
                        <option value="schedule">Schedule</option>
                        <option value="medication">Medication</option>
                        <option value="care_plan">Care Plan</option>
                        <option value="feedback">Feedback</option>
                        <option value="concern">Concern</option>
                        <option value="request">Request</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Priority</label>
                    <select class="form-control" name="priority">
                        <option value="low">Low</option>
                        <option value="normal" selected>Normal</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Message <span style="color:#e74c3c">*</span></label>
            <textarea class="form-control" name="message_content" rows="6" maxlength="5000" required></textarea>
        </div>
        <input type="hidden" name="replied_to_id" id="replied_to_id" value="">
        <div style="display:flex; gap:10px; justify-content:flex-end">
            <button type="button" class="btn btn-default" id="btn-cancel-compose">Cancel</button>
            <button type="submit" class="btn btn-primary" id="btn-send">
                <i class="fa fa-paper-plane"></i> Send Message
            </button>
        </div>
    </form>
</div>

@if($messages->count() > 0)
<div class="message-list" id="message-list">
    @foreach($messages as $msg)
    <div class="message-item {{ $msg->sender_type === 'staff' && !$msg->is_read ? 'unread-staff' : '' }}"
         data-id="{{ $msg->id }}"
         data-sender-type="{{ $msg->sender_type }}"
         data-is-read="{{ $msg->is_read ? '1' : '0' }}">
        <div class="msg-row">
            <div class="msg-avatar {{ $msg->sender_type === 'staff' ? 'msg-avatar-staff' : 'msg-avatar-family' }}">
                {{ strtoupper(substr($msg->sender_name, 0, 1)) }}
            </div>
            <div class="msg-body">
                <div class="msg-sender">
                    {{ $msg->sender_name }}
                    <span class="sender-label">{{ $msg->sender_type === 'staff' ? 'Care Team' : 'You' }}</span>
                </div>
                <div class="msg-subject">{{ $msg->subject }}</div>
                <div class="msg-preview">{{ Str::limit($msg->message_content, 80) }}</div>
                <div class="msg-meta">
                    <span class="badge-priority badge-{{ $msg->priority }}">{{ ucfirst($msg->priority) }}</span>
                    <span class="badge-category">{{ str_replace('_', ' ', ucfirst($msg->category)) }}</span>
                    @if($msg->sender_type === 'staff' && !$msg->is_read)
                        <span class="badge-new">New</span>
                    @endif
                    <span class="msg-date">{{ $msg->created_at ? $msg->created_at->format('d M Y, H:i') : '' }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="msg-detail" id="detail-{{ $msg->id }}">
        <div class="detail-body">{{ $msg->message_content }}</div>
        @if($msg->sender_type === 'staff')
        <button class="btn btn-sm btn-primary btn-reply" data-msg-id="{{ $msg->id }}" data-subject="{{ $msg->subject }}">
            <i class="fa fa-reply"></i> Reply
        </button>
        @endif
    </div>
    @endforeach
</div>
@else
<div class="empty-state">
    <i class="fa fa-envelope-o"></i>
    <h4>No messages yet</h4>
    <p style="color:#aaa">Start a conversation with your care team</p>
    <button class="btn btn-primary" id="btn-first-message" style="margin-top:15px">
        <i class="fa fa-plus"></i> Send Your First Message
    </button>
</div>
@endif

@endif
@endsection

@section('scripts')
<script src="{{ url('public/js/portal/messages.js') }}"></script>
@endsection
