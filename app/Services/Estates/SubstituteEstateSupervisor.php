<?php

declare(strict_types=1);

namespace App\Services\Estates;

use App\Models\Estate;
use App\Models\UserShift;
use App\Repositories\EstateRepository;
use DB;

class SubstituteEstateSupervisor
{
    public function __construct(
        private readonly EstateRepository $estateRepository,
    )
    {
    }

    public function perform(UserShift $userShift): void
    {
        $userEstateIds = $this->estateRepository->getAllByUserId($userShift->user_id)
            ->pluck('id')->toArray();

        DB::beginTransaction();

        Estate::query()
            ->whereIn('id', $userEstateIds)
            ->update(['supervisor_user_id' => $userShift->substitute_user_id]);

        $userShift->update(['temp_changes' => $userEstateIds]);

        DB::commit();
    }
}
