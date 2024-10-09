<?php

namespace App\Observers;

use App\Models\User;
use App\Services\UserBatchUpdateService;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    protected $batchUpdateService;

    public function __construct(UserBatchUpdateService $batchUpdateService)
    {
        $this->batchUpdateService = $batchUpdateService;
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        // Cache the user for batch processing
        $this->cacheUpdatedUser($user);

        // Every 5 minutes, process the batch of updates
        if (Cache::has('user_batch_processing')) {
            $this->processBatchIfNeeded();
        }
    }

    /**
     * Cache the updated user for future batch processing.
     *
     * @param User $user
     * @return void
     */
    protected function cacheUpdatedUser(User $user): void
    {
        $cachedUsers = Cache::get('user_batch_updates', collect());

        $cachedUsers->push($user);

        Cache::put('user_batch_updates', $cachedUsers, now()->addMinutes(10));
    }

    /**
     * Process the batch updates if necessary.
     */
    protected function processBatchIfNeeded(): void
    {
        $users = Cache::pull('user_batch_updates', collect());

        if ($users->isNotEmpty()) {
            $this->batchUpdateService->sendBatchUpdate($users);
        }
    }
}
