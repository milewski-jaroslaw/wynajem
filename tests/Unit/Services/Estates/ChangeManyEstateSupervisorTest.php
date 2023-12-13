<?php

namespace Tests\Unit\Services\Estates;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Services\Estates\ChangeManyEstateSupervisor;
use App\Models\Estate;
use App\Models\User;

class ChangeManyEstateSupervisorTest extends TestCase
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
    public function it_can_change_supervisor_for_many_estates(): void
    {
        $estateIds = Estate::factory()->count(3)->create()->pluck('id')->toArray();
        Estate::factory()->count(5)->create();
        $user = User::factory()->create();

        $this->changeManyEstateSupervisor->perform($user->getKey(), $estateIds);

        $this->assertDatabaseHas('estates', [
            'id' => $estateIds[0],
            'supervisor_user_id' => $user->getKey(),
        ]);

        $this->assertDatabaseHas('estates', [
            'id' => $estateIds[1],
            'supervisor_user_id' => $user->getKey(),
        ]);

        $this->assertDatabaseHas('estates', [
            'id' => $estateIds[2],
            'supervisor_user_id' => $user->getKey(),
        ]);

        $this->assertCount(3, $user->estates()->get());
    }
}
