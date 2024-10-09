<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UserBatchUpdateService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.api.batch_endpoint');
        $this->apiKey = config('services.api.key');
    }

    /**
     * Send batch updates to the third-party API.
     *
     * @param Collection $users
     * @return void
     */
    public function sendBatchUpdate(Collection $users): void
    {
        // Group users in batches of 1000 (API limit per batch request)
        $batches = $users->chunk(1000);

        foreach ($batches as $batch) {
            $batchPayload = $this->prepareBatchPayload($batch);

            // Send the request to the API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])->post($this->apiUrl, $batchPayload);

            if ($response->failed()) {
                // Log the failure or handle error response
                Log::error('Batch API update failed', ['response' => $response->body()]);
            }
        }
    }

    /**
     * Prepare the payload for the batch request.
     *
     * @param Collection $users
     * @return array
     */
    protected function prepareBatchPayload(Collection $users): array
    {
        $subscribers = $users->map(function ($user) {
            return [
                'email' => $user->email,
                'name' => $user->name,
                'time_zone' => $user->time_zone,
            ];
        })->toArray();

        return [
            'batches' => [
                [
                    'subscribers' => $subscribers,
                ]
            ]
        ];
    }
}
