<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UserShift;
use Illuminate\Database\Eloquent\Builder;

class UserShiftRepository
{
    private function query(): Builder
    {
        return UserShift::query();
    }

    /**
     * Get entries about changes that start today
     *
     * @return Builder
     */
    public function startsToday(): Builder
    {
        return $this->query()->where('date_from', now()->format('Y-m-d'));
    }

    /**
     *
     */
    public function endedYesterday(): Builder
    {
        $yesterday = now()->subDays();

        return $this->query()->where('date_to', $yesterday->format('Y-m-d'));
    }
}
