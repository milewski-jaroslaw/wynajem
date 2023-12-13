<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ChangeEstatesSupervisorsWhenUserShifts;
use App\Models\Estate;
use App\Models\User;
use App\Models\UserShift;
use App\Repositories\EstateRepository;
use App\Repositories\UserShiftRepository;
use App\Services\Estates\ChangeManyEstateSupervisor;
use App\Services\Estates\SubstituteEstateSupervisor;
use Illuminate\Support\Facades\DB;
use Queue;
use Tests\TestCase;

class ChangeEstatesSupervisorsWhenUserShiftsTest extends TestCase
{
    private ChangeManyEstateSupervisor $changeManyEstateSupervisor;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('users_shifts')->truncate();
        DB::table('estates')->truncate();

        $this->changeManyEstateSupervisor = new ChangeManyEstateSupervisor();
    }

    /** @test */
    public function it_dispatches_correctly(): void
    {
        Queue::fake();

        $this->dispatchChangeEstatesSupervisorsWhenUserShifts();

        Queue::assertPushed(ChangeEstatesSupervisorsWhenUserShifts::class);
    }

    private function dispatchChangeEstatesSupervisorsWhenUserShifts(): void
    {
        changeEstatesSupervisorsWhenUserShifts::dispatch(
            new UserShiftRepository(),
            new SubstituteEstateSupervisor(
                new EstateRepository(),
            ),
            $this->changeManyEstateSupervisor
        );
    }

    /**
     * @test
     */
    public function it_properly_change_estate_supervisor_whom_starts_or_ends_break(): void
    {
        $estates = $this->addUserWith5EstatesWhoStartsBreakToday();
        $userIdWhoStartsBreakToday = $estates->pluck('supervisor_user_id')->toArray();
        $this->add10UsersWithEstateAndRandomBreakTime();
        $userIdWhoEndsBreakToday = $this->addUserWith2EstatesWhoEndsBreakToday();

        $this->dispatchChangeEstatesSupervisorsWhenUserShifts();

        // Check whether the user on break no longer has any estate
        $estatesSupervisordByUserOnBreak =
            Estate::query()->where('supervisor_user_id', $userIdWhoStartsBreakToday)->count();
        $this->assertEquals(0, $estatesSupervisordByUserOnBreak);

        // Check if the changed estates have been saved in shift temp changes data
        $userShift = UserShift::query()->whereDate('date_from', now())->where('user_id', $userIdWhoStartsBreakToday)
            ->select('temp_changes')->first()->toArray();
        $this->assertEquals($estates->pluck('id')->toArray(), $userShift['temp_changes']);

        // Check whether previously transferred estates have been restored to the user
        $userShiftTempChanges = UserShift::query()->whereDate('date_to', now()->subDay())
            ->where('user_id', $userIdWhoEndsBreakToday)
            ->select('temp_changes')->first()->toArray();
        $userEstateIds = Estate::query()->where('supervisor_user_id', $userIdWhoEndsBreakToday)->get()
            ->pluck('id')->toArray();
        $this->assertEquals($userShiftTempChanges['temp_changes'], $userEstateIds);
        $this->assertCount(2, $userEstateIds);
    }

    private function addUserWith5EstatesWhoStartsBreakToday()
    {
        $userId = UserShift::factory()->create([
            'date_from' => now()->format('Y-m-d'),
            'date_to' => now()->addDays(7)->format('Y-m-d'),
        ])->user_id;

        return Estate::factory()->count(5)->create([
            'supervisor_user_id' => $userId
        ]);
    }

    private function add10UsersWithEstateAndRandomBreakTime(): void
    {
        UserShift::factory()->count(10)->create();
    }

    private function addUserWith2EstatesWhoEndsBreakToday(): int
    {
        $substituteUser = User::factory()->create();
        $estates = Estate::factory()->count(2)->create([
            'supervisor_user_id' => $substituteUser->getKey(),
        ]);
        $estateSupervisorBeforeChange = User::factory()->create();

        return UserShift::factory()->create([
            'date_from' => now()->subDays(5)->format('Y-m-d'),
            'date_to' => now()->subDay()->format('Y-m-d'),
            'user_id' => $estateSupervisorBeforeChange->getKey(),
            'substitute_user_id' => $substituteUser->getKey(),
            'temp_changes' => $estates->pluck('id')->toArray(),
        ])->toArray()['user_id'];
    }
}
