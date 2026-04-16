<?php

namespace App\Http\Controllers\frontEnd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\HandoverService;
use Auth;

class HandoverController extends Controller
{
    protected HandoverService $service;

    public function __construct()
    {
        $this->service = new HandoverService();
    }

    /**
     * Helper: extract first home_id for multi-home admins.
     */
    private function getHomeId(): int
    {
        return (int) explode(',', Auth::user()->home_id)[0];
    }

    /**
     * GET/POST /handover/daily/log
     * List handover entries with search/pagination. Returns HTML partial for AJAX.
     */
    public function index(Request $request)
    {
        if (!$request->isMethod('post')) {
            return '';
        }

        $homeId = $this->getHomeId();

        $search = $request->query('search');
        $searchType = $request->query('log_book_search_type');
        $searchDate = $request->query('log_book_date_search');

        $records = $this->service->list($homeId, $search, $searchType, $searchDate);

        // Build HTML response (matching existing view expectations)
        $html = '';
        $pagination = '';
        $isCollection = is_array($records);
        $items = $isCollection ? $records : $records->items();

        if (!$isCollection && $records->links() != '') {
            $pagination .= '</div><div class="log_records_paginate m-l-15 position-botm">';
            $pagination .= $records->links();
            $pagination .= '</div>';
        }

        if (!empty($items)) {
            $preDate = date('Y-m-d', strtotime($items[0]->date));

            foreach ($items as $key => $value) {
                $recordTime = date('h:i a', strtotime($value->date));
                $createdTime = date('h:i a', strtotime($value->created_at));
                $recTime = $recordTime . ' (' . $createdTime . ')';
                $recordDate = date('Y-m-d', strtotime($value->date));

                if ($recordDate != $preDate) {
                    $preDate = $recordDate;
                    $html .= '</div>
                    <div class="hndovr-daily-rcd-head">
                        <div class="col-md-12 col-sm-12 col-xs-12 cog-panel p-0 r-p-15 record_row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <a class="date-tab">
                                    <span class="pull-left">'
                                        . e(date('d F Y', strtotime($recordDate))) .
                                    '</span>
                                    <i class="fa fa-angle-right pull-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hndovr-daily-rcd-content" style="display: none;">';
                }

                // Acknowledgment status badge
                $ackBadge = '';
                if ($value->acknowledged_at) {
                    $ackBadge = '<span class="label label-success" style="margin-left:8px;">Acknowledged</span>';
                } else {
                    $ackBadge = '<span class="label label-warning" style="margin-left:8px;">Pending</span>'
                        . ' <button class="btn btn-xs btn-info acknowledge-handover-btn" data-handover-id="' . (int)$value->id . '">Acknowledge</button>';
                }

                $html .= '
                    <div class="col-md-12 col-sm-12 col-xs-12 cog-panel p-0 r-p-15 record_row">
                        <div class="form-group col-md-11 col-sm-11 col-xs-12 r-p-0 pull-center">
                            <div class="input-group popovr">
                                <input type="text" name="edit_su_record_desc[]" class="form-control cus-control edit_record_desc_' . (int)$value->id . ' edit_rcrd" disabled value="' . e(ucfirst($value->title)) . ' | ' . e($recTime) . '" />
                                <div class="input-plus color-green"> <i class="fa fa-plus"></i> </div>
                                <input type="hidden" name="edit_su_record_id[]" value="' . (int)$value->id . '" disabled="disabled" class="edit_record_id_' . (int)$value->id . '" />
                            </div>
                            ' . $ackBadge . '
                        </div>
                        <form id="edit-hndovr-daily-logged-form' . (int)$value->id . '" method="post">
                        <div class="input-plusbox form-group col-xs-11 p-0 detail">
                            <label class="cus-label color-themecolor"> Details: </label>
                            <div class="cus-input p-r-10">
                                <div class="input-group">
                                    <textarea rows="5" name="detail" class="form-control tick_text txtarea edit_detail_' . (int)$value->id . ' edit_rcrd">' . e($value->details) . '</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="input-plusbox form-group col-xs-11 p-0 detail">
                            <label class="cus-label color-themecolor"> Notes: </label>
                            <div class="cus-input p-r-10">
                                <div class="input-group">
                                    <textarea rows="5" name="notes" class="form-control tick_text txtarea edit_detail_' . (int)$value->id . ' edit_rcrd">' . e($value->notes) . '</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="input-plusbox form-group col-xs-11 p-0 detail">
                            <label class="cus-label color-themecolor"> Staff created: </label>
                            <div class="cus-input p-r-10">
                                <div class="input-group">
                                    <input type="text" value="' . e(ucfirst($value->staff_name)) . '" disabled="" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="input-plusbox form-group col-xs-11 p-0 detail">
                            <label class="cus-label color-themecolor">Handover Staff Name: </label>
                            <div class="cus-input p-r-10">
                                <div class="input-group">
                                    <input type="text" value="' . e(ucfirst($value->assigned_staff_name)) . '" disabled="" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="input-plusbox form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 detail pull-right">
                            <div class="cus-input p-r-10 pull-right">
                                <div class="input-group pull-right">
                                    <button class="btn btn-default pull-right sbmt_btn" handover_log_book_id="' . (int)$value->id . '">Submit</button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                        <input type="hidden" name="handover_log_book_id" value="' . (int)$value->id . '">
                        </form>
                    </div>';
            }

            $html .= $pagination;
        }

        return response($html);
    }

    /**
     * POST /handover/daily/log/edit
     * Update details and notes on a handover record.
     */
    public function update(Request $request)
    {
        if (!$request->isMethod('post')) {
            return response()->json(['success' => false], 405);
        }

        $request->validate([
            'handover_log_book_id' => 'required|integer',
            'detail' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:5000',
        ]);

        $homeId = $this->getHomeId();
        $id = (int) $request->handover_log_book_id;

        // IDOR check: verify record belongs to this home
        $record = $this->service->getById($homeId, $id);
        if (!$record) {
            return response("0");
        }

        $success = $this->service->update($homeId, $id, [
            'details' => $request->detail,
            'notes' => $request->notes,
        ]);

        // Return "1" or "2" to match existing view JS expectations
        return response($success ? "1" : "2");
    }

    /**
     * POST /handover/service/log
     * Create a handover from a logbook entry to a staff member.
     */
    public function handoverToStaff(Request $request)
    {
        if (!$request->isMethod('post')) {
            return response()->json(['success' => false], 405);
        }

        $request->validate([
            'log_id' => 'required|integer',
            'staff_user_id' => 'required|integer',
            'servc_use_id' => 'required|integer',
        ]);

        $homeId = $this->getHomeId();

        $result = $this->service->createFromLogBook($homeId, [
            'log_id' => (int) $request->log_id,
            'staff_user_id' => (int) $request->staff_user_id,
            'service_user_id' => (int) $request->servc_use_id,
        ]);

        if ($result['success']) {
            return response("1");
        } elseif ($result['message'] === 'Already handed over to this staff member.') {
            return response("already");
        } else {
            return response("0");
        }
    }

    /**
     * POST /handover/acknowledge
     * Mark a handover as acknowledged by the current user.
     */
    public function acknowledge(Request $request)
    {
        if (!$request->isMethod('post')) {
            return response()->json(['success' => false], 405);
        }

        $request->validate([
            'handover_log_book_id' => 'required|integer',
        ]);

        $homeId = $this->getHomeId();
        $id = (int) $request->handover_log_book_id;

        $success = $this->service->acknowledge($homeId, $id, Auth::id());

        return response($success ? "1" : "0");
    }
}
