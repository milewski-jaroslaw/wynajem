<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Estate;
use Illuminate\Database\Eloquent\Builder;

class EstateRepository
{
    private function query(): Builder
    {
        return Estate::query();
    }

    public function getAllByUserId(int $userId): Builder
    {
        return $this->query()->where('supervisor_user_id', $userId);
    }
}
