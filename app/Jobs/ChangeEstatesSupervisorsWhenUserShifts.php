<?php

namespace App\Jobs;

use App\Models\UserShift;
use App\Repositories\UserShiftRepository;
use App\Services\Estates\ChangeManyEstateSupervisor;
use App\Services\Estates\SubstituteEstateSupervisor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChangeEstatesSupervisorsWhenUserShifts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly UserShiftRepository        $userShiftRepository,
        private readonly SubstituteEstateSupervisor $substituteEstateSupervisor,
        private readonly ChangeManyEstateSupervisor $changeManyEstateSupervisor,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all users who are starting their break today
        $usersStartsBreakToday = $this->userShiftRepository->startsToday()
            ->select(['id', 'user_id', 'substitute_user_id'])->get();

        $usersStartsBreakToday->each(function (UserShift $userShift) {
            // Change estate supervisors whose break is starting
            $this->substituteEstateSupervisor->perform($userShift);
        });

        // Get all users whose break ended yesterday
        $usersEndedBreakYesterday = $this->userShiftRepository->endedYesterday()
            ->select(['id', 'user_id', 'temp_changes'])->get();

        $usersEndedBreakYesterday->each(function (UserShift $userShift) {
            // Reset estate supervisor whose break ended yesterday
            $this->changeManyEstateSupervisor->perform($userShift->user_id, (array)$userShift->temp_changes);
        });
    }
}
