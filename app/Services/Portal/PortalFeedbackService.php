<?php

namespace App\Services\Portal;

use App\Models\ClientPortalAccess;
use App\Models\ClientPortalFeedback;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PortalFeedbackService
{
    public function getFeedbackForPortal(ClientPortalAccess $access): Collection
    {
        return ClientPortalFeedback::forHome($access->home_id)
            ->forClient($access->client_id)
            ->where('submitted_by_id', $access->id)
            ->active()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function submitFeedback(ClientPortalAccess $access, array $data): ClientPortalFeedback
    {
        $priority = 'medium';
        if (($data['feedback_type'] ?? '') === 'complaint') {
            $priority = 'high';
        }

        $feedback = ClientPortalFeedback::create([
            'home_id' => $access->home_id,
            'client_id' => $access->client_id,
            'submitted_by' => $access->full_name,
            'submitted_by_id' => $access->id,
            'relationship' => $data['relationship'] ?? 'family',
            'feedback_type' => $data['feedback_type'] ?? 'general',
            'category' => $data['category'] ?? 'care_quality',
            'rating' => (int) ($data['rating'] ?? 5),
            'subject' => $data['subject'],
            'comments' => $data['comments'],
            'priority' => $priority,
            'status' => 'new',
            'is_anonymous' => !empty($data['is_anonymous']) ? 1 : 0,
            'wants_callback' => !empty($data['wants_callback']) ? 1 : 0,
            'contact_email' => $data['contact_email'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'is_deleted' => 0,
        ]);

        Log::info('Portal feedback submitted', [
            'feedback_id' => $feedback->id,
            'client_id' => $access->client_id,
            'home_id' => $access->home_id,
            'type' => $feedback->feedback_type,
        ]);

        return $feedback;
    }

    public function getFeedbackStats(ClientPortalAccess $access): array
    {
        $feedback = ClientPortalFeedback::forHome($access->home_id)
            ->forClient($access->client_id)
            ->where('submitted_by_id', $access->id)
            ->active();

        $total = (clone $feedback)->count();
        $withResponses = (clone $feedback)->whereNotNull('response')->count();
        $pending = (clone $feedback)->whereIn('status', ['new', 'acknowledged'])->count();

        return [
            'total' => $total,
            'with_responses' => $withResponses,
            'pending' => $pending,
        ];
    }

    // --- Admin methods ---

    public function getAllFeedbackForHome(int $homeId, ?string $status = null, ?string $type = null): Collection
    {
        $query = ClientPortalFeedback::forHome($homeId)
            ->active()
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->byStatus($status);
        }

        if ($type) {
            $query->where('feedback_type', $type);
        }

        $feedback = $query->get();

        $clientIds = $feedback->pluck('client_id')->unique();
        $clients = \App\ServiceUser::whereIn('id', $clientIds)->pluck('name', 'id');

        $feedback->each(function ($item) use ($clients) {
            $item->client_name = $clients[$item->client_id] ?? 'Unknown';
            if ($item->is_anonymous) {
                $item->submitted_by = 'Anonymous';
            }
        });

        return $feedback;
    }

    public function getAdminStats(int $homeId): array
    {
        $base = ClientPortalFeedback::forHome($homeId)->active();

        $total = (clone $base)->count();
        $new = (clone $base)->byStatus('new')->count();
        $compliments = (clone $base)->where('feedback_type', 'compliment')->count();
        $avgRating = (clone $base)->avg('rating');

        return [
            'total' => $total,
            'new' => $new,
            'compliments' => $compliments,
            'avg_rating' => $avgRating ? round($avgRating, 1) : 0,
        ];
    }

    public function acknowledgeFeedback(int $feedbackId, int $homeId, int $staffId): bool
    {
        $feedback = ClientPortalFeedback::where('id', $feedbackId)
            ->forHome($homeId)
            ->active()
            ->first();

        if (!$feedback || $feedback->status !== 'new') {
            return false;
        }

        $feedback->update([
            'status' => 'acknowledged',
            'acknowledged_by' => $staffId,
            'acknowledged_date' => now(),
        ]);

        return true;
    }

    public function respondToFeedback(int $feedbackId, int $homeId, int $staffId, string $staffName, string $response): bool
    {
        $feedback = ClientPortalFeedback::where('id', $feedbackId)
            ->forHome($homeId)
            ->active()
            ->first();

        if (!$feedback || in_array($feedback->status, ['closed'])) {
            return false;
        }

        $feedback->update([
            'status' => 'resolved',
            'response' => $response,
            'response_date' => now(),
            'responded_by' => $staffId,
            'responded_by_name' => $staffName,
        ]);

        return true;
    }

    public function closeFeedback(int $feedbackId, int $homeId): bool
    {
        $feedback = ClientPortalFeedback::where('id', $feedbackId)
            ->forHome($homeId)
            ->active()
            ->first();

        if (!$feedback || $feedback->status !== 'resolved') {
            return false;
        }

        $feedback->update(['status' => 'closed']);

        return true;
    }
}
