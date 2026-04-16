<?php

namespace App\Services;

use App\Models\HandoverLogBook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HandoverService
{
    /**
     * Get paginated handover log entries for a home, with optional search.
     */
    public function list(int $homeId, ?string $search = null, ?string $searchType = null, ?string $searchDate = null, int $perPage = 50): LengthAwarePaginator|array
    {
        $query = HandoverLogBook::forHome($homeId)
            ->active()
            ->select(
                'handover_log_book.*',
                'u.name as staff_name',
                'au.name as assigned_staff_name'
            )
            ->leftJoin('user as u', 'u.id', '=', 'handover_log_book.user_id')
            ->leftJoin('user as au', 'au.id', '=', 'handover_log_book.assigned_staff_user_id')
            ->orderBy('handover_log_book.id', 'desc')
            ->orderBy('handover_log_book.date', 'desc');

        if ($search && $searchType === 'log_title') {
            $query->where('handover_log_book.title', 'like', '%' . $search . '%');
        } elseif ($searchDate) {
            $startDate = date('Y-m-d 00:00:00', strtotime($searchDate));
            $endDate = date('Y-m-d 00:00:00', strtotime('+1 day', strtotime($searchDate)));
            $query->where('handover_log_book.date', '>', $startDate)
                  ->where('handover_log_book.date', '<', $endDate);
        }

        // Search returns all results (no pagination), normal returns paginated
        if ($search || $searchDate) {
            return $query->get()->all();
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a single handover record by ID, scoped to home.
     */
    public function getById(int $homeId, int $id): ?HandoverLogBook
    {
        return HandoverLogBook::forHome($homeId)
            ->active()
            ->where('handover_log_book.id', $id)
            ->first();
    }

    /**
     * Update details and notes on a handover record.
     */
    public function update(int $homeId, int $id, array $data): bool
    {
        $record = $this->getById($homeId, $id);
        if (!$record) {
            return false;
        }

        $record->details = $data['details'] ?? $record->details;
        $record->notes = $data['notes'] ?? $record->notes;
        $saved = $record->save();

        if ($saved) {
            Log::info('Handover log updated', [
                'action' => 'handover_update',
                'record_id' => $id,
                'home_id' => $homeId,
                'user_id' => Auth::id(),
            ]);
        }

        return $saved;
    }

    /**
     * Create a handover record from a logbook entry for a staff member.
     * Returns: ['success' => bool, 'message' => string]
     */
    public function createFromLogBook(int $homeId, array $data): array
    {
        $logBookId = $data['log_id'];
        $staffUserId = $data['staff_user_id'];
        $serviceUserId = $data['service_user_id'];

        // Check source logbook entry exists AND belongs to the same home (IDOR prevention)
        $logBook = \App\LogBook::where('id', $logBookId)
            ->where('home_id', $homeId)
            ->where('is_deleted', '0')
            ->first();

        if (!$logBook) {
            return ['success' => false, 'message' => 'Log book entry not found.'];
        }

        // Prevent duplicate handover to same staff for same logbook entry
        $existing = HandoverLogBook::forHome($homeId)
            ->where('log_book_id', $logBookId)
            ->where('assigned_staff_user_id', $staffUserId)
            ->where('service_user_id', $serviceUserId)
            ->first();

        if ($existing) {
            return ['success' => false, 'message' => 'Already handed over to this staff member.'];
        }

        $record = HandoverLogBook::create([
            'log_book_id' => $logBookId,
            'assigned_staff_user_id' => $staffUserId,
            'service_user_id' => $serviceUserId,
            'user_id' => Auth::id(),
            'home_id' => $homeId,
            'title' => $logBook->title ?? '',
            'details' => $logBook->details ?? '',
            'date' => $logBook->date ?? now(),
        ]);

        Log::info('Handover log created', [
            'action' => 'handover_create',
            'record_id' => $record->id,
            'home_id' => $homeId,
            'user_id' => Auth::id(),
            'assigned_staff_user_id' => $staffUserId,
            'log_book_id' => $logBookId,
        ]);

        return ['success' => true, 'message' => 'Handover created successfully.'];
    }

    /**
     * Mark a handover as acknowledged by incoming staff.
     */
    public function acknowledge(int $homeId, int $id, int $staffId): bool
    {
        $record = $this->getById($homeId, $id);
        if (!$record) {
            return false;
        }

        if ($record->acknowledged_at) {
            return true; // Already acknowledged
        }

        $record->acknowledged_at = now();
        $record->acknowledged_by = $staffId;
        $saved = $record->save();

        if ($saved) {
            Log::info('Handover log acknowledged', [
                'action' => 'handover_acknowledge',
                'record_id' => $id,
                'home_id' => $homeId,
                'acknowledged_by' => $staffId,
            ]);
        }

        return $saved;
    }

    /**
     * Soft-delete a handover record.
     */
    public function softDelete(int $homeId, int $id): bool
    {
        $record = $this->getById($homeId, $id);
        if (!$record) {
            return false;
        }

        $record->is_deleted = 1;
        $saved = $record->save();

        if ($saved) {
            Log::info('Handover log deleted', [
                'action' => 'handover_delete',
                'record_id' => $id,
                'home_id' => $homeId,
                'user_id' => Auth::id(),
                'record_snapshot' => $record->toArray(),
            ]);
        }

        return $saved;
    }
}
