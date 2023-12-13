<?php

namespace Tests\Unit\Services\Estates;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Services\Estates\SubstituteEstateSupervisor;
use App\Models\Estate;
use App\Models\User;
use App\Models\UserShift;
use App\Repositories\EstateRepository;

class SubstituteEstateSupervisorTest extends TestCase
{
    private SubstituteEstateSupervisor $substituteEstateSupervisor;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('users_shifts')->truncate();
        DB::table('estates')->truncate();

        $estateRepository                 = new EstateRepository();
        $this->substituteEstateSupervisor = new SubstituteEstateSupervisor(
            $estateRepository
        );
    }

    /** @test */
    public function it_can_substitute_estate_supervisor(): void
    {
        $user = User::factory()->create();
        $substituteUser = User::factory()->create();
        $userShift = UserShift::factory()->create([
            'user_id' => $user->getKey(),
            'substitute_user_id' => $substituteUser->getKey(),
        ]);

        Estate::factory()->create(['supervisor_user_id' => $user->getKey()]);
        Estate::factory()->create(['supervisor_user_id' => $user->getKey()]);
        Estate::factory()->create(['supervisor_user_id' => $user->getKey()]);
        Estate::factory()->create(['supervisor_user_id' => $substituteUser->getKey()]);
        Estate::factory()->create(['supervisor_user_id' => $substituteUser->getKey()]);
        Estate::factory()->create(['supervisor_user_id' => $substituteUser->getKey()]);
        Estate::factory()->count(10)->create();

        $this->substituteEstateSupervisor->perform($userShift);

        $estates = Estate::query()->where('supervisor_user_id', $user->getKey())->get();
        $this->assertCount(0, $estates);

        $estates = Estate::query()->where('supervisor_user_id', $substituteUser->getKey())->get();
        $this->assertCount(6, $estates);
    }
}
