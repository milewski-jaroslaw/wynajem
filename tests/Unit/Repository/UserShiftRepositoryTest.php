<?php

namespace Tests\Unit\Repository;

use App\Models\UserShift;
use App\Repositories\UserShiftRepository;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserShiftRepositoryTest extends TestCase
{
    private UserShiftRepository $userShiftRepository;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('users_shifts')->truncate();
        DB::table('estates')->truncate();

        $this->userShiftRepository = new UserShiftRepository();
    }

    /** @test */
    public function it_can_get_all_shifts_starting_today(): void
    {
        UserShift::factory()->create(['date_from' => now()->format('Y-m-d')]);
        UserShift::factory()->create(['date_from' => now()->subDays(2)->format('Y-m-d')]);

        $result = $this->userShiftRepository->startsToday()->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->date_from->isToday());
    }

    /** @test */
    public function it_can_get_all_shifts_ended_yesterday(): void
    {
        UserShift::factory()->create(['date_to' => now()->subDays()->format('Y-m-d')]);
        UserShift::factory()->create(['date_to' => now()->format('Y-m-d')]);

        $result = $this->userShiftRepository->endedYesterday()->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->date_to->isYesterday());
    }
}

