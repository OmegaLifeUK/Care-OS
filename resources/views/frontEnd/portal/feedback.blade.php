@extends('frontEnd.portal.layouts.master')

@section('title', '— Feedback')

@section('styles')
<style>
    .feedback-header { margin-bottom: 25px; }
    .feedback-header h2 { margin: 0 0 5px; font-weight: 600; color: #2c3e50; }
    .feedback-header p { margin: 0; color: #777; font-size: 15px; }

    .stat-row { margin-bottom: 20px; }
    .fb-stat {
        background: #fff; border-radius: 8px; padding: 15px;
        text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .fb-stat .stat-val { font-size: 28px; font-weight: 700; }
    .fb-stat .stat-lbl { font-size: 13px; color: #777; margin-top: 3px; }
    .fb-stat-total { border-top: 3px solid #3498db; }
    .fb-stat-total .stat-val { color: #3498db; }
    .fb-stat-responded { border-top: 3px solid #2ecc71; }
    .fb-stat-responded .stat-val { color: #2ecc71; }
    .fb-stat-pending { border-top: 3px solid #f39c12; }
    .fb-stat-pending .stat-val { color: #f39c12; }

    .view-toggle { margin-bottom: 20px; }
    .view-toggle .btn { margin-right: 8px; }
    .view-toggle .btn.active { background: #3498db; color: #fff; border-color: #3498db; }

    .feedback-form-panel {
        background: #fff; border-radius: 8px; padding: 25px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px;
    }
    .feedback-form-panel h4 { margin-top: 0; font-weight: 600; color: #2c3e50; }
    .feedback-form-panel .form-group label { font-weight: 600; color: #555; }
    .feedback-form-panel .form-control { border-radius: 6px; }
    .feedback-form-panel textarea.form-control { resize: vertical; }
    .required-star { color: #e74c3c; }

    .star-rating { display: inline-flex; gap: 4px; cursor: pointer; font-size: 24px; }
    .star-rating .fa-star, .star-rating .fa-star-o { color: #ddd; transition: color 0.15s; }
    .star-rating .fa-star.active, .star-rating .fa-star-o.hover-fill { color: #f5a623; }
    .star-rating .fa-star.active { color: #f5a623; }
    .rating-label { font-size: 14px; color: #777; margin-left: 8px; vertical-align: middle; }

    .success-card {
        background: #fff; border-radius: 8px; padding: 50px;
        text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        display: none;
    }
    .success-card i { font-size: 60px; color: #2ecc71; margin-bottom: 15px; }
    .success-card h3 { color: #2ecc71; font-weight: 600; }
    .success-card p { color: #888; }

    .history-panel { display: none; }
    .feedback-card {
        background: #fff; border-radius: 8px; padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 15px;
        border-left: 4px solid #ccc;
    }
    .feedback-card.type-compliment { border-left-color: #2ecc71; }
    .feedback-card.type-complaint { border-left-color: #e74c3c; }
    .feedback-card.type-suggestion { border-left-color: #3498db; }
    .feedback-card.type-concern { border-left-color: #f39c12; }
    .feedback-card.type-general { border-left-color: #95a5a6; }

    .fb-card-subject { font-weight: 600; font-size: 16px; color: #2c3e50; }
    .fb-card-meta { display: flex; align-items: center; gap: 8px; margin-top: 5px; flex-wrap: wrap; }
    .fb-card-date { font-size: 12px; color: #aaa; }
    .fb-card-comments {
        font-size: 14px; color: #555; margin-top: 10px; line-height: 1.5;
        white-space: pre-wrap; word-wrap: break-word;
    }
    .fb-card-stars { margin-top: 6px; color: #f5a623; font-size: 14px; }
    .fb-card-stars .fa-star-o { color: #ddd; }

    .fb-response-box {
        background: #f0faf4; border-radius: 6px; padding: 12px 15px;
        margin-top: 12px; border-left: 3px solid #2ecc71;
    }
    .fb-response-box .resp-label { font-size: 12px; color: #2ecc71; font-weight: 600; margin-bottom: 4px; }
    .fb-response-box .resp-text { font-size: 14px; color: #444; line-height: 1.5; }
    .fb-response-box .resp-meta { font-size: 12px; color: #999; margin-top: 5px; }

    .badge-type {
        font-size: 11px; padding: 2px 8px; border-radius: 10px; font-weight: 600;
    }
    .badge-compliment { background: #2ecc71; color: #fff; }
    .badge-complaint { background: #e74c3c; color: #fff; }
    .badge-suggestion { background: #3498db; color: #fff; }
    .badge-concern { background: #f39c12; color: #fff; }
    .badge-general { background: #95a5a6; color: #fff; }

    .badge-status {
        font-size: 11px; padding: 2px 8px; border-radius: 10px; font-weight: 500;
    }
    .badge-new { background: #f39c12; color: #fff; }
    .badge-acknowledged { background: #3498db; color: #fff; }
    .badge-resolved { background: #2ecc71; color: #fff; }
    .badge-closed { background: #95a5a6; color: #fff; }

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

    .contact-section {
        background: #f9f9f9; border-radius: 6px; padding: 15px; margin-top: 10px;
    }
</style>
@endsection

@section('content')
<div class="feedback-header">
    <h2><i class="fa fa-comments"></i> Feedback</h2>
    <p>Share your experience with our care services</p>
</div>

@if($access_denied)
<div class="access-denied-card">
    <i class="fa fa-lock"></i>
    <h3>Access Denied</h3>
    <p>You do not have permission to submit feedback. Please contact the care team if you believe this is an error.</p>
</div>
@else

<div class="row stat-row">
    <div class="col-md-4 col-sm-4">
        <div class="fb-stat fb-stat-total">
            <div class="stat-val">{{ $stats['total'] }}</div>
            <div class="stat-lbl">Total Submitted</div>
        </div>
    </div>
    <div class="col-md-4 col-sm-4">
        <div class="fb-stat fb-stat-responded">
            <div class="stat-val">{{ $stats['with_responses'] }}</div>
            <div class="stat-lbl">With Responses</div>
        </div>
    </div>
    <div class="col-md-4 col-sm-4">
        <div class="fb-stat fb-stat-pending">
            <div class="stat-val">{{ $stats['pending'] }}</div>
            <div class="stat-lbl">Pending</div>
        </div>
    </div>
</div>

<div class="view-toggle">
    <button class="btn btn-default active" id="btn-view-form"><i class="fa fa-pencil"></i> Submit Feedback</button>
    <button class="btn btn-default" id="btn-view-history"><i class="fa fa-history"></i> My Feedback History</button>
</div>

<div id="form-section">
    <div class="feedback-form-panel">
        <h4><i class="fa fa-heart" style="color:#e74c3c"></i> We Value Your Feedback</h4>
        <p style="color:#777; margin-bottom:20px">Help us improve our care services by sharing your experience</p>

        <form id="feedback-form">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Your Relationship <span class="required-star">*</span></label>
                        <select class="form-control" name="relationship" required>
                            <option value="family" {{ ($portal_access->relationship ?? '') === 'family' ? 'selected' : '' }}>Family Member</option>
                            <option value="self" {{ ($portal_access->relationship ?? '') === 'self' ? 'selected' : '' }}>Self</option>
                            <option value="guardian" {{ ($portal_access->relationship ?? '') === 'guardian' ? 'selected' : '' }}>Guardian</option>
                            <option value="representative" {{ ($portal_access->relationship ?? '') === 'representative' ? 'selected' : '' }}>Representative</option>
                            <option value="other" {{ ($portal_access->relationship ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Feedback Type <span class="required-star">*</span></label>
                        <select class="form-control" name="feedback_type" required>
                            <option value="general">General Feedback</option>
                            <option value="compliment">Compliment</option>
                            <option value="complaint">Complaint</option>
                            <option value="suggestion">Suggestion</option>
                            <option value="concern">Concern</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Category <span class="required-star">*</span></label>
                        <select class="form-control" name="category" required>
                            <option value="care_quality">Care Quality</option>
                            <option value="staff_performance">Staff Performance</option>
                            <option value="communication">Communication</option>
                            <option value="punctuality">Punctuality</option>
                            <option value="professionalism">Professionalism</option>
                            <option value="facilities">Facilities</option>
                            <option value="safety">Safety</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Your Rating <span class="required-star">*</span></label>
                        <div>
                            <div class="star-rating" id="star-rating">
                                <i class="fa fa-star active" data-val="1"></i>
                                <i class="fa fa-star active" data-val="2"></i>
                                <i class="fa fa-star active" data-val="3"></i>
                                <i class="fa fa-star active" data-val="4"></i>
                                <i class="fa fa-star active" data-val="5"></i>
                            </div>
                            <span class="rating-label" id="rating-label">5/5</span>
                            <input type="hidden" name="rating" id="rating-input" value="5">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Subject <span class="required-star">*</span></label>
                <input type="text" class="form-control" name="subject" maxlength="255" required placeholder="Brief summary of your feedback">
            </div>

            <div class="form-group">
                <label>Comments <span class="required-star">*</span></label>
                <textarea class="form-control" name="comments" rows="6" maxlength="5000" required placeholder="Please share your detailed feedback..."></textarea>
            </div>

            <div class="contact-section">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom:10px">
                            <label>Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" maxlength="255" value="{{ $portal_access->user_email ?? '' }}" placeholder="your@email.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="margin-bottom:10px">
                            <label>Contact Phone</label>
                            <input type="text" class="form-control" name="contact_phone" maxlength="50" value="{{ $portal_access->phone ?? '' }}" placeholder="Your phone number">
                        </div>
                    </div>
                </div>
                <div class="checkbox" style="margin:5px 0">
                    <label><input type="checkbox" name="wants_callback" value="1"> I would like someone to contact me about this feedback</label>
                </div>
                <div class="checkbox" style="margin:5px 0">
                    <label><input type="checkbox" name="is_anonymous" value="1"> Submit anonymously (your name will be hidden from staff)</label>
                </div>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px">
                <button type="button" class="btn btn-default" id="btn-cancel-form">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btn-submit-feedback">
                    <i class="fa fa-paper-plane"></i> Submit Feedback
                </button>
            </div>
        </form>
    </div>

    <div class="success-card" id="success-card">
        <i class="fa fa-check-circle"></i>
        <h3>Thank You!</h3>
        <p>Your feedback has been submitted successfully. We appreciate your input and will review it promptly.</p>
        <button class="btn btn-primary" id="btn-after-success" style="margin-top:15px">
            <i class="fa fa-history"></i> View My Feedback
        </button>
    </div>
</div>

<div class="history-panel" id="history-section">
    @if($feedback_list->count() > 0)
        @foreach($feedback_list as $fb)
        <div class="feedback-card type-{{ $fb->feedback_type }}">
            <div class="fb-card-subject">{{ $fb->subject }}</div>
            <div class="fb-card-meta">
                <span class="badge-type badge-{{ $fb->feedback_type }}">{{ ucfirst($fb->feedback_type) }}</span>
                <span class="badge-status badge-{{ $fb->status }}">{{ ucfirst(str_replace('_', ' ', $fb->status)) }}</span>
                <span class="fb-card-date">{{ $fb->created_at ? $fb->created_at->format('d M Y, H:i') : '' }}</span>
            </div>
            <div class="fb-card-stars">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fa {{ $i <= $fb->rating ? 'fa-star' : 'fa-star-o' }}"></i>
                @endfor
            </div>
            <div class="fb-card-comments">{{ Str::limit($fb->comments, 200) }}</div>

            @if($fb->response)
            <div class="fb-response-box">
                <div class="resp-label"><i class="fa fa-reply"></i> Response from Care Team</div>
                <div class="resp-text">{{ $fb->response }}</div>
                <div class="resp-meta">{{ $fb->responded_by_name ?? 'Staff' }} &middot; {{ $fb->response_date ? $fb->response_date->format('d M Y, H:i') : '' }}</div>
            </div>
            @endif
        </div>
        @endforeach
    @else
    <div class="empty-state">
        <i class="fa fa-comments-o"></i>
        <h4>No feedback submitted yet</h4>
        <p style="color:#aaa">Share your experience to help us improve</p>
        <button class="btn btn-primary" id="btn-first-feedback" style="margin-top:15px">
            <i class="fa fa-pencil"></i> Share Your First Feedback
        </button>
    </div>
    @endif
</div>

@endif
@endsection

@section('scripts')
<script src="{{ url('public/js/portal/feedback.js') }}"></script>
@endsection
