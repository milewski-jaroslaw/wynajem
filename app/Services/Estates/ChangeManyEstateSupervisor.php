<?php

declare(strict_types=1);

namespace App\Services\Estates;

use App\Models\Estate;
use App\Models\User;

class ChangeManyEstateSupervisor
{
    public function perform(int $userId, array $estateIds): void
    {
        Estate::query()
            ->whereIn('id', $estateIds)
            ->update(['supervisor_user_id' => $userId]);
    }
}
