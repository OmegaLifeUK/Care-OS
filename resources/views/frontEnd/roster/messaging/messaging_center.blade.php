@extends('frontEnd.layouts.master')
@section('title','Messaging Center — Client Comms Hub')
@section('content')

@include('frontEnd.roster.common.roster_header')
<main class="page-content">

<style>
    .page-wrapper .page-content > .comms-container {
        padding: 0;
        margin: 0;
    }
    .comms-container {
        display: flex;
        height: calc(100vh - 90px);
        background: #f4f6f9;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Left panel — client list */
    .client-panel {
        width: 300px;
        min-width: 300px;
        background: #fff;
        border-right: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
    }
    .client-panel-header {
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
    }
    .client-panel-header h4 {
        margin: 0 0 10px;
        font-weight: 600;
        color: #2c3e50;
        font-size: 16px;
    }
    .client-search {
        width: 100%;
        border-radius: 6px;
        border: 1px solid #ddd;
        padding: 8px 12px;
        font-size: 13px;
    }
    .client-list {
        flex: 1;
        overflow-y: auto;
    }
    .client-item {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background 0.15s;
    }
    .client-item:hover { background: #f8f9fa; }
    .client-item.selected {
        background: #eef5fb;
        border-left: 3px solid #3498db;
    }
    .client-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: #3498db; color: #fff; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; flex-shrink: 0;
    }
    .client-info { flex: 1; min-width: 0; }
    .client-name { font-weight: 600; font-size: 14px; color: #2c3e50; }
    .client-preview {
        font-size: 12px; color: #999; margin-top: 2px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .client-badges { display: flex; gap: 4px; flex-shrink: 0; }
    .badge-unread {
        background: #3498db; color: #fff; border-radius: 10px;
        padding: 1px 7px; font-size: 11px; font-weight: 700;
    }
    .badge-urgent {
        background: #e74c3c; color: #fff; border-radius: 10px;
        padding: 1px 7px; font-size: 11px; font-weight: 700;
    }
    .no-clients {
        text-align: center; padding: 40px 15px; color: #aaa;
    }

    /* Center panel — thread */
    .thread-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f4f6f9;
    }
    .thread-header {
        padding: 15px 20px;
        background: #fff;
        border-bottom: 1px solid #e0e0e0;
    }
    .thread-header h4 {
        margin: 0; font-weight: 600; color: #2c3e50; font-size: 16px;
    }
    .thread-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }
    .thread-placeholder {
        display: flex; align-items: center; justify-content: center;
        height: 100%; color: #aaa; font-size: 16px;
    }
    .thread-placeholder i { font-size: 40px; margin-right: 15px; }

    .chat-bubble {
        max-width: 70%;
        padding: 10px 14px;
        border-radius: 12px;
        margin-bottom: 12px;
        position: relative;
        word-wrap: break-word;
        white-space: pre-wrap;
    }
    .chat-bubble .bubble-sender {
        font-size: 11px; font-weight: 600; margin-bottom: 3px;
    }
    .chat-bubble .bubble-priority {
        font-size: 10px; padding: 1px 6px; border-radius: 8px; font-weight: 600;
        display: inline-block; margin-left: 6px;
    }
    .chat-bubble .bubble-body { font-size: 14px; line-height: 1.5; }
    .chat-bubble .bubble-meta {
        font-size: 11px; margin-top: 5px; opacity: 0.7;
    }

    .bubble-staff {
        background: #3498db; color: #fff;
        margin-left: auto;
        border-bottom-right-radius: 4px;
    }
    .bubble-staff .bubble-sender { color: rgba(255,255,255,0.8); }
    .bubble-staff .bubble-priority { background: rgba(255,255,255,0.2); color: #fff; }

    .bubble-family {
        background: #fff; color: #333;
        margin-right: auto;
        border-bottom-left-radius: 4px;
        border: 1px solid #e0e0e0;
    }
    .bubble-family .bubble-sender { color: #888; }
    .bubble-family .bubble-priority.bp-high { background: #fde8e8; color: #c0392b; }
    .bubble-family .bubble-priority.bp-normal { background: #e8f0fd; color: #2980b9; }
    .bubble-family .bubble-priority.bp-low { background: #eee; color: #777; }

    .bubble-subject {
        font-size: 12px; font-weight: 600; margin-bottom: 4px; opacity: 0.85;
    }

    .reply-box {
        padding: 12px 20px;
        background: #fff;
        border-top: 1px solid #e0e0e0;
        display: none;
    }
    .reply-box .reply-input-wrapper {
        display: flex; gap: 10px; align-items: flex-end;
    }
    .reply-box textarea {
        flex: 1; border-radius: 8px; border: 1px solid #ddd;
        padding: 10px 12px; font-size: 14px; resize: none;
        min-height: 42px; max-height: 120px;
    }
    .reply-box .btn-send-reply {
        border-radius: 8px; padding: 10px 20px; font-weight: 600;
    }

    /* Right panel — stats */
    .stats-panel {
        width: 220px;
        min-width: 220px;
        background: #fff;
        border-left: 1px solid #e0e0e0;
        padding: 20px 15px;
        overflow-y: auto;
    }
    .stats-panel h5 {
        font-weight: 600; color: #2c3e50; margin: 0 0 15px; font-size: 15px;
    }
    .stat-block {
        margin-bottom: 20px;
    }
    .stat-block .stat-title {
        font-size: 12px; color: #999; text-transform: uppercase; margin-bottom: 8px; font-weight: 600;
    }
    .stat-block .stat-number {
        font-size: 24px; font-weight: 700; color: #2c3e50;
    }
    .stat-block .stat-sub { font-size: 12px; color: #aaa; }
    .priority-bar {
        display: flex; align-items: center; gap: 8px; margin-bottom: 6px;
    }
    .priority-bar .p-label { font-size: 12px; color: #555; width: 60px; }
    .priority-bar .p-bar {
        flex: 1; height: 6px; background: #eee; border-radius: 3px; overflow: hidden;
    }
    .priority-bar .p-fill { height: 100%; border-radius: 3px; }
    .priority-bar .p-count { font-size: 12px; color: #888; width: 25px; text-align: right; }
</style>

<div class="comms-container">
    <!-- Left panel: Client list -->
    <div class="client-panel">
        <div class="client-panel-header">
            <h4><i class="fa fa-comments"></i> Client Comms Hub</h4>
            <input type="text" class="client-search" id="client-search" placeholder="Search clients...">
        </div>
        <div class="client-list" id="client-list">
            @if(isset($clients_with_messages) && $clients_with_messages->count() > 0)
                @foreach($clients_with_messages as $client)
                <div class="client-item" data-client-id="{{ $client->id }}" data-client-name="{{ $client->name }}">
                    <div class="client-avatar">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
                    <div class="client-info">
                        <div class="client-name">{{ $client->name }}</div>
                        <div class="client-preview">{{ $client->last_message }}</div>
                    </div>
                    <div class="client-badges">
                        @if($client->urgent_count > 0)
                            <span class="badge-urgent">{{ $client->urgent_count }}</span>
                        @endif
                        @if($client->unread_count > 0)
                            <span class="badge-unread">{{ $client->unread_count }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="no-clients">
                    <i class="fa fa-inbox" style="font-size:32px; margin-bottom:10px; display:block"></i>
                    No portal messages yet
                </div>
            @endif
        </div>
    </div>

    <!-- Center panel: Message thread -->
    <div class="thread-panel">
        <div class="thread-header" id="thread-header" style="display:none">
            <h4 id="thread-client-name"></h4>
        </div>
        <div class="thread-messages" id="thread-messages">
            <div class="thread-placeholder" id="thread-placeholder">
                <i class="fa fa-comments-o"></i>
                <span>Select a client from the list to view messages</span>
            </div>
        </div>
        <div class="reply-box" id="reply-box">
            <div class="reply-input-wrapper">
                <textarea id="reply-input" placeholder="Type a reply..." rows="1"></textarea>
                <button class="btn btn-primary btn-send-reply" id="btn-send-reply">
                    <i class="fa fa-paper-plane"></i> Send
                </button>
            </div>
        </div>
    </div>

    <!-- Right panel: Stats -->
    <div class="stats-panel" id="stats-panel">
        <h5><i class="fa fa-bar-chart"></i> Quick Stats</h5>
        <div class="stat-block">
            <div class="stat-title">Unread Messages</div>
            <div class="stat-number" id="stat-unread">
                {{ isset($clients_with_messages) ? $clients_with_messages->sum('unread_count') : 0 }}
            </div>
        </div>
        <div class="stat-block">
            <div class="stat-title">Total Conversations</div>
            <div class="stat-number" id="stat-total-clients">
                {{ isset($clients_with_messages) ? $clients_with_messages->count() : 0 }}
            </div>
        </div>
        <div class="stat-block">
            <div class="stat-title">Total Messages</div>
            <div class="stat-number" id="stat-total-messages">
                {{ isset($clients_with_messages) ? $clients_with_messages->sum('total_count') : 0 }}
            </div>
        </div>
    </div>
</div>

<script src="{{ url('public/js/roster/messaging_center.js') }}"></script>
@endsection
