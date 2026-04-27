@extends('frontEnd.layouts.master')
@section('title','Client Feedback Hub')
@section('content')

@include('frontEnd.roster.common.roster_header')
<main class="page-content">

<style>
    .fh-container { padding: 20px 30px; }
    .fh-title { font-size: 22px; font-weight: 600; color: #2c3e50; margin-bottom: 5px; }
    .fh-subtitle { color: #777; font-size: 14px; margin-bottom: 20px; }

    .stat-cards { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
    .stat-card {
        flex: 1; min-width: 180px; background: #fff; border-radius: 8px;
        padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        display: flex; align-items: center; gap: 15px;
    }
    .stat-card .stat-icon {
        width: 50px; height: 50px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; color: #fff;
    }
    .stat-card .stat-val { font-size: 26px; font-weight: 700; }
    .stat-card .stat-lbl { font-size: 13px; color: #777; }
    .stat-icon-total { background: #3498db; }
    .stat-icon-new { background: #f39c12; }
    .stat-icon-compliment { background: #2ecc71; }
    .stat-icon-rating { background: #f5a623; }
    .stat-val-total { color: #3498db; }
    .stat-val-new { color: #f39c12; }
    .stat-val-compliment { color: #2ecc71; }
    .stat-val-rating { color: #f5a623; }

    .filter-bar {
        display: flex; align-items: center; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;
    }
    .filter-bar .btn { border-radius: 20px; font-size: 13px; padding: 5px 16px; }
    .filter-bar .btn.active { background: #3498db; color: #fff; border-color: #3498db; }
    .filter-bar select { border-radius: 20px; font-size: 13px; padding: 5px 12px; max-width: 200px; }

    .feedback-list { }
    .fb-item {
        background: #fff; border-radius: 8px; padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 12px;
        border-left: 5px solid #ccc;
    }
    .fb-item.fb-complaint { border-left-color: #e74c3c; }
    .fb-item.fb-compliment { border-left-color: #2ecc71; }
    .fb-item.fb-suggestion { border-left-color: #3498db; }
    .fb-item.fb-concern { border-left-color: #f39c12; }
    .fb-item.fb-general { border-left-color: #95a5a6; }

    .fb-row1 { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .fb-subject { font-weight: 600; font-size: 16px; color: #2c3e50; }
    .fb-badge {
        font-size: 11px; padding: 2px 8px; border-radius: 10px; font-weight: 600;
        display: inline-block;
    }
    .fb-badge-compliment { background: #2ecc71; color: #fff; }
    .fb-badge-complaint { background: #e74c3c; color: #fff; }
    .fb-badge-suggestion { background: #3498db; color: #fff; }
    .fb-badge-concern { background: #f39c12; color: #fff; }
    .fb-badge-general { background: #95a5a6; color: #fff; }
    .fb-badge-new { background: #f39c12; color: #fff; }
    .fb-badge-acknowledged { background: #3498db; color: #fff; }
    .fb-badge-resolved { background: #2ecc71; color: #fff; }
    .fb-badge-closed { background: #95a5a6; color: #fff; }
    .fb-badge-high { background: #e74c3c; color: #fff; }
    .fb-badge-medium { background: #f39c12; color: #fff; }
    .fb-badge-cat { background: #ecf0f1; color: #555; font-weight: 500; }

    .fb-row2 { font-size: 13px; color: #777; margin-top: 5px; }
    .fb-row2 span { margin-right: 12px; }

    .fb-stars { color: #f5a623; font-size: 14px; margin-top: 5px; }
    .fb-stars .fa-star-o { color: #ddd; }

    .fb-comments {
        background: #f9f9f9; border-radius: 6px; padding: 10px 14px;
        margin-top: 10px; font-size: 14px; color: #444; line-height: 1.5;
        white-space: pre-wrap; word-wrap: break-word;
    }

    .fb-callback-box {
        background: #eaf4fc; border-radius: 6px; padding: 10px 14px;
        margin-top: 8px; font-size: 13px; color: #2980b9;
        border-left: 3px solid #3498db;
    }

    .fb-response-box {
        background: #f0faf4; border-radius: 6px; padding: 10px 14px;
        margin-top: 8px; border-left: 3px solid #2ecc71;
    }
    .fb-response-box .resp-label { font-size: 12px; color: #2ecc71; font-weight: 600; margin-bottom: 4px; }
    .fb-response-box .resp-text { font-size: 14px; color: #444; }
    .fb-response-box .resp-meta { font-size: 12px; color: #999; margin-top: 4px; }

    .fb-actions { margin-top: 10px; display: flex; gap: 8px; }
    .fb-actions .btn { font-size: 13px; border-radius: 6px; }

    .empty-fb {
        text-align: center; padding: 60px 20px;
        background: #fff; border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .empty-fb i { font-size: 48px; color: #bbb; margin-bottom: 15px; }
    .empty-fb h4 { color: #888; font-weight: 400; }

    .loading-spinner { text-align: center; padding: 40px; color: #999; }

    #respond-modal .modal-body .original-fb {
        background: #f9f9f9; border-radius: 6px; padding: 12px; margin-bottom: 15px;
    }
    #respond-modal .modal-body .original-fb .orig-subject { font-weight: 600; color: #2c3e50; }
    #respond-modal .modal-body .original-fb .orig-comments { font-size: 14px; color: #555; margin-top: 5px; }
</style>

<div class="fh-container">
    <h2 class="fh-title"><i class="fa fa-comments" style="color:#3498db"></i> Client Feedback Hub</h2>
    <p class="fh-subtitle">View and manage feedback from families and clients</p>

    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon stat-icon-total"><i class="fa fa-comments"></i></div>
            <div>
                <div class="stat-val stat-val-total">{{ $stats['total'] }}</div>
                <div class="stat-lbl">Total Feedback</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-new"><i class="fa fa-exclamation-circle"></i></div>
            <div>
                <div class="stat-val stat-val-new">{{ $stats['new'] }}</div>
                <div class="stat-lbl">New / Pending</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-compliment"><i class="fa fa-thumbs-up"></i></div>
            <div>
                <div class="stat-val stat-val-compliment">{{ $stats['compliments'] }}</div>
                <div class="stat-lbl">Compliments</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-rating"><i class="fa fa-star"></i></div>
            <div>
                <div class="stat-val stat-val-rating">{{ $stats['avg_rating'] }}/5</div>
                <div class="stat-lbl">Avg Rating</div>
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <button class="btn btn-default active" data-status="">All</button>
        <button class="btn btn-default" data-status="new">New</button>
        <button class="btn btn-default" data-status="acknowledged">Acknowledged</button>
        <button class="btn btn-default" data-status="resolved">Resolved</button>
        <button class="btn btn-default" data-status="closed">Closed</button>
        <select class="form-control" id="filter-type">
            <option value="">All Types</option>
            <option value="compliment">Compliments</option>
            <option value="complaint">Complaints</option>
            <option value="suggestion">Suggestions</option>
            <option value="concern">Concerns</option>
            <option value="general">General</option>
        </select>
    </div>

    <div class="feedback-list" id="feedback-list">
        <div class="loading-spinner"><i class="fa fa-spinner fa-spin fa-2x"></i><br>Loading feedback...</div>
    </div>
</div>

<div class="modal fade" id="respond-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Respond to Feedback</h4>
            </div>
            <div class="modal-body">
                <div class="original-fb">
                    <div class="orig-subject" id="respond-subject"></div>
                    <div class="orig-comments" id="respond-comments"></div>
                </div>
                <div class="form-group">
                    <label style="font-weight:600">Your Response <span style="color:#e74c3c">*</span></label>
                    <textarea class="form-control" id="respond-text" rows="5" maxlength="5000" required placeholder="Type your response..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-send-response">
                    <i class="fa fa-reply"></i> Send Response
                </button>
            </div>
            <input type="hidden" id="respond-feedback-id" value="">
        </div>
    </div>
</div>

</main>

<script src="{{ url('public/js/roster/feedback_hub.js') }}"></script>
@endsection
